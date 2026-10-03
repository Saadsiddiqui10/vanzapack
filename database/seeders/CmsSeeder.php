<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $t, string $bg = '000066', string $fg = 'ffffff') => "https://placehold.co/1600x640/{$bg}/{$fg}?text=".rawurlencode($t);

        Banner::insert([
            [
                'placement' => 'hero', 'title' => 'Your Destination for Sustainable Packaging',
                'subtitle' => 'From bagasse tableware to kraft takeaway — everything your kitchen needs, in one place.',
                'image' => $img('VanzaPack — Sustainable Packaging'), 'mobile_image' => null,
                'cta_label' => 'Shop Now', 'cta_url' => '/shop',
                'is_active' => true, 'position' => 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'placement' => 'hero', 'title' => 'Your Brand, Your Packaging',
                'subtitle' => 'Custom-printed cups, boxes and bags made for your business.',
                'image' => $img('Custom Printed Packaging', '95C93F', '000066'), 'mobile_image' => null,
                'cta_label' => 'Explore Collection', 'cta_url' => '/shop?sort=latest',
                'is_active' => true, 'position' => 2, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'placement' => 'promo', 'title' => 'Mega Deals — Limited Time',
                'subtitle' => 'Up to 30% off best-selling cups, bags and tissue.',
                'image' => $img('Mega Deals', '95C93F', '000066'), 'mobile_image' => null,
                'cta_label' => 'View Offers', 'cta_url' => '/offers',
                'is_active' => true, 'position' => 1, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        $pages = [
            ['About Us', '<p>VanzaPack is a UAE-based supplier and manufacturer of premium food packaging, hygiene products, cleaning solutions and grocery essentials. We serve restaurants, hotels, cafes, retailers and businesses with high-quality, reliable and sustainable solutions.</p><p>With a commitment to quality, innovation and customer satisfaction, VanzaPack ensures you get the best products, competitive prices and exceptional service every time.</p>'],
            ['Contact', '<p>Reach the VanzaPack team Monday to Saturday, 9:00AM - 6:00PM.</p>'],
            ['Privacy Policy', '<p>This Privacy Policy explains how VanzaPack collects, uses and protects your personal information when you use our website and services.</p>'.self::loremSections()],
            ['Terms & Conditions', '<p>By accessing and placing an order with VanzaPack you agree to the terms set out below.</p>'.self::loremSections()],
            ['Return Policy', '<p>Unopened cases in original condition may be returned within 7 days of delivery. Refunds are processed to the original payment method within 14 days.</p>'.self::loremSections()],
            ['Shipping & Delivery', '<p>We deliver across the UAE within 2-4 business days. Orders above AED 99 qualify for free standard delivery.</p>'.self::loremSections()],
            ['FAQs', '<p>Answers to the questions we hear most often about ordering, delivery, customisation and returns.</p>'.self::loremSections()],
        ];

        foreach ($pages as [$title, $content]) {
            Page::create([
                'title' => $title,
                'content' => $content,
                'meta_title' => $title.' | VanzaPack',
                'meta_description' => 'VanzaPack — '.$title,
                'is_published' => true,
            ]);
        }

        foreach (range(1, 8) as $i) {
            NewsletterSubscriber::create(['email' => "subscriber{$i}@example.com"]);
        }

        // Feature the first brands in the homepage "Trusted Brands" strip.
        \App\Models\Brand::query()->where('is_active', true)->orderBy('name')->take(8)->get()
            ->each(fn ($brand, $i) => $brand->update(['is_featured' => true, 'position' => $i + 1]));
    }

    private static function loremSections(): string
    {
        $out = '';
        foreach (range(1, 4) as $i) {
            $out .= "<h3>Section {$i}</h3><p>".fake()->paragraphs(2, true).'</p>';
        }

        return $out;
    }
}
