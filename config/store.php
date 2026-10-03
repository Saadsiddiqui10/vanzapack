<?php

return [
    'name' => 'VanzaPack',
    'tagline' => 'Sustainable packaging & food-service supplies',
    'currency' => env('STORE_CURRENCY', 'AED'),
    'email' => 'contact@vanzapack.test',
    'phone' => '+971 52 399 3759',
    'landline' => '04-3235340',
    'address' => 'Warehouse 17-20, 22nd Street, Al Quoz Industrial Area 3, Dubai',
    'business_hours' => 'Monday - Saturday : 9:00AM - 6:00PM',

    'whatsapp_country_code' => env('STORE_WHATSAPP_COUNTRY_CODE', '971'),
    'whatsapp_phone' => env('STORE_WHATSAPP_PHONE', '523993759'),
    'whatsapp_default_message' => 'Hello VanzaPack, I have a question about your products.',
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
        'linkedin' => 'https://linkedin.com',
        'x' => 'https://x.com',
    ],

    'google_analytics_id' => env('STORE_GA_ID', ''),
    'maintenance_mode' => false,
];
