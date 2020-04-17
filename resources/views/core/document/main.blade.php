<div id="documentContainer">
    {{ Form::hidden('FK_TABLE', $FK_TABLE) }}
    {{ Form::hidden('FK_ITEM', $FK_ITEM) }}
    {{ Form::hidden('documents_editable', ($options['editable'] ?? false)) }}
    {{ Form::hidden('uploader_table', ($options['uploader_table'] ?? null)) }}
    {{ Form::hidden('uploader_item', ($options['uploader_item'] ?? null)) }}
    {{ Form::hidden('document_library', ($options['document_library'] ?? null)) }}

    <div class="kt-widget4" style="min-height: 200px">
        <div class="row m-0">
            <div class="bordered-toolbar col-auto py-2 pr-0 pl-4">
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group mr-2" role="group">
                        @if(($options['editable'] ?? false) === true)
                            <button type="button" class="btn btn-outline-success btn-sm addDirectory"><i class="fa fa-folder-plus"></i>{{ KJLocalization::translate('Documenten', 'Add directory', 'Add directory') }}</button>
                        @endif
                        <button type="button" class="btn btn-outline-success btn-sm addFile"><i class="fa fa-plus-square"></i>{{ KJLocalization::translate('Documenten', 'Add file', 'Add file') }}</button>
                    </div>
                    @if(($options['editable'] ?? false) === true)
                        <div class="btn-group mr-2" role="group">
                            <button type="button" class="btn btn-secondary btn-sm renameFile" disabled="disabled"><i class="fa fa-edit"></i>{{ KJLocalization::translate('Documenten', 'Rename', 'Rename') }}</button>
                            <button type="button" class="btn btn-danger btn-sm deleteFiles" disabled="disabled"><i class="fa fa-trash"></i>{{ KJLocalization::translate('Documenten', 'Remove', 'Remove') }}</button>
                        </div>
                    @endif
                </div>
            </div>
            <div id="breadcrumb-dropzone" class="bordered-toolbar col p-2 pl-0"></div>
            <div id="reloadDocuments" class="bordered-toolbar col-auto py-2 pr-4">
                <a href="javascript:;" class="reloadButton"><i class="la la-refresh"></i></a>
            </div>
        </div>

        <div class="kt-dropzone dropzone" id="documentUploader" style="min-height: 200px">
            <div class="kt-dropzone__msg dz-message needsclick">
            </div>
        </div>

        <div id="dropzone-template-preview" style="display: none">
            <div class="kt-widget4__item">

{{--                @if(($options['editable'] ?? false) === true)--}}
                    <div class="checkboxField draggable_remove_at_clone" style="width: 37px;">
                        <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand mb-0 ml-2">
                            <input type="checkbox" name="MARK_DOCUMENT" class="exclude-screen-mode">
                            <span></span>
                        </label>
                    </div>
                {{--@endif--}}

                <div class="kt-widget4__img kt-widget4__img--logo">
                    <img name="THUMB" src="" alt="" />
                </div>

                <div class="kt-widget4__info">
                    <div class="row">
                        <div class="col">
                            <a href="javascript:;" class="kt-widget4__title"></a>
                        </div>

                        {{-- @TODO: NOG OP DYNAMISCHE MANIER MAKEN --}}
                        {{--@if(($options['show_custom_details'] ?? false) === true)--}}
                            {{--<div class="draggable_remove_at_clone no_header" style="width: 200px;">--}}
                                {{--<div class="kt-checkbox-inline">--}}
                                    {{--<label class="kt-checkbox">--}}
                                        {{--<input type="checkbox" class="autoSaveDocument" name="AVAILABLE_CLIENT" value="1" > {{ KJLocalization::translate('Documenten', 'Client', 'Client') }}--}}
                                        {{--<span class="mt-2"></span>--}}
                                    {{--</label>--}}
                                    {{--<label class="kt-checkbox">--}}
                                        {{--<input type="checkbox" class="autoSaveDocument" name="AVAILABLE_FAMILY" value="1" > {{ KJLocalization::translate('Documenten', 'Family', 'Family') }}--}}
                                        {{--<span class="mt-2"></span>--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}

                        @if(($options['show_document_info'] ?? true) === true)
                            <div class="draggable_remove_at_clone" style="width: 100px;">
                                <span class="fileSize"></span>
                            </div>

                            <div class="draggable_remove_at_clone" style="width: 150px;">
                                <span class="fileType"></span>
                            </div>
                        @endif

                        <div class="draggable_remove_at_clone" style="width: 120px;">
                            <span class="fileModified"></span>
                        </div>

                        @if(($options['show_document_info'] ?? true) === true)
                            <div class="draggable_remove_at_clone" style="width: 30px;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon mr-2 documentInfo" data-toggle="kt-popover" data-placement="top" data-html="true">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"></circle>
                                        <path d="M12,16 C12.5522847,16 13,16.4477153 13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 C11,16.4477153 11.4477153,16 12,16 Z M10.591,14.868 L10.591,13.209 L11.851,13.209 C13.447,13.209 14.602,11.991 14.602,10.395 C14.602,8.799 13.447,7.581 11.851,7.581 C10.234,7.581 9.121,8.799 9.121,10.395 L7.336,10.395 C7.336,7.875 9.31,5.922 11.851,5.922 C14.392,5.922 16.387,7.875 16.387,10.395 C16.387,12.915 14.392,14.868 11.851,14.868 L10.591,14.868 Z" fill="#000000"></path>
                                    </g>
                                </svg>
                            </div>
                        @endif

                        <div class="draggable_remove_at_clone" style="width: 30px;">
                            {{ Form::open(array(
                                'method' => 'post',
                                'class' => 'kt-form',
                                'novalidate'
                            )) }}
                                {{ Form::hidden('ID', '') }}
                                {{ Form::hidden('after_save', ($options['after_save'] ?? null)) }}
                                {{ Form::hidden('after_remove', ($options['after_remove'] ?? null)) }}

                                <span data-dz-remove>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                            <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </span>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var currentFolder = "<?php echo (isset($BASE_FOLDER) ? $BASE_FOLDER : $FK_TABLE . '/' . $FK_ITEM); ?>";
    var baseFolder = "<?php echo (isset($BASE_FOLDER) ? $BASE_FOLDER : $FK_TABLE . '/' . $FK_ITEM); ?>";
</script>