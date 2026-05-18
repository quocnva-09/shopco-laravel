<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->command->info('Clearing old data in Reviews table...');
        Review::truncate();
        Schema::enableForeignKeyConstraints();

        // Mảng chứa các câu bình luận mẫu cho sinh động
        $goodComments = [
            'Sản phẩm tuyệt vời, chất lượng vượt mong đợi!',
            'Giao hàng nhanh, đóng gói cẩn thận. Mặc rất vừa vặn.',
            'Đúng như mô tả, màu sắc đẹp, chất vải êm ái.',
            'Đáng đồng tiền bát gạo, sẽ tiếp tục ủng hộ shop.',
            'Form dáng cực chuẩn, shop tư vấn nhiệt tình.'
        ];

        $badComments = [
            'Hơi thất vọng về chất liệu, không như mình nghĩ.',
            'Giao hàng hơi chậm, hộp bị móp méo.',
            'Kích thước bị lệch một chút so với bảng size.',
            'Tạm ổn trong tầm giá, nhưng cần cải thiện đường may.'
        ];

        // 1. TẠO REVIEW THẬT TỪ NGƯỜI ĐÃ MUA HÀNG (Đơn hàng Paid)
        $orderItems = OrderItem::with('order')->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->get();

        $this->command->info('Creating reviews from real customers...');

        foreach ($orderItems as $item) {
            if (rand(1, 100) <= 60) {
                $rating = rand(3, 5);
                $comment = $rating >= 4
                    ? $goodComments[array_rand($goodComments)]
                    : $badComments[array_rand($badComments)];

                Review::create([
                    'product_id' => $item->product_id,
                    'user_id' => $item->order->user_id,
                    'order_item_id' => $item->id,
                    'rating' => $rating,
                    'comment' => (rand(1, 10) > 2) ? $comment : null,
                    'is_approved' => (rand(1, 10) > 1),
                ]);
            }
        }

        // 2. TẠO REVIEW ẨN DANH (Mô phỏng User/Order đã bị xóa)
        $this->command->info('Creating anonymous reviews (orphaned customers)...');

        $randomProducts = Product::inRandomOrder()->limit(15)->get();

        foreach ($randomProducts as $product) {
            $anonymousReviewCount = rand(1, 3);

            for ($i = 0; $i < $anonymousReviewCount; $i++) {
                $rating = rand(1, 5);
                $comment = $rating >= 4
                    ? 'Mình mua từ năm ngoái, nay mới nhớ vào đánh giá. Áo dùng vẫn rất tốt, chưa bị phai màu.'
                    : 'Sản phẩm mua đợt sale trước, chất lượng hơi tệ nên mình vứt rồi.';

                Review::create([
                    'product_id' => $product->id,
                    'user_id' => null,
                    'order_item_id' => null,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_approved' => true,
                ]);
            }
        }

        $this->command->info('Review seeding completed!');
    }
}