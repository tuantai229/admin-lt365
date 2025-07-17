<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure we have users with IDs 1-20
        $userIds = User::whereBetween('id', [1, 20])->pluck('id')->toArray();
        
        if (empty($userIds)) {
            $this->command->error('No users found with IDs 1-20. Please run UserSeeder first.');
            return;
        }

        // Create 15 root comments for document with ID 1
        $rootComments = [];
        for ($i = 1; $i <= 15; $i++) {
            $comment = Comment::factory()->create([
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'document',
                'type_id' => 1,
                'parent_id' => 0,
                'content' => $this->getRandomComment(),
                'status' => rand(0, 1) ? 1 : 0, // Mix of approved and pending
            ]);
            
            $rootComments[] = $comment;
        }

        // Create 5 reply comments (replies to some root comments)
        $replyCount = 5;
        for ($i = 1; $i <= $replyCount; $i++) {
            $parentComment = $rootComments[array_rand($rootComments)];
            
            Comment::factory()->create([
                'user_id' => $userIds[array_rand($userIds)],
                'type' => 'document',
                'type_id' => 1,
                'parent_id' => $parentComment->id,
                'content' => $this->getRandomReply(),
                'status' => rand(0, 1) ? 1 : 0, // Mix of approved and pending
            ]);
        }

        $this->command->info('Created 20 comments (15 root + 5 replies) for document ID 1');
    }

    /**
     * Get random comment content
     */
    private function getRandomComment(): string
    {
        $comments = [
            'Tài liệu này rất hữu ích cho việc học tập và ôn thi.',
            'Cảm ơn admin đã chia sẻ tài liệu chất lượng.',
            'Nội dung được trình bày rõ ràng và dễ hiểu.',
            'Tài liệu được cập nhật theo chương trình mới nhất.',
            'Rất phù hợp cho học sinh lớp 12 ôn thi tốt nghiệp.',
            'Chất lượng tài liệu tốt, download về học ngay.',
            'Cần thêm một số bài tập thực hành nữa sẽ hoàn hảo.',
            'Tài liệu chi tiết và bám sát chương trình học.',
            'Phần lý thuyết rõ ràng, ví dụ minh họa dễ hiểu.',
            'Cảm ơn tác giả đã biên soạn tài liệu hay như vậy.',
            'Tài liệu cần thiết cho kỳ thi sắp tới.',
            'Nội dung đầy đủ và được sắp xếp khoa học.',
            'Rất hữu ích cho việc tự học ở nhà.',
            'Tài liệu hay, download về nghiên cứu thêm.',
            'Chất lượng tốt, đáng để tham khảo và học tập.',
        ];

        return $comments[array_rand($comments)];
    }

    /**
     * Get random reply content
     */
    private function getRandomReply(): string
    {
        $replies = [
            'Cảm ơn bạn đã chia sẻ!',
            'Mình cũng đồng ý với bạn.',
            'Có thể bạn chia sẻ thêm kinh nghiệm không?',
            'Đúng vậy, tài liệu rất chất lượng.',
            'Cảm ơn góp ý của bạn!',
            'Mình cũng đang tìm hiểu về vấn đề này.',
            'Bạn có thể giải thích rõ hơn không?',
            'Cảm ơn bạn đã bổ sung thông tin.',
            'Ý kiến của bạn rất có giá trị.',
            'Mình hoàn toàn đồng tình với bạn.',
        ];

        return $replies[array_rand($replies)];
    }
}
