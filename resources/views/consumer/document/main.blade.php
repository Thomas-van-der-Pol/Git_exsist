@extends('theme.demo5.clean', ['title' => KJLocalization::translate('Portal - Menu', 'Gedeelde documenten', 'Gedeelde documenten')])

@section('topbar')
    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('shared-documents/logout') }}" class="btn btn-label btn-label-brand btn-sm btn-bold">
        {{ KJLocalization::translate('Algemeen', 'Uitloggen', 'Uitloggen') }}
    </a>
@endsection

@section('subheader')
    <div class="mt-3">
        <h2 class="mb-4">{{ KJLocalization::translate('Documenten', 'Gedeelde documenten voor project', 'Gedeelde documenten voor project :PROJECT', ['PROJECT' => $collection->project->DESCRIPTION]) }}</h2>
    </div>
@endsection

@section('content')
    @php($contact = $collection->contacts->where('FK_CRM_CONTACT', Auth::guard()->user()->ID)->first())
    @if($contact)
        <div class="alert alert-light alert-elevate fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">
                {{ KJLocalization::translate('Documenten', 'Let op: uw document(en) verlopen op', 'Let op: uw document(en) verlopen op') }}: <span class="kt-font-brand kt-font-bold">{{ $contact->getExpirationDateFormattedAttribute() }}</span>
            </div>
        </div>
    @endif

    <div class="row">
        @if($collection->documents->count() > 0)
            @foreach($collection->documents as $document)
                <div class="col-lg-3">
                    <a href="javascript:;" class="requestDocument" data-id="{{ $document->document->ID }}" data-downloader-table="{{ Auth::guard()->user()->getTable() }}" data-downloader-item="{{ Auth::guard()->user()->ID }}">
                        <div class="kt-portlet kt-iconbox kt-iconbox--brand kt-portlet--height-fluid">
                            <div class="kt-iconbox__body">
                                <div class="kt-iconbox__icon">
                                    <img name="THUMB" src="/assets/themes/demo1/media/files/{{ $document->document->FILETYPE }}.svg" alt="" width="50">
                                </div>
                                <div class="kt-iconbox__desc">
                                    <h3 class="kt-iconbox__title" style="word-break: break-word;">
                                        {{ $document->document->TITLE }}
                                    </h3>
                                    <div class="kt-iconbox__content" style="font-size: 0.8rem;">
                                        {{ KJLocalization::translate('Documenten', 'Grootte', 'Grootte') }}: {{ $document->document->FileSizeFormatted }}<br/>
                                        {{ KJLocalization::translate('Documenten', 'Gewijzigd op', 'Gewijzigd op') }}: {{ $document->document->LastModifiedFormatted }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <div class="col-lg-12">
                <h4>
                    <small class="text-muted">{{ KJLocalization::translate('Documenten', 'Geen documenten gevonden', 'Er zijn geen documenten meer gevonden. De documenten zijn verwijderd door :BEDRIJF.', ['BEDRIJF' => config('app.title')]) }}</small>
                </h4>
            </div>
        @endif
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/consumer/document/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection