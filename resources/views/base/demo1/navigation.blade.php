<li class="kt-menu__section ">
    <h4 class="kt-menu__section-text">
        {{ KJLocalization::translate('Admin - Menu', 'HOOFDMENU', 'HOOFDMENU') }}
    </h4>
    <i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>

@if(Auth::guard()->user()->hasPermission(config('permission.CRM')))
    <li class="kt-menu__item  kt-menu__item--submenu {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/crm']) == true) ? 'kt-menu__item--open kt-menu__item--expanded' : '') }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'CRM', 'CRM') }}
            </span>
            <i class="kt-menu__ver-arrow la la-angle-right"></i>
        </a>
        <div class="kt-menu__submenu ">
            <span class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/crm/relation'], ['admin/crm/contact']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation') }}" class="kt-menu__link ">
                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                        <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Relaties', 'Relaties') }}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="kt-menu__submenu ">
            <span class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/crm/contact']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/contact') }}" class="kt-menu__link ">
                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                        <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Contactpersonen', 'Contactpersonen') }}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif

@if(Auth::guard()->user()->hasPermission(config('permission.INTERVENTIES')))
    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/product']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/product') }}" class="kt-menu__link ">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M5.5,2 L18.5,2 C19.3284271,2 20,2.67157288 20,3.5 L20,6.5 C20,7.32842712 19.3284271,8 18.5,8 L5.5,8 C4.67157288,8 4,7.32842712 4,6.5 L4,3.5 C4,2.67157288 4.67157288,2 5.5,2 Z M11,4 C10.4477153,4 10,4.44771525 10,5 C10,5.55228475 10.4477153,6 11,6 L13,6 C13.5522847,6 14,5.55228475 14,5 C14,4.44771525 13.5522847,4 13,4 L11,4 Z" fill="#000000" opacity="0.3"/>
                        <path d="M5.5,9 L18.5,9 C19.3284271,9 20,9.67157288 20,10.5 L20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 L4,10.5 C4,9.67157288 4.67157288,9 5.5,9 Z M11,11 C10.4477153,11 10,11.4477153 10,12 C10,12.5522847 10.4477153,13 11,13 L13,13 C13.5522847,13 14,12.5522847 14,12 C14,11.4477153 13.5522847,11 13,11 L11,11 Z M5.5,16 L18.5,16 C19.3284271,16 20,16.6715729 20,17.5 L20,20.5 C20,21.3284271 19.3284271,22 18.5,22 L5.5,22 C4.67157288,22 4,21.3284271 4,20.5 L4,17.5 C4,16.6715729 4.67157288,16 5.5,16 Z M11,18 C10.4477153,18 10,18.4477153 10,19 C10,19.5522847 10.4477153,20 11,20 L13,20 C13.5522847,20 14,19.5522847 14,19 C14,18.4477153 13.5522847,18 13,18 L11,18 Z" fill="#000000"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'Interventies', 'Interventies') }}
            </span>
        </a>
    </li>
@endif

@if(Auth::guard()->user()->hasPermission(config('permission.DOSSIERS')))
    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/project']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/project') }}" class="kt-menu__link ">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5"/>
                        <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L12.5,10 C13.3284271,10 14,10.6715729 14,11.5 C14,12.3284271 13.3284271,13 12.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="#000000" opacity="0.3"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'Dossiers', 'Dossiers') }}
            </span>
        </a>
    </li>
@endif


@php
    $today = date('Y-m-d');

    $count =  \App\Models\Admin\Task\Task::whereDate('DEADLINE', '<=', $today)->where([
        'ACTIVE' => true,
        'FK_TASK_LIST' => null,
        'DONE' => false,
        'FK_CORE_USER_ASSIGNEE' => Auth::guard()->user()->ID
    ])
    ->count();
@endphp

@if(Auth::guard()->user()->hasPermission(config('permission.TAKEN')))
    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/tasks']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/tasks') }}" class="kt-menu__link ">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                        <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'Taken', 'Taken') }}
            </span>
            @if($count > 0)
                <span class="kt-nav__link-badge">
                    <span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">
                        {{ $count }}
                    </span>
                </span>
            @endif
        </a>
    </li>
@endif

@if(Auth::guard()->user()->hasPermission(config('permission.FACTURATIE')))
    <li class="kt-menu__item  kt-menu__item--submenu {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/invoice', 'admin/accountancy']) == true) ? 'kt-menu__item--open kt-menu__item--expanded' : '') }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                        <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'Facturatie', 'Facturatie') }}
            </span>
            <i class="kt-menu__ver-arrow la la-angle-right"></i>
        </a>
        <div class="kt-menu__submenu ">
            <span class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">

                <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/invoice/prepare']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice/prepare') }}" class="kt-menu__link ">
                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                        <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Facturatie voorbereiden', 'Facturatie voorbereiden') }}
                        </span>
                    </a>
                </li>

                <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/invoice'], ['admin/invoice/prepare']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice') }}" class="kt-menu__link ">
                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                        <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Facturen', 'Facturen') }}
                        </span>
                    </a>
                </li>
                @if(Auth::guard()->user()->hasPermission(config('permission.FACTURATIE_BOEKHOUDING')))
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/accountancy']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/accountancy') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                                {{ KJLocalization::translate('Admin - Menu', 'Boekhouding', 'Boekhouding') }}
                            </span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif

@if(Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')))
    <li class="kt-menu__item  kt-menu__item--submenu {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings']) == true) ? 'kt-menu__item--open kt-menu__item--expanded' : '') }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
        <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
            <span class="kt-menu__link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect id="Bound" opacity="0.200000003" x="0" y="0" width="24" height="24"/>
                        <path d="M4.5,7 L9.5,7 C10.3284271,7 11,7.67157288 11,8.5 C11,9.32842712 10.3284271,10 9.5,10 L4.5,10 C3.67157288,10 3,9.32842712 3,8.5 C3,7.67157288 3.67157288,7 4.5,7 Z M13.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L13.5,18 C12.6715729,18 12,17.3284271 12,16.5 C12,15.6715729 12.6715729,15 13.5,15 Z" id="Combined-Shape" fill="#000000" opacity="0.3"/>
                        <path d="M17,11 C15.3431458,11 14,9.65685425 14,8 C14,6.34314575 15.3431458,5 17,5 C18.6568542,5 20,6.34314575 20,8 C20,9.65685425 18.6568542,11 17,11 Z M6,19 C4.34314575,19 3,17.6568542 3,16 C3,14.3431458 4.34314575,13 6,13 C7.65685425,13 9,14.3431458 9,16 C9,17.6568542 7.65685425,19 6,19 Z" id="Combined-Shape" fill="#000000"/>
                    </g>
                </svg>
            </span>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen') }}
            </span>
            <i class="kt-menu__ver-arrow la la-angle-right"></i>
        </a>
        <div class="kt-menu__submenu ">
            <span class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                @if( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) )
                    {!! \App\Libraries\Core\SettingUtils::renderGroupsMenu() !!}
                @endif


{{--                @if( Auth::guard()->user()->hasPermission(config('permission.SETTINGS_LANGUAGE')) )--}}
{{--                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/language']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">--}}
{{--                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/language') }}" class="kt-menu__link ">--}}
{{--                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>--}}
{{--                            <span class="kt-menu__link-text">--}}
{{--                                {{ KJLocalization::translate('Admin - Menu', 'Language', 'Language') }}--}}
{{--                            </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endif--}}

                @if(( Auth::guard()->user()->hasPermission(config('permission.FACTURATIE')) && Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) ))
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/finance']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                                {{ KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel') }}
                            </span>
                        </a>
                    </li>
                @endif

                @if( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) )
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/tasklist']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten') }}
                        </span>
                        </a>
                    </li>
                @endif

                @if(( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN_WERKNEMERS')) && Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) ))
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/user']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                                {{ KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers') }}
                            </span>
                        </a>
                    </li>
                @endif

                @if( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) )
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/role']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/role') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                                {{ KJLocalization::translate('Admin - Menu', 'Rollen & rechten', 'Rollen & rechten') }}
                            </span>
                        </a>
                    </li>
                @endif

                @if( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) )
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/host']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/host') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                            {{ KJLocalization::translate('Admin - Menu', 'Werkstations', 'Werkstations') }}
                        </span>
                        </a>
                    </li>
                @endif

                @if( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')) )
                    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/translation']) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
                        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/translation') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                            <span class="kt-menu__link-text">
                                {{ KJLocalization::translate('Admin - Menu', 'Vertalingen', 'Vertalingen') }}
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>
@endif