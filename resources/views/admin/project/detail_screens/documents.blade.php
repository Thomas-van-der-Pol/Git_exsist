@include('core.document.main', [
    'FK_TABLE' => $item->getTable(),
    'FK_ITEM' => $item->ID,
    'options' => [
        'editable' => true,
        'uploader_table' => Auth::guard('admin')->user()->getTable(),
        'uploader_item' => Auth::guard('admin')->user()->ID
    ]
])