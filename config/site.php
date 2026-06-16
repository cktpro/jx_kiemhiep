<?php

/*
|--------------------------------------------------------------------------
| Site config - tương đương ClassHeader1.cs (constants) trong project cũ
|--------------------------------------------------------------------------
*/

return [

    // Link hướng dẫn tải game (mặc định / fallback)
    'link_tai_game' => env('SITE_LINK_TAI_GAME', '/tin-tuc/[Huong-dan]-tai-game-va-cai-dat.2069.aspx'),

    // Link tải theo từng nền tảng (linkAdV4 = Android, linkAdV6 = mặc định, linkIOS1 = iOS)
    'link_download_android' => env('SITE_LINK_DOWNLOAD_APK', '/client/jx1SHXT_v2003.apk'),
    'link_download_ios' => env('SITE_LINK_DOWNLOAD_IOS', 'https://testflight.apple.com/join/bnBteSwC'),
    'link_download_default' => env('SITE_LINK_DOWNLOAD_APK', '/client/jx1SHXT_v2003.apk'),

    // Mạng xã hội / cộng đồng
    'link_facebook' => env('SITE_LINK_FACEBOOK', 'https://www.facebook.com/sonhaxatacmobile2005'),
    'link_zalo' => env('SITE_LINK_ZALO', 'https://zalo.me/g/jmgpiy387'),
    'link_tiktok' => env('SITE_LINK_TIKTOK', 'https://www.tiktok.com/@pkvolamvietmobi'),
    // (tính năng mới, không có trong code gốc - trước đây icon Youtube ở jxAside dùng tạm link_facebook)
    'link_youtube' => env('SITE_LINK_YOUTUBE', 'https://www.facebook.com/sonhaxatacmobile2005'),

    // Số điện thoại nhận tin nhắn đổi SĐT / quên mật khẩu (OTP qua SMS)
    'phone_otp' => env('SITE_PHONE_OTP', '0931486731'),

    // Độ dài tối đa cho tài khoản / mật khẩu
    'max_acc_len' => (int) env('SITE_MAX_ACC_LEN', 16),
    'min_acc_len' => 6,

    // Khóa bí mật dùng để ký cookie "auth" (HMAC-SHA256, giống GetHmacHash trong ClassHeader1)
    'secret_key' => env('SITE_SECRET_KEY', 'sonhaxatac_key'),

    // Văn bản footer
    'footer1' => env('SITE_FOOTER1', 'PkVoLamViet.Mobi - Võ Lâm Truyền Kỳ 1 Mobile'),
    'footer2' => env('SITE_FOOTER2', 'Võ Lâm Truyền Kỳ 1 Mobile - Sơn Hà Xã Tắc'),

    // Thời gian sống của cookie "auth" (giờ)
    'auth_cookie_hours' => 24,
];
