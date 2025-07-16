<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'name' => 'Chính sách bảo mật',
                'slug' => 'chinh-sach-bao-mat',
                'content' => '<h1>Chính sách bảo mật</h1><p>Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn...</p>',
                'status' => 1,
            ],
            [
                'name' => 'Điều khoản sử dụng',
                'slug' => 'dieu-khoan-su-dung',
                'content' => '<h1>Điều khoản sử dụng</h1><p>Khi sử dụng trang web này, bạn đồng ý tuân thủ các điều khoản sau...</p>',
                'status' => 1,
            ],
            [
                'name' => 'Về chúng tôi',
                'slug' => 've-chung-toi',
                'content' => '<h1>Về chúng tôi</h1><p>Chúng tôi là một đơn vị cung cấp dịch vụ giáo dục trực tuyến...</p>',
                'status' => 1,
            ],
            [
                'name' => 'Liên hệ',
                'slug' => 'lien-he',
                'content' => '<h1>Liên hệ</h1><p>Để liên hệ với chúng tôi, vui lòng sử dụng thông tin sau...</p>',
                'status' => 1,
            ],
            [
                'name' => 'Hướng dẫn sử dụng',
                'slug' => 'huong-dan-su-dung',
                'content' => '<h1>Hướng dẫn sử dụng</h1><p>Hướng dẫn chi tiết cách sử dụng website...</p>',
                'status' => 0,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
