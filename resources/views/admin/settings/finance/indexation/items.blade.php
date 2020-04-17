<table class="table table-sm table-head-bg-brand">
    <thead class="thead-inverse">
        <tr>
            <th width="700">{{ KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving') }}</th>
            <th width="120">{{ KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage') }}</th>
            <th width="100">{{ KJLocalization::translate('Admin - Financieel', 'Oud', 'Oud') }}</th>
            <th width="100">{{ KJLocalization::translate('Admin - Financieel', 'Nieuw', 'Nieuw') }}</th>
            <th width="100">{{ KJLocalization::translate('Admin - Financieel', 'Verschil', 'Verschil') }}</th>
            <th style="border-bottom: unset !important;"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3"><h4 class="mt-2">{{ KJLocalization::translate('Admin - Financieel', 'Standaard producten & diensten', 'Standaard producten & diensten') }}</h4></td>
        </tr>

        @foreach($items as $item)
            @if($item->FLAG == 96)
                @if(!$loop->first)
                    <tr><td colspan="5" style="height: 40px"></td></tr>
                @endif
                <tr>
                    <td colspan="4"><h4 class="mt-2">{{ KJLocalization::translate('Admin - Financieel', 'Relatie producten & diensten', 'Relatie producten & diensten') }}</h4></td>
                </tr>
            @elseif($item->FLAG == 100)
                @if(!$loop->first)
                    <tr><td colspan="5" style="height: 40px"></td></tr>
                @endif
                <tr>
                    <td colspan="4"><h4 class="mt-2">{{ KJLocalization::translate('Admin - Financieel', 'Periodieke facturen', 'Periodieke facturen') }}</h4></td>
                </tr>
            @else
                @if(in_array($item->FLAG, [1, 97, 101]))
                    <tr style="background-color: var(--kj-zakelijk-blauw) !important;color: white !important;font-weight: bold;">
                @elseif(in_array($item->FLAG, [2, 98, 102]))
                    <tr style="background-color: var(--kj-fris-blauw) !important;color: white !important;font-weight: bold;">
                @else
                    <tr data-flag="{{ ( $item->FLAG ?? 1 ) }}">
                @endif
                    <td>{{ ( $item->DESCRIPTION ?? '' ) }}</td>
                    <td>{{ (($item->PERCENTAGE > 0) ? number_format($item->PERCENTAGE, 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), '') . '%' : '') }}</td>
                    <td>{{ $item->PRICE ? '€ ' . ( number_format((float)$item->PRICE, 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) ?? '' ) : '' }}</td>
                    <td>{{ $item->NEW_PRICE ? '€ ' . ( number_format((float)$item->NEW_PRICE, 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) ?? '' ) : '' }}</td>
                    <td class="{{ (($item->DIFFERENT_PRICE ?? 0) < 0) ? 'kt-font-bold kt-font-danger' : '' }}">{{ $item->DIFFERENT_PRICE ? '€ ' . ( number_format((float)$item->DIFFERENT_PRICE, 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) ?? '' ) : '' }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr><td colspan="5" style="height: 40px"></td></tr>
        <tr style="background-color: var(--kj-actief-rood); color: #ffffff; font-weight: bold;">
            <td colspan="2">{{ KJLocalization::translate('Admin - Financieel', 'Totaal', 'Totaal') }}</td>
            <td>{{ '€ ' . number_format((float)$items->sum('PRICE'), 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) }}</td>
            <td>{{ '€ ' . number_format((float)$items->sum('NEW_PRICE'), 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) }}</td>
            <td>{{ '€ ' . number_format((float)$items->sum('DIFFERENT_PRICE'), 2, \KJ\Localization\libraries\LanguageUtils::getDecimalPoint(), \KJ\Localization\libraries\LanguageUtils::getThousandsSeparator()) }}</td>
        </tr>
    </tfoot>
</table>
