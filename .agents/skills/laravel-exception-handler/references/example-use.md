namespace App\Services;

use App\Exceptions\AppException;
use App\Enums\ErrorCode;

class OrderService
{
public function createOrder(int $userId)
    {
        $cart = $this->cartRepository->findByUserId($userId);

        if (!$cart || $cart->items->isEmpty()) {
            // Ném lỗi bằng Enum! Cực kỳ tường minh!
            throw new AppException(ErrorCode::CART_IS_EMPTY);
        }

        // ...
    }

}
