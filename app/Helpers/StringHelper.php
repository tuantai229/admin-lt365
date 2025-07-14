<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper
{
    /**
     * Chuyển tiêu đề thành slug (hỗ trợ tiếng Việt)
     */
    public static function createSlug(string $title): string
    {
        // Chuyển các ký tự tiếng Việt thành không dấu
        $title = self::removeVietnameseAccents($title);
        
        // Sử dụng Laravel Str helper
        return Str::slug($title);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    public static function removeVietnameseAccents(string $str): string
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];

        $replacements = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'I', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'D'
        ];

        return str_replace($accents, $replacements, $str);
    }

    /**
     * Cắt chuỗi và thêm "..."
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Cắt chuỗi theo từ (không cắt giữa từ)
     */
    public static function truncateWords(string $text, int $words = 20, string $suffix = '...'): string
    {
        $wordArray = explode(' ', $text);
        
        if (count($wordArray) <= $words) {
            return $text;
        }

        return implode(' ', array_slice($wordArray, 0, $words)) . $suffix;
    }

    /**
     * Tạo excerpt từ nội dung
     */
    public static function excerpt(string $content, int $length = 200): string
    {
        // Loại bỏ HTML tags trước
        $text = strip_tags($content);
        
        // Cắt theo độ dài
        return self::truncate($text, $length);
    }

    /**
     * Chuyển số thành chuỗi có định dạng (1000 => 1,000)
     */
    public static function formatNumber(int|float $number): string
    {
        return number_format($number);
    }

    /**
     * Tạo random string
     */
    public static function random(int $length = 10): string
    {
        return Str::random($length);
    }

    /**
     * Chuyển snake_case thành Title Case
     */
    public static function titleCase(string $value): string
    {
        return Str::title(str_replace('_', ' ', $value));
    }

    /**
     * Kiểm tra chuỗi có chứa từ khóa không (không phân biệt hoa thường)
     */
    public static function contains(string $haystack, string $needle): bool
    {
        return Str::contains(strtolower($haystack), strtolower($needle));
    }
}