<?php

namespace App\Helpers;

class HtmlHelper
{
    /**
     * Xóa tất cả HTML tags
     */
    public static function stripTags(string $html): string
    {
        return strip_tags($html);
    }

    /**
     * Xóa HTML tags nhưng giữ lại một số tags cần thiết
     */
    public static function stripTagsExcept(string $html, array $allowedTags = ['p', 'br', 'strong', 'em']): string
    {
        $allowed = '<' . implode('><', $allowedTags) . '>';
        return strip_tags($html, $allowed);
    }

    /**
     * Làm sạch HTML (loại bỏ script, style, attributes nguy hiểm)
     */
    public static function clean(string $html): string
    {
        // Loại bỏ script tags
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Loại bỏ style tags
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        
        // Loại bỏ javascript: links
        $html = preg_replace('/javascript:/i', '', $html);
        
        // Loại bỏ on* attributes (onclick, onload, etc.)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        return $html;
    }

    /**
     * Tạo excerpt từ HTML content
     */
    public static function excerpt(string $html, int $length = 200, string $suffix = '...'): string
    {
        $text = self::stripTags($html);
        return StringHelper::truncate($text, $length, $suffix);
    }

    /**
     * Đếm số từ trong HTML
     */
    public static function wordCount(string $html): int
    {
        $text = self::stripTags($html);
        return str_word_count($text);
    }

    /**
     * Ước tính thời gian đọc (words per minute)
     */
    public static function readingTime(string $html, int $wordsPerMinute = 200): int
    {
        $words = self::wordCount($html);
        return max(1, round($words / $wordsPerMinute));
    }

    /**
     * Chuyển HTML thành plain text với format đẹp
     */
    public static function toPlainText(string $html): string
    {
        // Thay thế các tags block bằng newline
        $html = preg_replace('/<\/(div|p|h[1-6]|li|blockquote)>/i', "\n", $html);
        $html = preg_replace('/<(br|hr)\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/li>/i', "\n", $html);
        
        // Loại bỏ tất cả HTML tags
        $text = strip_tags($html);
        
        // Loại bỏ khoảng trắng thừa
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        $text = trim($text);
        
        return $text;
    }

    /**
     * Tạo meta description từ HTML content
     */
    public static function metaDescription(string $html, int $length = 160): string
    {
        $text = self::toPlainText($html);
        return StringHelper::truncate($text, $length, '');
    }

    /**
     * Escape HTML để hiển thị an toàn
     */
    public static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Tìm và extract images từ HTML
     */
    public static function extractImages(string $html): array
    {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Tìm và extract links từ HTML
     */
    public static function extractLinks(string $html): array
    {
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $html, $matches);
        
        $links = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $links[] = [
                'url' => $matches[1][$i],
                'text' => strip_tags($matches[2][$i])
            ];
        }
        
        return $links;
    }

    /**
     * Thêm target="_blank" cho external links
     */
    public static function makeExternalLinksBlank(string $html, string $currentDomain = null): string
    {
        if (!$currentDomain) {
            $currentDomain = parse_url(config('app.url'), PHP_URL_HOST);
        }

        return preg_replace_callback(
            '/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i',
            function($matches) use ($currentDomain) {
                $url = $matches[2];
                $beforeHref = $matches[1];
                $afterHref = $matches[3];
                
                // Check if it's an external link
                $urlHost = parse_url($url, PHP_URL_HOST);
                if ($urlHost && $urlHost !== $currentDomain) {
                    // Add target="_blank" if not already present
                    if (!preg_match('/target\s*=/i', $beforeHref . $afterHref)) {
                        $afterHref .= ' target="_blank" rel="noopener"';
                    }
                }
                
                return '<a ' . $beforeHref . 'href="' . $url . '"' . $afterHref . '>';
            },
            $html
        );
    }
}