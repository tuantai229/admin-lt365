<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'type' => 'document',
            'type_id' => 1, // Document with ID 1
            'parent_id' => 0, // Root comment by default
            'content' => $this->faker->paragraphs(rand(1, 3), true),
            'status' => $this->faker->boolean(80) ? 1 : 0, // 80% approved (1), 20% pending (0)
        ];
    }

    /**
     * Indicate that the comment is a reply to another comment.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function reply($parentId)
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return [
                'parent_id' => $parentId,
                'content' => $this->faker->sentences(rand(1, 2), true),
            ];
        });
    }

    /**
     * Indicate that the comment is pending approval.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0,
            ];
        });
    }

    /**
     * Indicate that the comment is approved.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1,
            ];
        });
    }

    /**
     * Indicate that the comment is hidden.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function hidden()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 2,
            ];
        });
    }
}
