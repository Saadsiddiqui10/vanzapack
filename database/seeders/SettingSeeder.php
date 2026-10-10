<?php

namespace Database\Seeders;

use App\Services\SettingsRepository;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        /** @var SettingsRepository $settings */
        $settings = app(SettingsRepository::class);

        $defaults = [
            ['store_name', 'VanzaPack', 'general', 'string'],
            ['store_tagline', 'Sustainable packaging & food-service supplies', 'general', 'string'],
            ['store_email', 'info@vanzapack.com', 'general', 'string'],
            ['store_sales_email', 'sales@vanzapack.com', 'general', 'string'],
            ['store_support_email', 'support@vanzapack.com', 'general', 'string'],
            ['notification_email', 'sales@vanzapack.com', 'general', 'string'],
            ['store_phone', '+971 4 262 7225', 'general', 'string'],
            ['store_landline', '+971 4 262 7225', 'general', 'string'],
            ['store_address', 'Down Town Jebel Ali St 19, JAFZA View, 1st Floor Tower 18, Dubai, United Arab Emirates', 'general', 'string'],
            ['business_hours', 'Monday - Saturday : 9:00AM - 6:00PM', 'general', 'string'],
            ['currency', 'AED', 'general', 'string'],
            ['timezone', 'Asia/Dubai', 'general', 'string'],
            ['maintenance_mode', false, 'general', 'bool'],

            ['order_prefix', 'ORD', 'store', 'string'],
            ['low_stock_threshold', 5, 'store', 'int'],
            ['allow_guest_checkout', true, 'store', 'bool'],
            ['free_shipping_threshold', 99, 'store', 'int'],

            ['whatsapp_country_code', '971', 'whatsapp', 'string'],
            ['whatsapp_phone', '505021026', 'whatsapp', 'string'],
            ['whatsapp_default_message', 'Hello, Good Day. I would like to connect with the Vanza Pack Web Sales Team regarding your products and services', 'whatsapp', 'string'],
            ['whatsapp_product_enabled', true, 'whatsapp', 'bool'],
            ['whatsapp_cart_enabled', true, 'whatsapp', 'bool'],
            ['whatsapp_order_enabled', true, 'whatsapp', 'bool'],

            ['payment_cod_enabled', true, 'payment', 'bool'],
            ['payment_bank_transfer_enabled', true, 'payment', 'bool'],
            ['payment_bank_details', "Bank: Emirates NBD\nAccount name: VanzaPack Supplies LLC\nIBAN: AE00 0000 0000 0000 0000 000", 'payment', 'string'],
            ['payment_manual_enabled', false, 'payment', 'bool'],
            ['payment_online_enabled', false, 'payment', 'bool'],

            ['tax_enabled', true, 'tax', 'bool'],
            ['prices_include_tax', false, 'tax', 'bool'],

            ['social_facebook', 'https://facebook.com', 'social', 'string'],
            ['social_instagram', 'https://instagram.com', 'social', 'string'],
            ['social_tiktok', 'https://tiktok.com', 'social', 'string'],
            ['social_youtube', 'https://youtube.com', 'social', 'string'],
            ['social_linkedin', 'https://www.linkedin.com/in/vanza-pack-852122441/', 'social', 'string'],
            ['social_x', 'https://x.com', 'social', 'string'],

            ['seo_default_title', 'VanzaPack — Sustainable Packaging & Food-Service Supplies', 'seo', 'string'],
            ['seo_default_description', 'VanzaPack supplies premium eco-friendly packaging, tableware, tissues and food-service essentials to restaurants, cafes and retailers across the UAE.', 'seo', 'string'],
            ['seo_default_keywords', 'packaging, bagasse, paper cups, food service, eco friendly, UAE', 'seo', 'string'],
            ['google_analytics_id', '', 'seo', 'string'],

            ['announcement_text', 'Enjoy Free Delivery on Orders Above AED 99  •  10% OFF your first order — code WELCOME10', 'general', 'string'],
        ];

        foreach ($defaults as [$key, $value, $group, $type]) {
            $settings->set($key, $value, $group, $type);
        }
    }
}
