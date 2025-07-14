<?php

namespace App\Helpers;

class StatusHelper
{
    // General status constants (TINYINT)
    const DRAFT = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;

    /**
     * General status cho content tables (documents, news, schools, levels, etc.)
     */
    public static function getGeneralStatusOptions(): array
    {
        return [
            self::DRAFT => 'Nháp',
            self::ACTIVE => 'Kích hoạt',
            self::INACTIVE => 'Không kích hoạt',
        ];
    }

    /**
     * User status (TINYINT) - users, admin_users
     */
    public static function getUserStatusOptions(): array
    {
        return [
            0 => 'Chờ kích hoạt',
            1 => 'Đang hoạt động',
            2 => 'Tạm khóa',
        ];
    }

    /**
     * Contact status (TINYINT) - contacts
     */
    public static function getContactStatusOptions(): array
    {
        return [
            0 => 'Chưa xử lý',
            1 => 'Đã xử lý',
            2 => 'Đã từ chối',
        ];
    }

    /**
     * Newsletter status (TINYINT) - newsletters
     */
    public static function getNewsletterStatusOptions(): array
    {
        return [
            0 => 'Chưa xác nhận',
            1 => 'Đã đăng ký',
            2 => 'Đã hủy',
        ];
    }

    /**
     * Rating status (TINYINT) - ratings
     */
    public static function getRatingStatusOptions(): array
    {
        return [
            0 => 'Chờ duyệt',
            1 => 'Đã duyệt',
            2 => 'Bị từ chối',
        ];
    }

    /**
     * Comment status (TINYINT) - comments
     */
    public static function getCommentStatusOptions(): array
    {
        return [
            0 => 'Chờ duyệt',
            1 => 'Đã duyệt',
            2 => 'Bị từ chối',
        ];
    }

    /**
     * Order status (ENUM) - orders
     */
    public static function getOrderStatusOptions(): array
    {
        return [
            'pending' => 'Chờ xử lý',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
        ];
    }

    /**
     * Payment status (ENUM) - orders
     */
    public static function getPaymentStatusOptions(): array
    {
        return [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
        ];
    }

    /**
     * Gender options (ENUM) - users
     */
    public static function getGenderOptions(): array
    {
        return [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];
    }

    // Helper methods
    public static function getGeneralStatusLabel(int $status): string
    {
        return self::getGeneralStatusOptions()[$status] ?? 'Không xác định';
    }

    public static function getUserStatusLabel(int $status): string
    {
        return self::getUserStatusOptions()[$status] ?? 'Không xác định';
    }

    public static function getContactStatusLabel(int $status): string
    {
        return self::getContactStatusOptions()[$status] ?? 'Không xác định';
    }

    public static function getOrderStatusLabel(string $status): string
    {
        return self::getOrderStatusOptions()[$status] ?? $status;
    }

    public static function getPaymentStatusLabel(string $status): string
    {
        return self::getPaymentStatusOptions()[$status] ?? $status;
    }

    public static function getGenderLabel(string $gender): string
    {
        return self::getGenderOptions()[$gender] ?? $gender;
    }

    // Status check methods
    public static function isGeneralActive(int $status): bool
    {
        return $status === self::ACTIVE;
    }

    public static function isGeneralDraft(int $status): bool
    {
        return $status === self::DRAFT;
    }

    public static function isUserActive(int $status): bool
    {
        return $status === 1;
    }

    public static function isOrderPaid(string $status): bool
    {
        return $status === 'paid';
    }

    public static function isPaymentCompleted(string $status): bool
    {
        return $status === 'paid';
    }

    // Color mapping for UI (Filament badges)
    public static function getGeneralStatusColor(int $status): string
    {
        return match($status) {
            self::DRAFT => 'warning',
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            default => 'secondary'
        };
    }

    public static function getOrderStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'paid' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public static function getPaymentStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }
}