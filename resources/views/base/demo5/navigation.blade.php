<li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['home','/']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('home') }}" class="kt-menu__link">
        <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Home', 'Home') }}</span>
    </a>
</li>

@if(!Auth::guard('web')->check())
    <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['login']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('login') }}" class="kt-menu__link">
            <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Login', 'Login') }}</span>
        </a>
    </li>
@else
    @if(Auth::guard()->user()->isClient())
        <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['families']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('families') }}" class="kt-menu__link">
                <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Families', 'Families') }}</span>
            </a>
        </li>
        <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['relocations']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('relocations') }}" class="kt-menu__link">
                <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Relocations', 'Relocations') }}</span>
            </a>
        </li>
        <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['schools']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('schools') }}" class="kt-menu__link">
                <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Schools', 'Schools') }}</span>
            </a>
        </li>
    @else
        <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['relocation']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('relocation') }}" class="kt-menu__link">
                <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Relocation', 'Relocation') }}</span>
            </a>
        </li>
        <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['questionnaire']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('questionnaire') }}" class="kt-menu__link">
                <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Questionnaires', 'Questionnaires') }}</span>

                @php($outstandingQuestionnaires = \App\Libraries\Consumer\QuestionnaireUtils::getOutstandingQuestionnaires())
                @if($outstandingQuestionnaires > 0)
                    <span class="kt-menu__link-badge">
                        <span class="kt-badge kt-badge--rounded kt-badge--brand">{{ $outstandingQuestionnaires }}</span>
                    </span>
                @endif
            </a>
        </li>
    @endif

    <li class="kt-menu__item kt-menu__item--open kt-menu__item--rel {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['profile']) == true) ? ' kt-menu__item--here kt-menu__item--hover' : '') }}">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('profile') }}" class="kt-menu__link">
            <span class="kt-menu__link-text">{{ KJLocalization::translate('Portal - Menu', 'Profile', 'Profile') }}</span>
        </a>
    </li>
@endif