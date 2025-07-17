<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female', 'other']);
        $firstName = $gender === 'female' 
            ? fake('vi_VN')->firstNameFemale() 
            : fake('vi_VN')->firstNameMale();
        $lastName = fake('vi_VN')->lastName();
        $fullName = $lastName . ' ' . $firstName;

        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'full_name' => $fullName,
            'avatar' => fake()->optional(0.3)->imageUrl(200, 200, 'people'),
            'date_of_birth' => fake()->optional(0.7)->dateTimeBetween('-60 years', '-16 years')?->format('Y-m-d'),
            'gender' => $gender,
            'address' => fake()->optional(0.6)->address(),
            'bio' => fake()->optional(0.4)->paragraph(2),
            'status' => fake()->randomElement([0, 1, 1, 1]), // 75% active
            'last_login_at' => fake()->optional(0.8)->dateTimeBetween('-30 days', 'now'),
            'email_verified_at' => fake()->optional(0.9)->dateTimeBetween('-1 year', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the user should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    /**
     * Indicate that the user should be male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
            'full_name' => fake('vi_VN')->lastName() . ' ' . fake('vi_VN')->firstNameMale(),
        ]);
    }

    /**
     * Indicate that the user should be female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
            'full_name' => fake('vi_VN')->lastName() . ' ' . fake('vi_VN')->firstNameFemale(),
        ]);
    }

    /**
     * Create a user with Vietnamese phone number format.
     */
    public function withVietnamesePhone(): static
    {
        $prefixes = ['090', '091', '094', '083', '084', '085', '081', '082', '032', '033', '034', '035', '036', '037', '038', '039'];
        
        return $this->state(fn (array $attributes) => [
            'phone' => fake()->randomElement($prefixes) . fake()->numerify('#######'),
        ]);
    }

    /**
     * Create a recently registered user.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'email_verified_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'last_login_at' => fake()->optional(0.9)->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}