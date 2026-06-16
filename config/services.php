<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // SMS gateway dùng cho OTP đổi SĐT / quên mật khẩu (port từ chức năng gửi SMS cũ nếu có)
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'api_url' => env('SMS_API_URL'),
        'api_key' => env('SMS_API_KEY'),
        'brand_name' => env('SMS_BRAND_NAME'),
    ],

];
