<?php

return [
    'name' => 'VanzaPack',
    'tagline' => 'Sustainable packaging & food-service supplies',
    'currency' => env('STORE_CURRENCY', 'AED'),
    'email' => 'info@vanzapack.com',
    'sales_email' => 'sales@vanzapack.com',
    'support_email' => 'support@vanzapack.com',
    'phone' => '+971 4 262 7225',
    'landline' => '+971 4 262 7225',
    'address' => 'Down Town Jebel Ali St 19, JAFZA View, 1st Floor Tower 18, Dubai, United Arab Emirates',
    'business_hours' => 'Monday - Saturday : 9:00AM - 6:00PM',

    'whatsapp_country_code' => env('STORE_WHATSAPP_COUNTRY_CODE', '971'),
    'whatsapp_phone' => env('STORE_WHATSAPP_PHONE', '505021026'),
    'whatsapp_default_message' => 'Hello, Good Day. I would like to connect with the Vanza Pack Web Sales Team regarding your products and services',
    'whatsapp_product_enabled' => true,
    'whatsapp_cart_enabled' => true,
    'whatsapp_order_enabled' => true,

    'order_prefix' => 'ORD',

    'free_shipping_threshold' => 99,
    'default_tax_rate' => 5, // % VAT

    'social' => [
        'facebook' => 'https://facebook.com',
        'instagram' => 'https://instagram.com',
        'tiktok' => 'https://tiktok.com',
        'youtube' => 'https://youtube.com',
        'linkedin' => 'https://www.linkedin.com/in/vanza-pack-852122441/',
        'x' => 'https://x.com',
    ],

    'google_analytics_id' => env('STORE_GA_ID', ''),
    'maintenance_mode' => false,
];
