# VanzaPack — Build Progress

Brand: **VanzaPack** — eco packaging & food-service supplies (primary `#95C93F`, secondary `#000066`).
Stack: Laravel 12, PHP 8.2, MySQL/MariaDB, Blade + Tailwind (Breeze), Vite.
Local: XAMPP for PHP/MySQL; target Laragon host `http://vanzapack.test`. DB name `vanzapack`.

## Accounts
- Super Admin: `admin@vanzapack.test` / `ChangeMe@12345`
- Manager: `manager@vanzapack.test` / `Password@123`
- Sales: `sales@vanzapack.test` / `Password@123`
- Inventory: `inventory@vanzapack.test` / `Password@123`
- Customer: `customer@vanzapack.test` / `Password@123`

## Phases
- [x] **A — Domain**: enums, migrations (30+ tables), models, factories, seeders. `migrate:fresh --seed` green.
- [x] **B — Services & routing**: Cart/Coupon/Shipping/Tax/Inventory/Checkout/WhatsApp/ProductQuery services, payment gateway architecture, route map, staff/permission middleware, Blade layout shell.
- [x] **C — Storefront**: home, shop (filters/sort/paginate), product detail (+variants, gallery, reviews, WhatsApp), category, brand, search + autocomplete, cart (drawer + page), checkout, confirmation, CMS pages, contact.
- [x] **D — Checkout & account**: multi-step checkout, order placement + notifications, account dashboard/orders/addresses/profile, wishlist, review submission, guest-cart merge on login.
- [x] **E — Admin panel**: dashboard + charts, product/category/brand/attribute/coupon/banner/page/shipping/tax/user/role CRUD, order workflow + invoice, customers, review moderation, inventory adjust + history, settings, reports, CSV exports, activity log, newsletter, messages.
- [x] **F — Cross-cutting**: SEO (meta/OG/Twitter/JSON-LD, sitemap.xml, robots.txt), notifications + Mailables, styled error pages (403/404/419/429/500/503).
- [x] **G — Tests & docs**: 37 tests pass (`php artisan test`), README + Laragon/production guide.

## Status: COMPLETE — all 7 phases delivered, tests green, storefront + admin smoke-tested.

## Notes
- Product images use `https://placehold.co` URLs (models detect `http` prefix). Replaceable via admin upload to `storage/app/public`.
- `settings()` helper reads DB settings with `config/store.php` fallback.
