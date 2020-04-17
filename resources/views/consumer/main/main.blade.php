@extends('theme.demo5.main', ['title' => KJLocalization::translate('Portal - Menu', 'Home', 'Home')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => '/',
        'parentTitle'   => KJLocalization::translate('Portal - Menu', 'Home', 'Home'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('home'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Algemeen', 'Welcome', 'Welcome').' '.Auth::guard()->user()->FIRSTNAME, 'colsize' => 12])
            @if(Auth::guard()->user()->isClient())
                {!! stripslashes(KJLocalization::translate('Portal - Home', 'Welcome text client', 'To be filled (navigate to admin -> translation -> search for welcome text client)', [], null, true)) !!}
            @else
                {!! stripslashes(KJLocalization::translate('Portal - Home', 'Welcome text family', 'To be filled (navigate to admin -> translation -> search for welcome text family)', [], null, true)) !!}
            @endif
        @endcomponent
    </div>
@endsection

@section('page-resources')

@endsection