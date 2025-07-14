<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Upload file và trả về path
     */
    public static function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $filename = self::sanitizeFilename($filename);
        
        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Upload image với resize (cần intervention/image package)
     */
    public static function uploadImage(UploadedFile $file, string $directory = 'images', ?int $width = null, ?int $height = null): string
    {
        $filename = time() . '_' . StringHelper::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;
        
        // Nếu cần resize (cần cài intervention/image)
        // $image = Image::make($file);
        // if ($width && $height) {
        //     $image->resize($width, $height, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        // }
        // Storage::disk('public')->put($path, $image->encode());
        
        // Upload trực tiếp
        Storage::disk('public')->putFileAs($directory, $file, $filename);
        
        return $path;
    }

    /**
     * Xóa file
     */
    public static function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Làm sạch tên file
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Loại bỏ dấu tiếng Việt
        $filename = StringHelper::removeVietnameseAccents($filename);
        
        // Loại bỏ ký tự đặc biệt, chỉ giữ chữ cái, số, dấu chấm, gạch ngang
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Loại bỏ dấu gạch dưới liên tiếp
        $filename = preg_replace('/_+/', '_', $filename);
        
        return trim($filename, '_');
    }

    /**
     * Lấy extension file
     */
    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Kiểm tra file có phải image không
     */
    public static function isImage(string $filename): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(self::getExtension($filename), $imageExtensions);
    }

    /**
     * Kiểm tra file có phải document không
     */
    public static function isDocument(string $filename): bool
    {
        $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        return in_array(self::getExtension($filename), $docExtensions);
    }

    /**
     * Lấy icon cho file type
     */
    public static function getFileIcon(string $filename): string
    {
        $extension = self::getExtension($filename);
        
        return match($extension) {
            'pdf' => 'fa-file-pdf',
            'doc', 'docx' => 'fa-file-word',
            'xls', 'xlsx' => 'fa-file-excel',
            'ppt', 'pptx' => 'fa-file-powerpoint',
            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
            'mp4', 'avi', 'mov' => 'fa-file-video',
            'mp3', 'wav' => 'fa-file-audio',
            'zip', 'rar', '7z' => 'fa-file-archive',
            default => 'fa-file'
        };
    }

    /**
     * Tạo URL public cho file
     */
    public static function url(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Kiểm tra file tồn tại
     */
    public static function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Lấy size file
     */
    public static function size(string $path): int
    {
        return Storage::disk('public')->size($path);
    }

    /**
     * Download file
     */
    public static function download(string $path, ?string $name = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk('public')->download($path, $name);
    }

    /**
     * Tạo thumbnail cho image
     */
    public static function createThumbnail(string $imagePath, int $width = 300, int $height = 300): string
    {
        // Cần intervention/image package
        // $thumbnailPath = 'thumbnails/' . basename($imagePath);
        // $image = Image::make(Storage::disk('public')->path($imagePath));
        // $image->resize($width, $height, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // Storage::disk('public')->put($thumbnailPath, $image->encode());
        // return $thumbnailPath;
        
        return $imagePath; // Fallback
    }
}