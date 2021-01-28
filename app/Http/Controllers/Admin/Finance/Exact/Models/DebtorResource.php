<?php

namespace App\Http\Controllers\Admin\Finance\Exact\Models;

use App\Http\Controllers\Admin\Finance\Exact\ExactBaseResource;
use App\Models\Admin\CRM\Address;
use App\Models\Admin\CRM\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use KJLocalization;

class DebtorResource extends ExactBaseResource
{
    public function __construct()
    {
        parent::__construct();

        $this->doConnect();
    }

    protected function doOrderBy(&$items, $field, $sort)
    {
        // Tabelnaam eraf halen, want die hebben we niet nodig in een SP
        $field = str_replace($this->baseTable.'.', '', $field);

        if (strtolower($sort) == 'asc') {
            $items = $items->sortBy($field);
        } else {
            $items = $items->sortByDesc($field);
        }
    }

    protected function allInternal(Request $request, bool $doDatatableFilter = false, array &$pagination = [], array &$sort = [], $items = null)
    {
        $query = $request->query($this->datatableQueryKey);

        $filter = null;
        if ($doDatatableFilter === true) {
            $filter = isset($query['query']['ADM_ACCOUNTANCY_FILTER_SEARCH']) ? $query['query']['ADM_ACCOUNTANCY_FILTER_SEARCH'] : null;
        }

        $items = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_DEBTOR] ?, ?, ?', [
            config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE'),
            config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.VISIT'),
            $filter
        ]));

        //Sortering
        $this->applySorting($items, $sort);

        // Pagination
        $this->applyPagination($items, $pagination);
        if ((int)$pagination['perpage'] !== -1) {
            $items = $items->slice((($pagination['page'] - 1) * $pagination['perpage']))->take($pagination['perpage']);
        } else {
            $items = $items->slice(0)->take(999999);
        }

        return $items;
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('INVOICE_ADDRESS', function($item) {
                $address = $item->INVOICE_ADDRESS_ADDRESSLINE . " " . $item->INVOICE_ADDRESS_HOUSENUMBER . "\n";
                $address .= isset($item->INVOICE_ADDRESS_ZIPCODE) ? $item->INVOICE_ADDRESS_ZIPCODE . " " : "";
                $address .= isset($item->INVOICE_ADDRESS_CITY) ? $item->INVOICE_ADDRESS_CITY . "\n" : " ";
                $address .= isset($item->INVOICE_ADDRESS_COUNTRYCODE) ? $item->INVOICE_ADDRESS_COUNTRYCODE : "";

                return new HtmlString(nl2br($address));
            })
            ->addColumn('VISIT_ADDRESS', function($item) {
                $address = $item->VISIT_ADDRESS_ADDRESSLINE . " " . $item->VISIT_ADDRESS_HOUSENUMBER . "\n";
                $address .= isset($item->VISIT_ADDRESS_ZIPCODE) ? $item->VISIT_ADDRESS_ZIPCODE . " " : "";
                $address .= isset($item->VISIT_ADDRESS_CITY) ? $item->VISIT_ADDRESS_CITY . "\n" : " ";
                $address .= isset($item->VISIT_ADDRESS_COUNTRYCODE) ? $item->VISIT_ADDRESS_COUNTRYCODE : "";

                return new HtmlString(nl2br($address));
            });
    }

    public function export(Request $request)
    {
        // Want we moeten de juiste tijden hebben
        date_default_timezone_set('Europe/Amsterdam');

        $ids = json_decode($request->get('ids'), true);
        $idString = implode(",", $ids);

        $items = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_DEBTOR] ?, ?, ?, ?', [
            config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE'),
            config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.VISIT'),
            '',
            $idString
        ]));

        foreach ($items as $item) {
            $relation = Relation::find($item->ID);

            $accounts = new \Picqer\Financials\Exact\Account($this->connection);

            try
            {
                $exactId = $relation->EXACT_DEBTOR_ID ? $relation->EXACT_DEBTOR_ID : $accounts->findID("$item->DEBTORNUMBER");

                if($exactId <> '') {
                    // Bestaand account
                    $account = $accounts->find("{" . $exactId . "}");
                } else {
                    // Nieuw account
                    $account = $accounts;
                }

                // Versturen naar Exact
                $account->Name = ($item->NAME != '' ? $item->NAME : $account->Name);
                $account->IsSales = true;
                $account->Status = 'C';
                $account->Code = $item->DEBTORNUMBER;
                $account->Email = ($item->EMAILADDRESS != '' ? $item->EMAILADDRESS : $account->Email);
                $account->Phone = ($item->PHONENUMBER != '' ? $item->PHONENUMBER : $account->Phone);
//                if ($item->STARTDATE != null) {
//                    $account->StartDate = $item->STARTDATE;
//                }
                $account->Remarks = ($item->REMARKS != '' ? $item->REMARKS : $account->Remarks);
                if (!$item->VAT_LIABLE) {
                    $account->VATNumber = $item->VAT_NUMBER;
                } else {
                    $account->VATNumber = '';
                }

                // Bezoekadres wordt standaard opgeslagen bij het account
                if (($item->VISIT_ADDRESS_ID > 0) && ($item->VISIT_RELATION_ADDRESS_ID > 0)) {
                    $address = Address::find($item->VISIT_RELATION_ADDRESS_ID);

                    $AddressLine1 = $item->VISIT_ADDRESS_ADDRESSLINE . ' ' . $item->VISIT_ADDRESS_HOUSENUMBER;
                    $account->AddressLine1 = (trim($AddressLine1) != '' ? $AddressLine1 : $account->AddressLine1);
                    $account->Postcode = ($item->VISIT_ADDRESS_ZIPCODE != '' ? $item->VISIT_ADDRESS_ZIPCODE : $account->Postcode);
                    $account->City = ($item->VISIT_ADDRESS_CITY != '' ? $item->VISIT_ADDRESS_CITY : $account->City);
                    $account->Country = ($item->VISIT_ADDRESS_COUNTRYCODE != '' ? $item->VISIT_ADDRESS_COUNTRYCODE : $account->Country);

                    $address->EXACT_ADDRESS_LASTSYNC = date('Y-m-d H:i:s');
                    $address->save();
                }

                $account->save();

                // Relatie bijwerken
                $relation->EXACT_DEBTOR_ID = $account->ID;
                $relation->EXACT_DEBTOR_ERROR = '';
                $relation->EXACT_DEBTOR_LASTSYNC = date('Y-m-d H:i:s');
                $relation->save();

                // Factuuradres
                if (($item->INVOICE_ADDRESS_ID > 0) && ($item->INVOICE_RELATION_ADDRESS_ID > 0)) {
                    $address = Address::find($item->INVOICE_RELATION_ADDRESS_ID);

                    $addresses = new \Picqer\Financials\Exact\Address($this->connection);

                    try
                    {
                        if ($address->EXACT_ADDRESS_ID <> '') {
                            $accountAddress = $addresses->find("{" . $address->EXACT_ADDRESS_ID . "}");
                        } else {
                            $accountAddress = $addresses;
                        }

                        // Versturen naar Exact
                        $accountAddress->Account = $account->ID;
                        $accountAddress->Type = 3; /* Invoice */
                        $accountAddress->Main = false;

                        $AddressLine1 = $item->INVOICE_ADDRESS_ADDRESSLINE . ' ' . $item->INVOICE_ADDRESS_HOUSENUMBER;
                        $accountAddress->AddressLine1 = (trim($AddressLine1) != '' ? $AddressLine1 : $accountAddress->AddressLine1);
                        $accountAddress->Postcode = ($item->INVOICE_ADDRESS_ZIPCODE != '' ? $item->INVOICE_ADDRESS_ZIPCODE : $accountAddress->Postcode);
                        $accountAddress->City = ($item->INVOICE_ADDRESS_CITY != '' ? $item->INVOICE_ADDRESS_CITY : $accountAddress->City);
                        $accountAddress->Country = ($item->INVOICE_ADDRESS_COUNTRYCODE != '' ? $item->INVOICE_ADDRESS_COUNTRYCODE : $accountAddress->Country);
                        $accountAddress->save();

                        // Adres bijwerken
                        $address->EXACT_ADDRESS_ID = $accountAddress->ID;
                        $address->EXACT_ADDRESS_ERROR = '';
                        $address->EXACT_ADDRESS_LASTSYNC = date('Y-m-d H:i:s');
                        $address->save();
                    }
                    catch (\Exception $e)
                    {
                        $address->EXACT_ADDRESS_ERROR = $e->getMessage();
                        $address->save();
                    }
                }
            }
            catch (\Exception $e)
            {
                $relation->EXACT_DEBTOR_ERROR = $e->getMessage();
                $relation->save();
            }
        }

        return response()->json([
            'success' => true
        ], 200);
    }
}