<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tạo 1 user demo với thông tin cố định
        User::create([
            'email' => 'user@demo.com',
            'password' => Hash::make('password'),
            'phone' => '0901234567',
            'full_name' => 'Nguyễn Văn Demo',
            'gender' => 'male',
            'date_of_birth' => '1990-01-15',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'bio' => 'Tài khoản demo cho hệ thống LT365',
            'status' => 1,
            'last_login_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Tạo một số user với thông tin tiếng Việt cụ thể
        $specificUsers = [
            [
                'email' => 'nguyenvannam@gmail.com',
                'full_name' => 'Nguyễn Văn Nam',
                'gender' => 'male',
                'phone' => '0901111111',
                'date_of_birth' => '1985-05-20',
                'address' => 'Số 45 Nguyễn Huệ, Quận 1, TP.HCM',
                'bio' => 'Phụ huynh có con đang học lớp 6, quan tâm đến việc thi chuyển cấp THPT.',
            ],
            [
                'email' => 'tranthimai@gmail.com',
                'full_name' => 'Trần Thị Mai',
                'gender' => 'female',
                'phone' => '0902222222',
                'date_of_birth' => '1988-08-10',
                'address' => 'Số 123 Lê Lợi, Quận 3, TP.HCM',
                'bio' => 'Giáo viên Toán, muốn chia sẻ kinh nghiệm và tài liệu giảng dạy.',
            ],
            [
                'email' => 'levanhoang@gmail.com',
                'full_name' => 'Lê Văn Hoàng',
                'gender' => 'male',
                'phone' => '0903333333',
                'date_of_birth' => '1992-12-03',
                'address' => 'Số 789 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
                'bio' => 'Chuyên gia tư vấn giáo dục, hỗ trợ phụ huynh chọn trường phù hợp.',
            ],
            [
                'email' => 'phamthilinh@gmail.com',
                'full_name' => 'Phạm Thị Linh',
                'gender' => 'female',
                'phone' => '0904444444',
                'date_of_birth' => '1983-07-25',
                'address' => 'Số 456 Võ Văn Tần, Quận 3, TP.HCM',
                'bio' => 'Phụ huynh có 2 con, con lớn đang chuẩn bị thi vào lớp 10.',
            ],
            [
                'email' => 'vuthanhduc@gmail.com',
                'full_name' => 'Vũ Thành Đức',
                'gender' => 'male',
                'phone' => '0905555555',
                'date_of_birth' => '1987-11-18',
                'address' => 'Số 321 Cách Mạng Tháng 8, Quận 10, TP.HCM',
                'bio' => 'Giám đốc trung tâm gia sư, chuyên luyện thi chuyển cấp.',
            ],
            [
                'email' => 'dothiha@gmail.com',
                'full_name' => 'Đỗ Thị Hà',
                'gender' => 'female',
                'phone' => '0906666666',
                'date_of_birth' => '1990-04-14',
                'address' => 'Số 654 Nguyễn Thị Minh Khai, Quận 1, TP.HCM',
                'bio' => 'Cô giáo Văn, có kinh nghiệm luyện thi vào lớp 10 chuyên Văn.',
            ],
            [
                'email' => 'buivanlam@gmail.com',
                'full_name' => 'Bùi Văn Lâm',
                'gender' => 'male',
                'phone' => '0907777777',
                'date_of_birth' => '1984-02-28',
                'address' => 'Số 987 Lý Tự Trọng, Quận 1, TP.HCM',
                'bio' => 'Phụ huynh quan tâm đến giáo dục sớm, con đang học mẫu giáo.',
            ],
            [
                'email' => 'hoangthituyet@gmail.com',
                'full_name' => 'Hoàng Thị Tuyết',
                'gender' => 'female',
                'phone' => '0908888888',
                'date_of_birth' => '1991-09-12',
                'address' => 'Số 147 Pasteur, Quận 1, TP.HCM',
                'bio' => 'Giáo viên Tiếng Anh, chuyên luyện thi chuyển cấp cho học sinh.',
            ],
            [
                'email' => 'nguyenminhtan@gmail.com',
                'full_name' => 'Nguyễn Minh Tân',
                'gender' => 'male',
                'phone' => '0909999999',
                'date_of_birth' => '1989-06-07',
                'address' => 'Số 258 Hai Bà Trưng, Quận 1, TP.HCM',
                'bio' => 'Nhà tâm lý học giáo dục, tư vấn về stress thi cử cho học sinh.',
            ],
            [
                'email' => 'trinhthihong@gmail.com',
                'full_name' => 'Trịnh Thị Hồng',
                'gender' => 'female',
                'phone' => '0900000000',
                'date_of_birth' => '1986-03-22',
                'address' => 'Số 369 Trần Hưng Đạo, Quận 5, TP.HCM',
                'bio' => 'Chủ nhiệm lớp 9, có kinh nghiệm hướng nghiệp cho học sinh.',
            ]
        ];

        foreach ($specificUsers as $userData) {
            User::create([
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'phone' => $userData['phone'],
                'full_name' => $userData['full_name'],
                'gender' => $userData['gender'],
                'date_of_birth' => $userData['date_of_birth'],
                'address' => $userData['address'],
                'bio' => $userData['bio'],
                'status' => 1,
                'last_login_at' => fake()->optional(0.8)->dateTimeBetween('-30 days', 'now'),
                'email_verified_at' => fake()->optional(0.9)->dateTimeBetween('-1 year', 'now'),
            ]);
        }

        // Tạo thêm 8 user ngẫu nhiên để đủ 20 user
        User::factory()
            ->count(8)
            ->withVietnamesePhone()
            ->create();

        // Tạo một số user với trạng thái đặc biệt
        User::factory()
            ->count(2)
            ->unverified()
            ->inactive()
            ->create();

        // Tạo user đã đăng ký gần đây
        User::factory()
            ->count(3)
            ->recent()
            ->active()
            ->create();

        $this->command->info('Đã tạo thành công 25 users (20 chính + 5 bổ sung)');
        $this->command->info('Tài khoản demo: user@demo.com / password');
        $this->command->info('Các tài khoản khác sử dụng mật khẩu: password');
    }
}