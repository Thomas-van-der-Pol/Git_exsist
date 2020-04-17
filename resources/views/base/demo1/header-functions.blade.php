@if( Auth::guard('admin')->check())

    <!--begin: Notifications -->
    @php
        $notifications = \App\Models\Admin\Core\Notification::where([
            'ACTIVE' => true,
            'READED' => false,
            'RECIPIENT_FK_CORE_USER' => Auth::guard()->user()->ID
        ])
        ->where('DATE', '<=', date('Y-m-d'))
        ->get();
    @endphp
    <div class="kt-header__topbar-item dropdown">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px" aria-expanded="true">
            <span class="kt-header__topbar-icon {{ $notifications->count() > 0 ? 'kt-pulse kt-pulse--brand' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3"></path>
                        <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000"></path>
                    </g>
                </svg>
                <span class="kt-pulse__ring"></span>
            </span>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
            <form>
                <!--begin: Head -->
                <div class="kt-head kt-head--skin-dark kt-user-card--skin-dark">
                    <div class="kt-form kt-form--label-right">
                        <div class="row align-items-center">
                            <div class="col order-2 order-xl-2">
                                <h3 class="kt-head__title float-left ml-3">
                                    {{ KJLocalization::translate('Admin - Menu', 'Notificaties', 'Notificaties') }}
                                </h3>
                            </div>
                            <div class="col order-2 order-xl-2">
                                <span class="kt-badge kt-badge--success kt-badge--xl kt-badge--inline pull-right">
                                    {{ $notifications->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Head -->

                <div class="tab-content">
                    @if($notifications->count() > 0)
                        <div class="tab-pane active show" id="topbar_notifications_notifications" role="tabpanel">
                            <div class="kt-notification kt-scroll ps" data-scroll="true" data-height="300" data-mobile-height="200" style="height: 300px; overflow: hidden;">
                                @foreach($notifications as $notification)
                                    <a href="{{ $notification->SOURCE_URL ?? '' }}" class="kt-notification__item readedNotification" data-id="{{ $notification->ID ?? 0 }}" data-url="{{ $notification->SOURCE_URL ?? '' }}">
                                        <div class="kt-notification__item-details">
                                            <div class="kt-notification__item-title">
                                                {{ $notification->SUBJECT ?? '' }}
                                            </div>
                                            @if($notification->CONTENT)
                                                <div class="kt-notification__item-content">
                                                    {{ $notification->CONTENT }}
                                                </div>
                                            @endif
                                            <div class="kt-notification__item-time">
                                                {{ $notification->getDateFormattedAttribute() ?? '' }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div>
                            </div>
                        </div>
                    @else
                        <div class="tab-pane active show" id="topbar_notifications_logs" role="tabpanel">
                            <div class="kt-grid kt-grid--ver" style="min-height: 200px;">
                                <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                    <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                        {{ KJLocalization::translate('Admin - Menu', 'Geen notificaties', 'Geen notificaties') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <!--end: Notifications -->

    <!--begin: User Bar -->
    <div class="kt-header__topbar-item kt-header__topbar-item--user">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px">
            <div class="kt-header__topbar-user">
                <span class="kt-header__topbar-username kt-hidden-mobile">{{ Auth::user()->FULLNAME }}</span>
                <span class="kt-header__topbar-icon kt-header__topbar-icon--brand">
                    <span class="kt-badge kt-badge--username kt-badge--unified-brand kt-badge--lg kt-badge--rounded kt-badge--bold">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" id="Mask" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" id="Mask-Copy" fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>
                </span>
            </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

            <!--begin: Head -->
            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x">
                <div class="kt-user-card__avatar">
                    <span class="kt-badge kt-badge--unified-brand kt-badge--lg kt-badge--rounded kt-badge--bold">
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
                <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/profile') }}" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                        <i class="flaticon-lock kt-font-brand"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {{ KJLocalization::translate('Admin - Menu', 'Profiel', 'Profiel') }}
                        </div>
                        <div class="kt-notification__item-time">
                            {{ KJLocalization::translate('Algemeen', 'Wachtwoord wijzigen', 'Wachtwoord wijzigen') }}
                        </div>
                    </div>
                </a>

                <div class="kt-notification__custom kt-space-between">
                    <a href="/admin/logout" class="btn btn-label btn-label-brand btn-sm btn-bold">
                        {{ KJLocalization::translate('Algemeen', 'Uitloggen', 'Uitloggen') }}
                    </a>
                </div>
            </div>
            <!--end: Navigation -->
        </div>
    </div>
    <!--end: User Bar -->

    <!--begin: Language bar -->
    @if(count(config('language.langs')) > 1)
        <div class="kt-header__topbar-item kt-header__topbar-item--langs">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px">
                <span class="kt-header__topbar-icon kt-header__topbar-icon--brand">
                    <span class="kt-badge kt-badge--unified-brand kt-badge--lg kt-badge--rounded kt-badge--bold">
                        @php($config = \KJ\Localization\libraries\LanguageUtils::getLanguageConfig(App::getLocale()))
                        {!! Html::image($config['ICONPATH'], $config['DESCRIPTION'], ['height' => '16']) !!}
                    </span>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                    @foreach(config('language.langs') as $lang)
                        @php($currentLanguage = (strtolower(App::getLocale()) == strtolower($lang['CODE'])))
                        @php($language = \App\Models\Admin\Core\Language::find($lang['ID']))

                        <li class="kt-nav__item {{ ($currentLanguage ? 'kt-nav__item--active' : '') }}">
                            <a href="/admin/changeLanguage/{{ $lang['CODE'] }}" class="kt-nav__link">
                                <span class="kt-nav__link-icon">{!! Html::image($lang['ICONPATH'], $lang['DESCRIPTION'], ['height' => '16']) !!}</span>
                                <span class="kt-nav__link-text">{{ $language ? $language->getLanguageDescriptionAttribute($lang['ID']) : $lang['DESCRIPTION'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <!--end: Language bar -->
@endif