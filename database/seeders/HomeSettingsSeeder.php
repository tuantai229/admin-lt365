<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class HomeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero Banner Slides
        $heroSlides = [
            [
                'title' => 'Đồng hành cùng con vào trường chuyên',
                'description' => 'Cung cấp tài liệu, kinh nghiệm và tư vấn chuyên sâu giúp học sinh và phụ huynh tự tin vượt qua kỳ thi chuyển cấp',
                'image' => 'html/images/0185df70dbcee70ff37980a67a92ce64.jpg',
                'button1_text' => 'Tìm tài liệu',
                'button1_url' => '/tai-lieu',
                'button1_color_class' => 'bg-white text-primary',
                'button2_text' => 'Đăng ký tư vấn',
                'button2_url' => '/lien-he',
                'button2_color_class' => 'bg-secondary text-white'
            ],
            [
                'title' => 'Học cùng thầy giỏi – vào trường chuyên dễ dàng hơn',
                'description' => 'Hơn 50 trung tâm & giáo viên luyện thi chuyển cấp uy tín, được phụ huynh tin chọn trên toàn quốc.',
                'image' => 'html/images/slide-giaovien.png',
                'button1_text' => 'Tìm giáo viên phù hợp',
                'button1_url' => '/giao-vien',
                'button1_color_class' => 'bg-white text-green-600',
                'button2_text' => 'Xem trung tâm',
                'button2_url' => '/trung-tam',
                'button2_color_class' => 'bg-secondary text-white'
            ],
            [
                'title' => 'Chọn đúng trường, đúng hướng đi',
                'description' => 'Chuyên gia tư vấn giáo dục LT365 giúp phụ huynh lựa chọn trường phù hợp nhất với năng lực và sở thích của con.',
                'image' => 'html/images/slide-tuvan.png',
                'button1_text' => 'Đăng ký tư vấn 1-1',
                'button1_url' => '/tu-van',
                'button1_color_class' => 'bg-white text-blue-600',
                'button2_text' => 'Xem trường phù hợp',
                'button2_url' => '/truong-hoc',
                'button2_color_class' => 'bg-secondary text-white'
            ],
            [
                'title' => 'Cập nhật lịch thi mới nhất năm 2025',
                'description' => 'Theo dõi lịch thi, chỉ tiêu tuyển sinh và thông báo quan trọng từ các trường chuyên, trường top.',
                'image' => 'html/images/slide-lichthi.png',
                'button1_text' => 'Xem lịch thi 2025',
                'button1_url' => '/lich-thi',
                'button1_color_class' => 'bg-white text-purple-600',
                'button2_text' => 'Nhận thông báo qua email',
                'button2_url' => '/dang-ky-nhan-tin',
                'button2_color_class' => 'bg-secondary text-white'
            ]
        ];

        Setting::set('home_hero_slides', $heroSlides);

        // Quick Transfer Section
        $quickTransfer = [
            'title' => 'Đồng hành cùng con vào trường chuyên',
            'boxes' => [
                [
                    'title' => 'Thi vào lớp 1',
                    'image' => 'html/images/dc78c9c0887200a40954cba8e72a3499.jpg',
                    'description' => "5 trường tiểu học hàng đầu Hà Nội\nLịch thi tuyển sinh năm 2025-2026\nBộ đề luyện thi mẫu cập nhật",
                    'button_url' => '/thi-vao-lop-1'
                ],
                [
                    'title' => 'Thi vào lớp 6',
                    'image' => 'html/images/3d7d5e0502820a5e09cf3fb76caa9d88.jpg',
                    'description' => "Top trường THCS chất lượng cao\nCấu trúc đề thi các môn năm 2025\nTài liệu luyện thi chuyên sâu",
                    'button_url' => '/thi-vao-lop-6'
                ],
                [
                    'title' => 'Thi vào lớp 10',
                    'image' => 'html/images/2ea343b800b7ca44c1844291afa997e9.jpg',
                    'description' => "Trường chuyên & trường top THPT\nĐiểm chuẩn 3 năm gần nhất\nĐề thi thử & đáp án chi tiết",
                    'button_url' => '/thi-vao-lop-10'
                ]
            ]
        ];

        Setting::set('home_quick_transfer', $quickTransfer);

        // News Schedule (assuming first active news category)
        $newsSchedule = [
            'selected_category_id' => 1 // You might need to adjust this based on your actual news categories
        ];

        Setting::set('home_news_schedule', $newsSchedule);

        // Teachers & Centers (empty arrays - will be configured by admin)
        $teachersCenters = [
            'centers' => [],
            'teachers' => []
        ];

        Setting::set('home_teachers_centers', $teachersCenters);

        // Stats & Reviews
        $statsReviews = [
            'stats' => [
                'documents' => '10,000+',
                'schools' => '500+',
                'members' => '50,000+',
                'rating' => '4.8/5'
            ],
            'reviews' => [
                [
                    'name' => 'Chị Nguyễn Thị Hà',
                    'avatar' => 'html/images/ef17c185dacb5a2bbf309f78e126433f.jpg',
                    'rating' => 5,
                    'review_content' => 'Tôi rất hài lòng với tài liệu ôn thi vào lớp 1 của LT365. Con tôi đã vượt qua kỳ thi vào trường Tiểu học Thăng Long một cách dễ dàng. Cảm ơn đội ngũ LT365 rất nhiều!'
                ],
                [
                    'name' => 'Anh Trần Minh Đức',
                    'avatar' => 'html/images/c6aa794681e3876e3fffebc450fdafda.jpg',
                    'rating' => 4.5,
                    'review_content' => 'Tư vấn chọn trường của LT365 rất hữu ích. Nhờ có sự hướng dẫn chi tiết, gia đình tôi đã chọn được trường THCS phù hợp nhất cho con. Đội ngũ tư vấn rất nhiệt tình và chuyên nghiệp.'
                ],
                [
                    'name' => 'Chị Lê Thị Hồng Nhung',
                    'avatar' => 'html/images/9dd31202e9f05056bfbfb5ef7cbdea35.jpg',
                    'rating' => 4,
                    'review_content' => 'Đề thi thử vào lớp 10 của LT365 rất sát với đề thi thật. Con tôi đã luyện tập với bộ đề này và đạt kết quả cao trong kỳ thi vào trường THPT chuyên. Rất đáng để thử!'
                ]
            ]
        ];

        Setting::set('home_stats_reviews', $statsReviews);

        $this->command->info('✅ Home settings seeded successfully!');
        $this->command->info('📄 Hero slides: ' . count($heroSlides) . ' slides');
        $this->command->info('📚 Quick transfer boxes: ' . count($quickTransfer['boxes']) . ' boxes');
        $this->command->info('⭐ Parent reviews: ' . count($statsReviews['reviews']) . ' reviews');
    }
}
