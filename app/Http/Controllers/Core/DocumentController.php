<?php

namespace App\Http\Controllers\Core;

use App\Models\Core\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use KJ\Core\controllers\BaseController;
use KJ\Core\models\FileRequest;
use KJLocalization;

class DocumentController extends BaseController
{
    protected $guard = 'auth:web,admin,document';

    protected $model = 'App\Models\Core\Document';

    protected $saveUnsetValues = [
        'after_save',
        'after_remove'
    ];

    public function retrieve(Request $request)
    {
        $dir = ($request->get('dir') ?? '');
        $dir = str_replace('/', '\\', $dir);

        $base_dir = ($request->get('base_dir') ?? '');
        $base_dir = str_replace('/', '\\', $base_dir);

        $uploader_table = $request->get('uploader_table');
        $uploader_item = $request->get('uploader_item');
        $document_library = $request->get('document_library');

        $documents = Document::where(function($query) use ($dir) {
                $query->whereRaw("DIRECTORY = IIF('".$dir."' <> '', '".$dir."' + '\', '".$dir."')");
                if ($dir == '') {
                    $query->orWhereNull('DIRECTORY');
                }
            })
            ->where(function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('FILETYPE', 'dir');
                    $subQuery->whereNotExists(function($dirQuery) {
                        $dirQuery->select(DB::raw(1))
                            ->from('DOCUMENT AS DOC2')
                            ->whereRaw("DOC2.DIRECTORY = ISNULL(DOCUMENT.FILEPATH, '') + '\'");
                    });
                });
                $query->orWhere('FILETYPE', '<>', 'dir');
            });

        if ($document_library) {
            $documents = call_user_func_array($document_library.'::filter_documents', array($documents));
        }
        $documents = $documents->get();

        $directories = Document::whereNotNull('DIRECTORY')
            ->whereRaw("DIRECTORY <> IIF('".$dir."' <> '', '".$dir."' + '\', '".$dir."')")
            ->where(function($query) use ($dir) {
                $query->where('DIRECTORY', 'like', $dir . '%');
                if ($dir == '') {
                    $query->orWhereNull('DIRECTORY');
                }
            });

        if ($document_library) {
            $directories = call_user_func_array($document_library.'::filter_directories', array($dir, $directories));
        }

        $directories = $directories->select([
//                'FK_TABLE',
//                'FK_ITEM',
                DB::raw("'dir' AS FILETYPE"),
                DB::raw("SUBSTRING(
                    REPLACE(
                        DIRECTORY, 
                        IIF('".$dir."' <> '', '".$dir."' + '\', '".$dir."'), 
                        ''
                    ), 
                    0, 
                    IIF(
                        CHARINDEX('\', REPLACE(DIRECTORY, IIF('".$dir."' <> '', '".$dir."' + '\', '".$dir."'), '')) > 0,
                        CHARINDEX('\', REPLACE(DIRECTORY, IIF('".$dir."' <> '', '".$dir."' + '\', '".$dir."'), '')),
                        LEN(DIRECTORY)
                    )
                ) AS TITLE"),
            ])
            ->distinct()
            ->get();

        $itemCount = $documents->count() + $directories->count();

        // Add dummy item for parent folder
        if (($dir != '') && ($dir != $base_dir)) {
            $parentDir = new Document();
            $parentDir->ID = -1;
            $parentDir->FILETYPE = 'dir';
            $parentDir->TITLE = '...';
            $parentDir->DIRECTORY = '';

            $directoryParts = explode('\\', $dir);
            for ($i = 0; $i < count($directoryParts) - 1; $i++) {
                $parentDir->DIRECTORY .= ($parentDir->DIRECTORY ? '\\' : '') . $directoryParts[$i];
            }

            $directories->prepend($parentDir);
        }

        // toBase = convert to base Collection instead of Database collection
        $merged = $directories->toBase()->merge($documents);

        // Sorting - Directories first, then files ordered by title
        $merged = $merged->sortBy('TITLE')->sortBy(function ($document) {
            if ($document->FILETYPE == 'dir') {
                return 1; // Top
            } else {
                return 2; // Bottom
            }
        })->values();

        foreach($merged as $document) {
            if ($document_library) {
                $document->DELETE_PERMISSION = call_user_func_array($document_library . '::check_document_permission', array($document, $uploader_table, $uploader_item));
            } else {
                $document->DELETE_PERMISSION = true; // Admin
            }

            if ($document->FILETYPE !== 'dir') {
                $document->DOCUMENT_INFORMATION = $document->getDocumentInformationAttribute();
                $document->FILESIZE_FORMATTED = $document->getFileSizeFormattedAttribute();
                $document->LASTMODIFIED_FORMATTED = $document->getLastModifiedFormattedAttribute();
            }
            // Directories
            else if ($document->ID != -1) {
                $document->DIRECTORY = (($dir != '') ? $dir . '\\' : $dir) . $document->TITLE;

                $fileSize = Document::where(function($query) use ($document) {
                        $query->where('DIRECTORY', 'like', $document->DIRECTORY . '%');
                        if ($document->DIRECTORY == '') {
                            $query->orWhereNull('DIRECTORY');
                        }
                    })
                    ->sum('FILESIZE');

                $document->FILESIZE_FORMATTED = Document::getFileSize($fileSize);
            }

            $document->FILETYPE_FORMATTED = $document->getFileTypeFormattedAttribute();
        }

        return response()->json([
            'success' => true,
            'item_count' => $itemCount,
            'items' => $merged
        ]);
    }

    public function upload(Request $request)
    {
        $table = $request->get('fk_table');
        $item = $request->get('fk_item');
        $uploader_table = $request->get('uploader_table');
        $uploader_item = $request->get('uploader_item');
        $document_library = $request->get('document_library');
        $currentDir = '/' . str_replace('\\', '/', ($request->get('directory') ?? ''));

        $result = false;

        $file = $request->file('file');

        if ($file) {
            // Save in storage
            $fullpathsavedfile = $file->storeAs($currentDir, $file->getClientOriginalName(), ['disk' => 'ftp']);
            $accessableFile = str_replace('public/','storage/', $fullpathsavedfile);

            $documentInfo = pathinfo($accessableFile);

            // Link to model
            $document = new Document();
            $document->FK_TABLE = $table;
            $document->FK_ITEM = $item;
            $document->UPLOADER_FK_TABLE = $uploader_table;
            $document->UPLOADER_FK_ITEM = $uploader_item;
            $document->FILEPATH = str_replace('/', '\\', $accessableFile);
            $document->FILESIZE = $file->getSize();
            $document->TITLE = $documentInfo['filename'];
            $document->FILETYPE = ($file->getClientOriginalExtension() ?? 'file');

            if ($document_library) {
                $document = call_user_func_array($document_library.'::upload_document', array($document));
            }

            $result = $document->save();

            // Add additional information
            $document->refresh();
            if ($document_library) {
                $document->DELETE_PERMISSION = call_user_func_array($document_library . '::check_document_permission', array($document, $uploader_table, $uploader_item));
            } else {
                $document->DELETE_PERMISSION = true; // Admin
            }

            $document->DOCUMENT_INFORMATION = $document->getDocumentInformationAttribute();
            $document->FILETYPE_FORMATTED = $document->getFileTypeFormattedAttribute();
            $document->FILESIZE_FORMATTED = $document->getFileSizeFormattedAttribute();
            $document->LASTMODIFIED_FORMATTED = $document->getLastModifiedFormattedAttribute();
        }

        $response = [
            'success' => $result,
            'document' => $document
        ];

        $this->afterSave($document, null, $request, $response);

        return response()->json($response);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        $after_save = $request->get('after_save');
        if ($after_save) {
            $response = call_user_func_array($after_save, array($item, $originalItem, $request, $response));
        }
    }

    public function request(Request $request)
    {
        $id = $request->get('id');
        $uploader_table = $request->get('uploader_table');
        $uploader_item = $request->get('uploader_item');
        $document = Document::find($id);

        if ($document) {
            $fileRequest = new FileRequest();
            $fileRequest->OBJECT = $document->getTable();
            $fileRequest->OBJECT_ID = $document->ID;
            $fileRequest->REQUEST_OBJECT = $uploader_table;
            $fileRequest->REQUEST_OBJECT_ID = $uploader_item;
            $fileRequest->FILENAME = $document->FILEPATH;
            $fileRequest->save();
            $fileRequest->refresh();

            return response()->json([
                'success' => true,
                'request_token' => $fileRequest->TOKEN,
                'try_communicator' => in_array($document->FILETYPE, ['doc', 'docx']),
                'communicator_url' => URL::to('/') . '/api/',
                'communicator_title' => $document->TITLE
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Administration - Document', 'Document unavailable', 'Document unavailable')
            ]);
        }
    }

    protected function deleteSingleDocument($document, $after_remove)
    {
        if ($after_remove != '') {
            call_user_func_array($after_remove, array($document));
        }
        return $document->delete();
    }

    public function delete(Request $request)
    {
        $selectedDocuments = json_decode(( $request->get('documents') ?? 0 ), true);
        $after_remove = ($request->get('after_remove') ?? '');

        $result = false;

        foreach ($selectedDocuments as $selectedDocument) {
            if ($selectedDocument['type'] == 'dir')
            {
                $documents = Document::where(function($query) use ($selectedDocument) {
                        $query->where('DIRECTORY', 'like', $selectedDocument['folder'] . '%');
                        $query->orWhere('FILEPATH', $selectedDocument['folder']);
                        if ($selectedDocument['folder'] == '') {
                            $query->orWhereNull('DIRECTORY');
                        }
                    })
                    ->get();

                foreach ($documents as $document) {
                    $result = $this->deleteSingleDocument($document, $after_remove);
                }
            }
            else
            {
                $document = Document::find($selectedDocument['id']);
                if ($document) {
                    $result = $this->deleteSingleDocument($document, $after_remove);
                }
            }
        }

        return response()->json([
            'success' => $result
        ]);
    }

    public function addFolder(Request $request)
    {
        $table = $request->get('fk_table');
        $item = $request->get('fk_item');
        $uploader_table = $request->get('uploader_table');
        $uploader_item = $request->get('uploader_item');
        $currentDir = ($request->get('directory') ?? '');
        $newDirectoryName = $request->get('new_directory_name');

        $result = false;

        if ($newDirectoryName) {
            // Add model
            $document = new Document();
            $document->FK_TABLE = $table;
            $document->FK_ITEM = $item;
            $document->UPLOADER_FK_TABLE = $uploader_table;
            $document->UPLOADER_FK_ITEM = $uploader_item;
            $document->FILEPATH = (($currentDir != '') ? $currentDir . '\\' : $currentDir) . $newDirectoryName;
            $document->TITLE = $newDirectoryName;
            $document->FILETYPE = 'dir';

            $result = $document->save();

            Storage::disk('ftp')->makeDirectory(str_replace('\\', '/', $document->FILEPATH));
        }

        return response()->json([
            'success' => $result
        ]);
    }

    protected function moveSingleDocument($document, $current_directory, $dest_directory) {
        $oldFile = str_replace('\\', '/', $document->FILEPATH);

        $newFile = str_replace(str_replace('\\', '/', $current_directory), '', $oldFile);
        $newFile = ltrim($newFile, '/');
        $newFile = (($dest_directory != '') ? str_replace('\\', '/', $dest_directory) . '/' : '') . $newFile;

        try {
            // Create directory if not exists
            if (dirname($newFile) != '.') {
                Storage::disk('ftp')->makeDirectory(dirname($newFile));
            }

            // Move
            Storage::disk('ftp')->move($oldFile, $newFile);

            $document->FILEPATH = str_replace('/', '\\', $newFile);
            $document->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function move(Request $request)
    {
        $id = $request->get('id');
        $current_directory = $request->get('current_directory');
        $dest_directory = $request->get('dest_directory');
        $source_directory = $request->get('source_directory');

        $result = false;

        // Move entire directory
        if ($source_directory) {
            $documents = Document::where(function($query) use ($source_directory) {
                    $query->where('DIRECTORY', 'like', $source_directory . '%');
                    if ($source_directory == '') {
                        $query->orWhereNull('DIRECTORY');
                    }
                })
                ->where('FILETYPE', '<>', 'dir')
                ->get();

            foreach ($documents as $document) {
                $result = $this->moveSingleDocument($document, $current_directory, $dest_directory);
            }

        }
        // Move single file
        else if ($id > 0) {
            $document = Document::find($id);
            $result = $this->moveSingleDocument($document, $current_directory, $dest_directory);
        }

        return response()->json([
            'success' => $result
        ]);
    }

    protected function renameSingleDocument($document, $dest_filename) {
        $oldFile = str_replace('\\', '/', $document->FILEPATH);

        $fileInfo = pathinfo($oldFile);
        $newFile = $fileInfo['dirname'] . '/' . $dest_filename . '.' . $fileInfo['extension'];

        try {
            // Create directory if not exists
            if (dirname($newFile) != '.') {
                Storage::disk('ftp')->makeDirectory(dirname($newFile));
            }

            // Move to rename
            Storage::disk('ftp')->move($oldFile, $newFile);

            $document->FILEPATH = str_replace('/', '\\', $newFile);
            $document->TITLE = $dest_filename;
            $document->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function rename(Request $request)
    {
        $selected_document_id = $request->get('selected_document_id');
        $selected_document_folder = $request->get('selected_document_folder');
        $selected_document_type = $request->get('selected_document_type');

        $current_directory = $request->get('current_directory');
        $new_filename = rtrim($request->get('new_filename'), '.');

        $result = false;

        // Rename entire directory
        if ($selected_document_type == 'dir') {
            $documents = Document::where(function($query) use ($selected_document_folder) {
                    $query->where('DIRECTORY', 'like', $selected_document_folder . '%');
                    $query->orWhere('FILEPATH', $selected_document_folder);
                    if ($selected_document_folder == '') {
                        $query->orWhereNull('DIRECTORY');
                    }
                })
                ->get();

            foreach ($documents as $document) {
                $oldFile = str_replace('\\', '/', $document->FILEPATH);

                $newDir = $current_directory . '\\' . $new_filename;
                $newFile = str_replace('\\', '/', str_replace($selected_document_folder, $newDir, $document->FILEPATH));

                try {
                    // Create directory if not exists
                    if (dirname($newFile) != '.') {
                        Storage::disk('ftp')->makeDirectory(dirname($newFile));
                    }

                    // Move to rename
                    if ($document->FILETYPE != 'dir') {
                        Storage::disk('ftp')->move($oldFile, $newFile);
                    }

                    if (($document->FILEPATH == $selected_document_folder) && ($document->FILETYPE == 'dir')) {
                        $document->TITLE = $new_filename;
                    }
                    $document->FILEPATH = str_replace('/', '\\', $newFile);
                    $document->save();

                    $result = true;
                } catch (\Exception $e) {
                    $result = false;
                }
            }
        }
        // Rename single file
        else if ($selected_document_id > 0) {
            $document = Document::find($selected_document_id);
            $result = $this->renameSingleDocument($document, $new_filename);
        }

        return response()->json([
            'success' => $result
        ]);
    }
}