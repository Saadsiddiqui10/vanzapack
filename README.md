# VanzaPack — Laravel 12 E-Commerce Platform

VanzaPack is a production-ready e-commerce application for a packaging &
food-service supplies brand, built with **Laravel 12 + MySQL + Blade + Tailwind**.
It ships with a full storefront, a role-based admin panel, cart/checkout/orders,
inventory, coupons, wishlist, reviews, shipping, tax, WhatsApp integration, SEO,
transactional email/notification architecture, and an automated test suite.

---

## 1. Requirements

| Tool      | Version                        |
|-----------|--------------------------------|
| PHP       | 8.2+ (with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, `gd`) |
| Composer  | 2.x                            |
| MySQL     | 8.0+ **or** MariaDB 10.4+      |
| Node.js   | 18+ (20/22/24 fine)            |
| NPM       | 9+                             |

Windows + **Laragon** is the target environment. XAMPP / WAMP / `php artisan serve`
also work. Docker is **not** required.

---

## 2. Quick start (Laragon)

```text
1.  Copy this project folder into  C:\laragon\www\vanzapack
2.  Start Laragon → Start All (Apache + MySQL)
3.  Open Laragon → Menu → MySQL → phpMyAdmin (or HeidiSQL) and create a database:
        CREATE DATABASE vanzapack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
4.  Open a terminal in the project folder (Laragon → Menu → Terminal) and run:

        composer install
        copy .env.example .env
        php artisan key:generate
        php artisan migrate --seed
        php artisan storage:link
        npm install
        npm run build

5.  Laragon → Menu → Apache → Reload  (creates the vanzapack.test vhost)
6.  Visit  http://vanzapack.test
```

If `vanzapack.test` does not resolve, enable Laragon's **Auto virtual hosts**
(Preferences → General) or add `127.0.0.1 vanzapack.test` to
`C:\Windows\System32\drivers\etc\hosts`, then reload Apache.

The app also runs at `http://localhost/vanzapack/public` and via
`php artisan serve` → `http://127.0.0.1:8000`.

---

## 3. Environment configuration

`.env` (already prepared by `.env.example`):

```dotenv
APP_NAME="VanzaPack"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://vanzapack.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vanzapack
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log        # transactional mail is written to storage/logs/laravel.log
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

# Store defaults (also editable in Admin → Settings)
STORE_WHATSAPP_COUNTRY_CODE=971
STORE_WHATSAPP_PHONE=523993759
STORE_CURRENCY=AED
```

For real email during development, point `MAIL_*` at
[Mailpit](https://github.com/axllent/mailpit) (bundled with Laragon) or an SMTP host.

---

## 4. Demo accounts (created by the seeder)

| Role              | Email                        | Password         |
|-------------------|------------------------------|------------------|
| Super Admin       | `admin@vanzapack.test`      | `ChangeMe@12345` |
| Manager           | `manager@vanzapack.test`    | `Password@123`   |
| Sales Manager     | `sales@vanzapack.test`      | `Password@123`   |
| Inventory Manager | `inventory@vanzapack.test`  | `Password@123`   |
| Customer          | `customer@vanzapack.test`   | `Password@123`   |

> **Change the admin password immediately in production.** New admin users created
> from the panel are flagged `must_change_password` and prompted on their dashboard.
> To rotate the seeded owner credential, edit `database/seeders/UserSeeder.php`
> **or** run `php artisan tinker` →
> `User::where('email','admin@vanzapack.test')->first()->update(['password'=>bcrypt('YOUR-NEW-PASSWORD')]);`

Admin panel: **`/admin`**  ·  Customer area: **`/account`**

---

## 5. What's included

**Storefront** — home (hero slider, categories, deals, best sellers, testimonials),
shop with live sidebar filters (category / brand / attribute / price / rating /
availability / sale) + 7 sort modes + pagination, product detail (image gallery,
variant matrix with live price/stock, tabs, reviews, WhatsApp enquiry), category &
brand pages, autocomplete search, cart drawer + full cart page, coupons, multi-step
checkout (guest + authenticated), order confirmation, wishlist, CMS pages, contact
form, newsletter.

**Customer account** — dashboard, orders + order detail (cancel / reorder),
addresses CRUD, wishlist, product reviews (with photo upload), notifications,
profile & password.

**Admin panel** — dashboard with revenue/status charts; CRUD for products
(+ image management), categories (nested), brands, attributes, coupons, banners,
CMS pages, shipping methods, tax rates, admin users, roles & permissions; order
management (status workflow with automatic stock restock on cancel, fulfilment,
printable invoice); customers; review moderation; inventory adjustments with
movement history; grouped store settings; sales reports with date presets; CSV
exports (orders / customers / products / inventory); activity log; newsletter;
contact messages.

**Platform** — role-based access control (6 roles, granular permissions),
Form Request validation, policies/gates, PHP enums, service layer
(`Cart`, `Coupon`, `Shipping`, `Tax`, `Checkout`, `Inventory`, `WhatsApp`,
`ProductQuery`, `Settings`), pluggable payment gateway architecture
(`PaymentGateway` interface + `PaymentManager` + COD / Bank Transfer / Manual /
Online-stub), database notifications + Mailables, SEO (per-entity meta/OG/Twitter,
JSON-LD Product/Organization/Breadcrumb, `/sitemap.xml`, `/robots.txt`),
styled error pages (403/404/419/429/500/503), activity logging.

---

## 6. Common commands

```bash
php artisan migrate:fresh --seed     # rebuild DB with demo data
php artisan test                     # run the test suite (SQLite in-memory)
php artisan db:seed --class=CatalogSeeder
npm run dev                          # Vite dev server with HMR
npm run build                        # production asset build
php artisan queue:work               # process queued mail/notifications
php artisan storage:link             # expose storage/app/public → public/storage
php artisan optimize:clear           # clear all caches
```

---

## 7. Payment gateways

Payment logic is decoupled behind `App\Services\Payments\PaymentGateway`.
Enabled gateways are read from **Admin → Settings → Payment**. Cash on Delivery
and Bank Transfer are enabled by default; `OnlineGateway` is a stub that redirects
to a mock success page — replace `OnlineGateway::process()` with a real hosted
checkout (Stripe / Telr / Network International / PayTabs) plus a webhook route,
then register it (it is already registered in `PaymentManager`).

---

## 8. WhatsApp integration

Configure the number and toggles in **Admin → Settings → WhatsApp**.
The `whatsapp_link()` helper and `WhatsAppService` build `https://wa.me/…` URLs
with a pre-filled message on:

- product pages (product name, SKU, variant, quantity, price, URL),
- the cart drawer & cart page (entire cart),
- the order confirmation & account order pages,
- a floating support button site-wide.

No private API credentials are used or exposed — these are public click-to-chat links.

---

## 9. Production deployment

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain
```

- Generate a fresh `APP_KEY` (`php artisan key:generate`).
- `composer install --no-dev --optimize-autoloader`
- `php artisan config:cache route:cache view:cache`
- `npm run build`
- `php artisan migrate --force`
- Ensure `storage/` and `bootstrap/cache/` are writable by the web server.
- Run the queue worker (`php artisan queue:work` via Supervisor/systemd) for mail.
- Add the scheduler cron: `* * * * * php /path/artisan schedule:run >> /dev/null 2>&1`
- Configure real `MAIL_*` (SMTP) and set `SESSION_SECURE_COOKIE=true` behind HTTPS.
- Back up the MySQL database and `storage/app/public` regularly.

---

## 10. Project layout

```
app/
  Enums/            OrderStatus, PaymentStatus, PaymentMethod, UserRole, …
  Http/Controllers/ (storefront)  +  Http/Controllers/Admin/ (panel)
  Http/Middleware/  EnsureUserIsStaff, EnsurePermission, ShareStorefrontData
  Http/Requests/    PlaceOrderRequest, …
  Models/           ~30 Eloquent models
  Observers/        OrderObserver (status history + notifications)
  Services/         domain services  +  Services/Payments/ (gateway architecture)
  Support/          helpers.php (settings/money/whatsapp_link), ActivityLogger, Notify
database/
  migrations/       ~30 migrations
  seeders/          Role/Setting/Catalog/Logistics/Coupon/Cms/User/Review/Order
  factories/
resources/views/
  components/        storefront-layout, account-layout, admin-layout, product-card, …
  storefront/  account/  admin/  errors/  seo/
routes/             web.php  +  admin.php  +  auth.php
tests/              Feature/ + Unit/
```

See `BUILD_PROGRESS.md` for the phase-by-phase build log.
