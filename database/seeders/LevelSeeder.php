<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            // Cấp học chính
            ['name' => 'Mầm non', 'slug' => 'mam-non', 'parent_id' => 0, 'status' => 1, 'sort_order' => 1],
            ['name' => 'Tiểu học', 'slug' => 'tieu-hoc', 'parent_id' => 0, 'status' => 1, 'sort_order' => 2],
            ['name' => 'Trung học cơ sở', 'slug' => 'trung-hoc-co-so', 'parent_id' => 0, 'status' => 1, 'sort_order' => 3],
            ['name' => 'Trung học phổ thông', 'slug' => 'trung-hoc-pho-thong', 'parent_id' => 0, 'status' => 1, 'sort_order' => 4],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }

        // Thêm các lớp con cho Tiểu học
        $tieuHoc = Level::where('slug', 'tieu-hoc')->first();
        if ($tieuHoc) {
            $lopTieuHoc = [
                ['name' => 'Lớp 1', 'slug' => 'lop-1', 'parent_id' => $tieuHoc->id, 'status' => 1, 'sort_order' => 1],
                ['name' => 'Lớp 2', 'slug' => 'lop-2', 'parent_id' => $tieuHoc->id, 'status' => 1, 'sort_order' => 2],
                ['name' => 'Lớp 3', 'slug' => 'lop-3', 'parent_id' => $tieuHoc->id, 'status' => 1, 'sort_order' => 3],
                ['name' => 'Lớp 4', 'slug' => 'lop-4', 'parent_id' => $tieuHoc->id, 'status' => 1, 'sort_order' => 4],
                ['name' => 'Lớp 5', 'slug' => 'lop-5', 'parent_id' => $tieuHoc->id, 'status' => 1, 'sort_order' => 5],
            ];

            foreach ($lopTieuHoc as $lop) {
                Level::create($lop);
            }
        }

        // Thêm các lớp con cho THCS
        $thcs = Level::where('slug', 'trung-hoc-co-so')->first();
        if ($thcs) {
            $lopThcs = [
                ['name' => 'Lớp 6', 'slug' => 'lop-6', 'parent_id' => $thcs->id, 'status' => 1, 'sort_order' => 1],
                ['name' => 'Lớp 7', 'slug' => 'lop-7', 'parent_id' => $thcs->id, 'status' => 1, 'sort_order' => 2],
                ['name' => 'Lớp 8', 'slug' => 'lop-8', 'parent_id' => $thcs->id, 'status' => 1, 'sort_order' => 3],
                ['name' => 'Lớp 9', 'slug' => 'lop-9', 'parent_id' => $thcs->id, 'status' => 1, 'sort_order' => 4],
            ];

            foreach ($lopThcs as $lop) {
                Level::create($lop);
            }
        }

        // Thêm các lớp con cho THPT
        $thpt = Level::where('slug', 'trung-hoc-pho-thong')->first();
        if ($thpt) {
            $lopThpt = [
                ['name' => 'Lớp 10', 'slug' => 'lop-10', 'parent_id' => $thpt->id, 'status' => 1, 'sort_order' => 1],
                ['name' => 'Lớp 11', 'slug' => 'lop-11', 'parent_id' => $thpt->id, 'status' => 1, 'sort_order' => 2],
                ['name' => 'Lớp 12', 'slug' => 'lop-12', 'parent_id' => $thpt->id, 'status' => 1, 'sort_order' => 3],
            ];

            foreach ($lopThpt as $lop) {
                Level::create($lop);
            }
        }
    }
}