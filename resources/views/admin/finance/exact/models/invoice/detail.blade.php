<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    <table class="table table-striped kt-table">
        <thead>
            <tr>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Financieel jaar', 'Financieel jaar') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Financiele periode', 'Financiele periode') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Omschrijving', 'Omschrijving') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Totaal excl', 'Totaal excl.') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Basisbedrag btw', 'Basisbedrag btw') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Btw code', 'Btw code') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Btw percentage', 'Btw percentage') }}
                </th>
                <th>
                    {{ KJLocalization::translate('Admin - Boekhouding', 'Grootboekrekening', 'Grootboekrekening') }}
                </th>
            </tr>
        </thead>

        @if(count($invoiceItems) > 0)
            <tbody>
                @foreach($invoiceItems as $invoiceItem)
                    <tr >
                        <td>
                            {{ $invoiceItem->FINANCIAL_YEAR  }}
                        </td>
                        <td>
                            {{ $invoiceItem->FINANCIAL_PERIOD }}
                        </td>
                        <td>
                            {{ $invoiceItem->DESCRIPTION }}
                        </td>
                        <td>
                            {{ isset($invoiceItem->AMOUNT_DC) ? '€ ' . number_format($invoiceItem->AMOUNT_DC, 2, KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) : '' }}
                        </td>
                        <td>
                            {{ isset($invoiceItem->AMOUNT_VAT_BASE_DC) ? '€ ' . number_format($invoiceItem->AMOUNT_VAT_BASE_DC, 2, KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) : '' }}
                        </td>
                        <td>
                            {{ $invoiceItem->VATCODE }}
                        </td>
                        <td>
                            {{ isset($invoiceItem->PERCENTAGE) ? number_format($invoiceItem->PERCENTAGE) . '%' : '' }}
                        </td>
                        <td>
                            {{ $invoiceItem->GL_ACCOUNT_CODE }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>