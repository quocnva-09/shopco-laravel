<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    private const MIN_REVIEWS_PER_PRODUCT = 10;

    /** Bình luận tốt */
    private array $goodComments = [
        'Sản phẩm tuyệt vời, chất lượng vượt mong đợi!',
        'Giao hàng nhanh, đóng gói cẩn thận. Mặc rất vừa vặn.',
        'Đúng như mô tả, màu sắc đẹp, chất vải êm ái.',
        'Đáng đồng tiền bát gạo, sẽ tiếp tục ủng hộ shop.',
        'Form dáng cực chuẩn, shop tư vấn nhiệt tình.',
        "As a UI/UX enthusiast, I value simplicity and functionality. This t-shirt not only represents those principles but also feels great to wear.",
        "I absolutely love this t-shirt! The design is unique and the fabric feels so comfortable.",
        "This t-shirt is a must-have for anyone who appreciates good design. The fit is perfect.",
        "I'm not just wearing a t-shirt; I'm wearing a piece of design philosophy.",
    ];

    /** Bình luận tiêu cực */
    private array $badComments = [
        'Hơi thất vọng về chất liệu, không như mình nghĩ.',
        'Giao hàng hơi chậm, hộp bị móp méo.',
        'Kích thước bị lệch một chút so với bảng size.',
        'Tạm ổn trong tầm giá, nhưng cần cải thiện đường may.',
        "The print quality is not as vibrant as it appears in the photos. It looks faded after one wash.",
        "I was expecting a more premium fabric for the price. This feels quite cheap.",
        "The color in the picture was misleading. In reality, it's a much duller shade.",
        "Itchy tag on the neck ruined the experience.",
    ];

    /** Tên khách mẫu cho guest review */
    private array $guestNames = [
        'Nguyen Van A', 'Tran Thi B', 'Le Van C', 'Pham Thi D',
        'Hoang Van E', 'Do Thi F', 'Bui Van G', 'Vo Thi H',
        'Ngo Van I', 'Dang Thi K', 'Ly Van L', 'Mai Thi M',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->command->info('Clearing old data in Reviews table...');
        Review::truncate();
        Schema::enableForeignKeyConstraints();

        // ── Phase 1: Review từ người đã mua hàng (60% order items) ──────────
        $this->command->info('Phase 1: Creating reviews from real customers...');

        $orderItems = OrderItem::with('order')
            ->whereHas('order', fn($q) => $q->where('status', 'paid'))
            ->get();

        foreach ($orderItems as $item) {
            if (rand(1, 100) <= 60) {
                $rating  = rand(3, 5);
                $comment = $this->pickComment($rating);

                // Review được viết 1-14 ngày sau khi nhận hàng
                $orderDate    = Carbon::parse($item->created_at);
                $reviewedAt   = $orderDate->copy()->addDays(rand(1, 14));

                Review::create([
                    'product_id'    => $item->product_id,
                    'user_id'       => $item->order->user_id,
                    'order_item_id' => $item->id,
                    'rating'        => $rating,
                    'comment'       => (rand(1, 10) > 1) ? $comment : null,
                    'is_approved'   => (rand(1, 10) > 1),
                    'created_at'    => $reviewedAt,
                    'updated_at'    => $reviewedAt,
                ]);
            }
        }

        // ── Phase 2: Đảm bảo mỗi product có ít nhất MIN_REVIEWS_PER_PRODUCT ─
        $this->command->info(
            'Phase 2: Guaranteeing ' . self::MIN_REVIEWS_PER_PRODUCT . ' reviews per product...'
        );

        $products = Product::all();

        foreach ($products as $product) {
            $existing = Review::where('product_id', $product->id)->count();
            $needed   = self::MIN_REVIEWS_PER_PRODUCT - $existing;

            for ($i = 0; $i < $needed; $i++) {
                $this->createGuestReview($product->id);
            }
        }

        $this->command->info(
            'Review seeding done! '
            . Review::count() . ' reviews created.'
        );
    }

    private function createGuestReview(int $productId): void
    {
        $guestName  = $this->guestNames[array_rand($this->guestNames)];
        $guestEmail = strtolower(
            str_replace(' ', '.', $guestName) . rand(10, 99) . '@example.com'
        );
        $rating  = rand(1, 5);
        $comment = $this->pickComment($rating);

        // Guest review rải ngẫu nhiên trong 30-180 ngày trước
        $reviewedAt = Carbon::now()->subDays(rand(30, 180));

        Review::create([
            'product_id'    => $productId,
            'user_id'       => null,
            'order_item_id' => null,
            'guest_name'    => $guestName,
            'guest_email'   => $guestEmail,
            'rating'        => $rating,
            'comment'       => $comment,
            'is_approved'   => true,
            'created_at'    => $reviewedAt,
            'updated_at'    => $reviewedAt,
        ]);
    }

    private function pickComment(int $rating): string
    {
        return $rating >= 4
            ? $this->goodComments[array_rand($this->goodComments)]
            : $this->badComments[array_rand($this->badComments)];
    }
}