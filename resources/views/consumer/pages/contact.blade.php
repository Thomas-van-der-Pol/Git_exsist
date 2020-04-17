@extends('theme.demo5.main', ['title' => KJLocalization::translate('Portal - Menu', 'Contact', 'Contact')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => '/',
        'parentTitle'   => KJLocalization::translate('Portal - Menu', 'Contact', 'Contact')
    ])
    @endcomponent
@endsection

@section('content')
    @component('portlet::main', ['notitle' => true, 'colsize' => '12 p-0'])
        <div class="row m-row--no-padding m-row--col-separator-xl">
            <div class="col-xl-12">
                {!! KJLocalization::translate('Portal - Contact', 'Contact content', 'To be filled') !!}
            </div>
        </div>
    @endcomponent
@endsection

@section('page-resources')
@endsection