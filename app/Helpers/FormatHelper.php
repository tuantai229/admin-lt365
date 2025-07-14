<?php

namespace App\Helpers;

use Carbon\Carbon;

class FormatHelper
{
    /**
     * Format tiền VND
     */
    public static function currency(int|float $amount): string
    {
        if ($amount == 0) {
            return 'Miễn phí';
        }
        
        return number_format($amount) . ' VNĐ';
    }

    /**
     * Format file size
     */
    public static function fileSize(int $bytes): string
    {
        if ($bytes == 0) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Format ngày tháng tiếng Việt
     */
    public static function date(string|Carbon $date, string $format = 'd/m/Y'): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $date->format($format);
    }

    /**
     * Format datetime tiếng Việt
     */
    public static function datetime(string|Carbon $datetime, string $format = 'd/m/Y H:i'): string
    {
        if (is_string($datetime)) {
            $datetime = Carbon::parse($datetime);
        }
        
        return $datetime->format($format);
    }

    /**
     * Thời gian tương đối (2 giờ trước, 3 ngày trước)
     */
    public static function timeAgo(string|Carbon $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        // Set locale Vietnamese for Carbon
        Carbon::setLocale('vi');
        
        return $date->diffForHumans();
    }

    /**
     * Format số điện thoại
     */
    public static function phone(string $phone): string
    {
        // Loại bỏ ký tự không phải số
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format theo kiểu VN: 0xxx xxx xxx
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
        }
        
        return $phone;
    }

    /**
     * Format lượt xem
     */
    public static function viewCount(int $count): string
    {
        if ($count < 1000) {
            return $count . ' lượt xem';
        }
        
        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K lượt xem';
        }
        
        return round($count / 1000000, 1) . 'M lượt xem';
    }

    /**
     * Format download count
     */
    public static function downloadCount(int $count): string
    {
        if ($count < 1000) {
            return $count . ' lượt tải';
        }
        
        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K lượt tải';
        }
        
        return round($count / 1000000, 1) . 'M lượt tải';
    }

    /**
     * Format rating (1-5 stars)
     */
    public static function rating(float $rating, int $maxStars = 5): string
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = $maxStars - $fullStars - $halfStar;
        
        $result = str_repeat('★', $fullStars);
        if ($halfStar) {
            $result .= '☆';
        }
        $result .= str_repeat('☆', $emptyStars);
        
        return $result . ' (' . number_format($rating, 1) . ')';
    }

    /**
     * Format percentage
     */
    public static function percentage(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals) . '%';
    }

    /**
     * Format exam year
     */
    public static function examYear(int $year): string
    {
        if ($year == 0) return 'Chưa xác định';
        
        return 'Năm ' . $year;
    }

    /**
     * Format email (ẩn một phần)
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) != 2) return $email;
        
        $username = $parts[0];
        $domain = $parts[1];
        
        $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
        
        return $maskedUsername . '@' . $domain;
    }

    /**
     * Format address ngắn gọn
     */
    public static function shortAddress(string $address, int $length = 50): string
    {
        return StringHelper::truncate($address, $length);
    }

    /**
     * Format số thứ tự
     */
    public static function ordinal(int $number): string
    {
        return 'Thứ ' . $number;
    }
}