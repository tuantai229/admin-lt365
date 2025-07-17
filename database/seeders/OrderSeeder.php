<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $document = Document::find(1);

        if (!$document) {
            $this->command->warn('Document with ID=1 not found. Skipping OrderSeeder.');
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $price = $document->price ?? 100000;
            $totalAmount = $price;

            $order = Order::create([
                'user_id' => $users->random()->id,
                'total_amount' => $totalAmount,
                'status' => ['pending', 'paid', 'cancelled'][array_rand(['pending', 'paid', 'cancelled'])],
                'payment_method' => ['cod', 'bank_transfer', 'vnpay'][array_rand(['cod', 'bank_transfer', 'vnpay'])],
                'payment_status' => ['pending', 'paid', 'failed'][array_rand(['pending', 'paid', 'failed'])],
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'document_id' => $document->id,
                'price' => $price,
            ]);
        }
    }
}
