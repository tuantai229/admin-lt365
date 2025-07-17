<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;

Route::get('/', function () {
    return view('welcome');
});
// Route download để admin có thể test download:
Route::get('/admin/documents/{document}/download', function(Document $document) {
    if (!$document->hasFile()) {
        abort(404, 'File không tồn tại');
    }
    
    $filePath = $document->file_path;
    $fileName = $document->name . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    
    return Storage::disk('public')->download($filePath, $fileName);
})->name('documents.download');