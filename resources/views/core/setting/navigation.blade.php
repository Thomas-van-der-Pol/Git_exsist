@foreach($groups as $group)
    <li class="kt-menu__item {{ ((\App\Libraries\Shared\AppUtils::isUrlActive(['admin/settings/group/' . $group->ID]) == true) ? 'kt-menu__item--active' : '') }}" aria-haspopup="true">
        <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/' . $group->ID) }}" class="kt-menu__link ">
            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
            <span class="kt-menu__link-text">
                {{ KJLocalization::translate('Admin - Menu', $group->DESCRIPTION, $group->DESCRIPTION) }}
            </span>
        </a>
    </li>
@endforeach