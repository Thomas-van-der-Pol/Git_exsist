<?php

namespace App\Http\Controllers\Admin\Finance\Exact;

use KJ\Core\controllers\AdminBaseController;
use App\Libraries\Admin\ExactUtils;

class ExactBaseResource extends AdminBaseController
{
    // Objecten: https://github.com/picqer/exact-php-client/tree/master/src/Picqer/Financials/Exact

    protected $connection = null;

//    protected function authorizeRequest($method, $parameters)
//    {
//        return Auth::guard()->user()->hasPermission(config('permission.FINANCIEEL'));
//    }

    public function doConnect()
    {
        $this->connection = ExactUtils::authorizeOrConnect();

        if ($this->connection != null) {
            $administrationCode = config('exact.administrationCode');

            $division = new \Picqer\Financials\Exact\Division($this->connection);
            $divisions = $division->get();

            foreach($divisions as $aDivision){
                if($aDivision->HID == $administrationCode){
                    $divisionId = $aDivision->Code;
                }
            }

            if (isset($divisionId)) {
                $this->connection->setDivision($divisionId);
            } else {
                dd('Administratiecode ongeldig!');
            }
        }
    }
}