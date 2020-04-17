@if( Auth::guard('web')->check())
    <!--begin: User bar -->
    <div class="kt-header__topbar-item kt-header__topbar-item--user">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px">
            <span class="kt-header__topbar-welcome"></span>
            <span class="kt-header__topbar-username kt-hidden-mobile">{{ Auth::user()->FULLNAME }}</span>
            <span class="kt-header__topbar-icon kt-header__topbar-icon--brand">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" id="Mask" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" id="Mask-Copy" fill="#000000" fill-rule="nonzero"/>
                    </g>
                </svg>
            </span>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">

            <!--begin: Head -->
            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x">
                <div class="kt-user-card__avatar">
                    <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" id="Mask" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" id="Mask-Copy" fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>
                </div>
                <div class="kt-user-card__name">
                    {{ Auth::user()->FULLNAME }}<br/>
                    <small>{{ Auth::user()->EMAILADDRESS }}</small>
                </div>
            </div>
            <!--end: Head -->

            <!--begin: Navigation -->
            <div class="kt-notification">
                <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('profile') }}" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                        <i class="flaticon-lock kt-font-brand"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {{ KJLocalization::translate('Portal - Menu', 'Profile', 'Profile') }}
                        </div>
                        <div class="kt-notification__item-time">
                            {{ KJLocalization::translate('Algemeen', 'Change password', 'Change password') }}
                        </div>
                    </div>
                </a>

                <div class="kt-notification__custom kt-space-between">
                    <a href="/logout" class="btn btn-label btn-label-brand btn-sm btn-bold">
                        {{ KJLocalization::translate('Algemeen', 'Uitloggen', 'Uitloggen') }}
                    </a>
                </div>
            </div>
            <!--end: Navigation -->
        </div>
    </div>
    <!--end: User bar -->

    <!--begin: Language bar -->
    <div class="kt-header__topbar-item kt-header__topbar-item--langs">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px">
            <span class="kt-header__topbar-icon kt-header__topbar-icon--brand">
                @php($config = \KJ\Localization\libraries\LanguageUtils::getLanguageConfig(App::getLocale()))
                {!! Html::image($config['ICONPATH'], $config['DESCRIPTION'], ['height' => '16']) !!}
            </span>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim">
            <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                @foreach(config('language.langs') as $lang)
                    @php($currentLanguage = (strtolower(App::getLocale()) == strtolower($lang['CODE'])))
                    @php($language = \App\Models\Admin\Core\Language::find($lang['ID']))

                    <li class="kt-nav__item {{ ($currentLanguage ? 'kt-nav__item--active' : '') }}">
                        <a href="/changeLanguage/{{ $lang['CODE'] }}" class="kt-nav__link">
                            <span class="kt-nav__link-icon">{!! Html::image($lang['ICONPATH'], $lang['DESCRIPTION'], ['height' => '16']) !!}</span>
                            <span class="kt-nav__link-text">{{ $language ? $language->getLanguageDescriptionAttribute($lang['ID']) : $lang['DESCRIPTION'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <!--end: Language bar -->
@endif