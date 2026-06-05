<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'product_not_found' => 'Product not found',
    'cart_not_found' => 'Cart not found',
    'cart_item_not_found' => 'Cart item not found',
    'cart_item_not_found_or_denied' => 'Cart item not found or access denied.',
    'cart_empty_create_order' => 'Cart is empty. Cannot create order.',
    'user_not_found' => 'User not found',
    'review_not_found' => 'Review not found',
    'order_not_found' => 'Order not found',
    'category_not_found' => 'Category not found',
    'invalid_credentials' => 'Invalid credentials',
    'invalid_otp' => 'Invalid OTP',
    'export_not_ready' => 'Export is not ready for download.',
    'export_file_not_found' => 'Export file not found.',
    'review_unpurchased_product' => 'You can only review products you have purchased and paid for.',
    'upload_failed' => 'Upload failed',
    'logout_failed' => 'Logout failed',
    'forget_password_failed' => 'Forget password failed',
    'verify_otp_failed' => 'Verify otp failed',

    // Guest Checkout
    'guest_checkout_product_not_found' => 'Product ID :id does not exist.',

    // Guest Review
    'guest_review_order_not_paid' => 'Reviews are only allowed for paid orders.',
    'guest_review_order_already_reviewed' => 'This order has already been reviewed.',
    'guest_review_product_not_in_order' => 'This product does not belong to the specified order.',
];
