# Artiva — Art that Speaks to You

A complete digital-artwork e-commerce application built with **Laravel 11, Blade, MySQL, Bootstrap 5, CSS and vanilla JavaScript**. Every page has its own Blade view, route, controller action and (where needed) its own stylesheet and script.

The storefront is a curated gallery: customers browse artwork and artist profiles, filter and sort the catalogue, save pieces to a wishlist, check out through a clearly labelled simulated payment gateway, and download the high-resolution master files from a private library. Administrators manage artists, the catalogue, categories, orders, customers and sales reports from a separate dashboard.

---

## 1. Requirements

| Requirement | Version |
|---|---|
| PHP | 8.2 or newer (with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`) |
| Composer | 2.x |
| MySQL / MariaDB | MySQL 8.0+ or MariaDB 10.4+ |
| Web browser | Any modern browser |

No Node.js or build step is required — Bootstrap 5, Bootstrap Icons and the Google Fonts (Montserrat, Lato, Playfair Display) are loaded from CDNs.

## Database connections and safe copies

The application uses the single connection selected by `DB_CONNECTION`. Registration, checkout, admin pages and Eloquent models all use that same default connection. Selecting SQLite does not import a MySQL database, and switching `DB_CONNECTION` does not move any records.

Use separate database-name variables when keeping both databases configured:

```dotenv
DB_CONNECTION=sqlite
DB_SQLITE_DATABASE=database/database.sqlite
DB_MYSQL_HOST=127.0.0.1
DB_MYSQL_PORT=3307
DB_MYSQL_DATABASE=Artiva
```

For XAMPP, use the port configured by MySQL (this installation listens on `3307`). Set `DB_MYSQL_DATABASE` to the actual existing MySQL schema name before selecting `DB_CONNECTION=mysql`; if needed, set `DB_MYSQL_USERNAME` and `DB_MYSQL_PASSWORD` locally without sharing them. The older `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` values remain compatibility fallbacks. After changing connection settings, run `php artisan config:clear` and `php artisan migrate:status`; do not use `migrate:fresh` on a database with records.

Before any database migration, take a separate MySQL dump and verify that it is non-empty and restorable. A MySQL dump is a backup, not a MySQL-to-SQLite conversion: create the SQLite schema from the Laravel migrations and copy data with a migration tool that preserves IDs, foreign keys, indexes and all application tables. Keep the original MySQL database untouched until the copied database has been checked. The project already contains a populated `database/database.sqlite`; do not overwrite it when creating another copy.

---

## 2. Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your environment file
cp .env.example .env          # Windows: copy .env.example .env

# 3. Generate the application key
php artisan key:generate

# 4. Create the database (MySQL example)
mysql -u root -p -e "CREATE DATABASE artiva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Point .env at your database
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=artiva
#    DB_USERNAME=root
#    DB_PASSWORD=your_password
#    APP_URL=http://localhost:8000

# 6. Create the tables and load demo data
php artisan migrate --seed

# 7. Expose preview images (storage/app/public -> public/storage)
php artisan storage:link

# 8. Run the application
php artisan serve
```

Open **http://localhost:8000**.

### Alternative: import the demo database directly

`database/artiva-demo.sql` contains the same demo data produced by the seeders. Import it instead of running `migrate --seed` if you prefer:

```bash
mysql -u root -p artiva < database/artiva-demo.sql
php artisan migrate             # applies newer tables such as customer reviews
php artisan storage:link
```

The seeded preview images and master files are generated automatically by the seeders. If you import the SQL file instead, run `php artisan db:seed --class=ArtworkSeeder` once so the image files exist, or simply run `php artisan migrate:fresh --seed`.

---

## 3. Demo accounts

| Role | Email | Password |
|---|---|---|
| Administrator | `admin@artiva.test` | `password` |
| Customer | `customer@artiva.test` | `password` |
| Customer | `mara@artiva.test` | `password` |
| Customer | `liam@artiva.test` | `password` |
| Customer | `aya@artiva.test` | `password` |

Seeded orders, payments, wishlist items and a populated library are created for these customers so the dashboard, reports and library pages have realistic data on first load.

---

## 4. Feature overview

### Public
- **Home** — hero, featured artwork, categories, latest arrivals, value propositions.
- **Explore artwork** — keyword search, category filter, price range, five sort orders, pagination.
- **Artist directory and profiles** — searchable artist cards, profile photos, biographies, specializations, portfolio/social links and linked artwork.
- **Artwork details** — large preview, linked artist profile, price, specs (format, resolution, file size, downloads), licence panel, add to cart / wishlist, related artwork, verified-purchase reviews and star ratings.
- **Category page** — category hero, artwork grid, sorting, pagination, other collections.
- **About** and **Contact** — marketing pages; the contact form stores messages in the database.

### Authentication
- Register, login, logout, remember-me, password reset (token + email).
- Role separation: `customer` and `admin`. Admins are redirected to the dashboard after login.

### Customer
- **Cart** — add, remove, clear, order summary (each artwork is a single personal-use licence).
- **Checkout** — billing details, three labelled sandbox payment methods, card preview/formatting, licence agreement, order creation inside a database transaction.
- **Order confirmation** — order and payment status; downloads remain locked until payment verification.
- **Wishlist** — toggle from any artwork card or detail page.
- **Reviews & ratings** — verified buyers can leave or update one 1–5 star review per artwork; public pages show the average rating and paginated feedback.
- **Order history / order details** — status filters, receipts, payment records, re-download.
- **Purchased library** — every owned artwork with verified download links.
- **Profile** — avatar upload, account details, password change, collection summary.

### Admin (`/admin`)
- **Dashboard** — revenue, orders, customers, catalogue metrics, six-month revenue chart, best sellers, recent orders, category distribution.
- **Artists** — searchable artist directory, profile photo uploads, biography/specialization/social links, and guarded deletion while artworks remain assigned.
- **Artworks** — searchable/filterable list, create, edit, delete (blocked when sales exist), separate public preview and private master-file uploads.
- **Categories** — create, edit, delete (blocked while artwork exists), visibility toggles.
- **Orders** — search, status filter, full order review, status updates (refunding marks the payment refunded).
- **Customers** — search, order counts, lifetime spend, individual customer profiles.
- **Sales reports** — date-range revenue report, monthly chart, top artwork, revenue by category, status mix, top customers.

### Managing artist profiles

1. Sign in as an administrator and open **Admin → Artists → Add artist**.
2. Enter the artist’s name and optional biography, specialization, portrait and portfolio/social links, then save.
3. Open **Admin → Artworks**, create or edit an artwork, choose the artist from the **Artist** selector and save.
4. Customers can open the public **Artists** directory or click an artist name on an artwork card/details page.

An artist profile cannot be deleted while artworks are assigned to it. Reassign those artworks first; this preserves artwork and sales history.

---

## 5. Project structure

```
artiva/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php          ArtworkController.php ArtistController.php
│   │   │   ├── CategoryController.php      CartController.php
│   │   │   ├── WishlistController.php      CheckoutController.php
│   │   │   ├── OrderController.php         LibraryController.php
│   │   │   ├── ProfileController.php       ContactController.php
│   │   │   ├── Auth/ (Login, Register, PasswordResetLink, NewPassword)
│   │   │   └── Admin/ (Dashboard, Artwork, Artist, Category, Order, Customer, Report)
│   │   └── Middleware/EnsureUserIsAdmin.php
│   ├── Models/  User, Artist, Category, Artwork, Cart, CartItem, Wishlist, Order, OrderItem, Payment, ContactMessage
│   ├── Providers/AppServiceProvider.php
│   └── Support/ helpers.php, SalesReport.php
├── config/ artiva.php, filesystems.php (explicit public/private disks)
├── database/
│   ├── migrations/ (users, artists, catalogue, checkout and supporting tables)
│   ├── seeders/    (Database, User, Category, Artwork, Order)
│   └── artiva-demo.sql
├── public/
│   ├── css/ app.css, home.css, artworks.css, artwork-details.css,
│   │        about.css, contact.css, auth.css, cart.css, checkout.css,
│   │        wishlist.css, orders.css, library.css, profile.css, artists.css, admin.css
│   ├── js/  app.js, home.js, artworks.js, cart.js, checkout.js, admin.js
│   └── images/ logo/, placeholders/
├── resources/views/
│   ├── layouts/ app, guest, admin
│   ├── components/ navbar, footer, artwork-card, alert, admin-sidebar
│   ├── home, about, contact
│   ├── artworks/ index, show        artists/ index, show        categories/ show
│   ├── auth/ login, register, forgot-password, reset-password
│   ├── cart/ index                  checkout/ index, success
│   ├── wishlist/ index              orders/ index, show
│   ├── library/ index               profile/ index
│   ├── admin/ dashboard + artworks/ + categories/ + orders/ + customers/ + reports/
│   └── errors/ 403, 404
├── routes/web.php
├── tests/Feature/ StorefrontTest, AuthTest, CheckoutTest
└── phpunit.xml
```

**Note on CSS:** shared components (navbar, cards, buttons, forms, summary card, CTA band, page shell) live in `app.css`; each page only loads the file with its own page-specific rules. There is no single monolithic stylesheet and no database work inside Blade views.

---

## 6. Database design

| Table | Purpose | Key relationships |
|---|---|---|
| `users` | customers and admins (`role` enum) | has many orders, cart, wishlist |
| `password_reset_tokens` | reset tokens | — |
| `categories` | catalogue categories | has many artworks |
| `artists` | artist profile, portrait and portfolio links | has many artworks |
| `artworks` | artwork listings, `preview_path` (public), `file_path` (private) | belongs to category and artist |
| `carts` | one cart per user | has many cart items |
| `cart_items` | unique artwork per cart | belongs to cart, artwork |
| `wishlists` | saved artwork per user (unique pair) | belongs to user, artwork |
| `orders` | totals, status, payment status, billing snapshot | belongs to user, has many items/payments |
| `order_items` | price/title snapshot at purchase time | belongs to order, artwork |
| `payments` | gateway reference, amount, status, JSON payload | belongs to order |
| `contact_messages` | contact form submissions | — |\n| `reviews` | verified-purchase ratings and comments | belongs to user and artwork; one review per user/artwork |

Checkout runs inside `DB::transaction()`: the order, its items and the payment row are written together, and the cart is only emptied after all of them succeed.

---

## 7. Security notes

- **Admin routes** are protected by `auth` + the `admin` middleware (`EnsureUserIsAdmin`); customers receive a 403 page.
- **Downloads** are authorised against the signed-in customer's paid order items — a user cannot download an artwork they have not bought, and cannot open another customer's order (404).
- **Master files** live on the private disk (`storage/app/private/artworks`) and are streamed through a controller. Only previews and avatars are on the public disk.
- Artist portraits are validated image uploads stored on Laravel’s public disk; artist deletion is blocked until linked artwork is reassigned.
- All forms use CSRF tokens, all input is server-side validated, passwords are hashed with bcrypt, and login/contact routes are rate limited.
- Artwork deletion is refused when sales history exists; categories with artwork cannot be deleted.

---

## 8. Payments

The checkout page accepts GCash, card and PayPal order selections but does not process a real charge. Orders remain pending until an administrator independently confirms the received amount and provider transaction reference. A customer-supplied reference or screenshot alone cannot verify payment. Once verified, Artiva records the administrator and verification time, issues one receipt per order, and emails it to the registered customer address. A real gateway callback can replace this manual review flow later.

Password reset emails use `MAIL_MAILER=log`, so the reset link is written to `storage/logs/laravel.log` — useful for a classroom demonstration without an SMTP account.

---

## 9. Tests

```bash
php artisan test
```

Feature tests cover the artist directory and admin management, storefront, authentication, role protection, cart, checkout, downloads, receipts and order privacy. PHPUnit is configured to use an in-memory SQLite database so test runs do not alter the local application database.

---

## 10. Artwork assets

The seeder generates abstract SVG artwork previews and master files so the project runs without any external image assets. The seeded artwork names include **Kai, Jp, Captain Valor, Archery and Dan – The Boss**. When you upload real artwork in the admin panel, the preview image replaces the generated one and the master file replaces the generated download. If no preview exists, the storefront automatically renders a styled placeholder instead of a broken image.

---

## 11. Troubleshooting

| Symptom | Fix |
|---|---|
| Images return 404 | Run `php artisan storage:link` and make sure `APP_URL` matches the URL you browse (`php artisan serve` uses `http://127.0.0.1:8000` by default). |
| `419 Page Expired` | Clear cookies and make sure `SESSION_DRIVER=file` with a writable `storage/framework/sessions`. |
| `SQLSTATE[HY000] [1049] Unknown database` | Create the `artiva` database, or update `DB_DATABASE`. |
| Permission errors on storage | `chmod -R 775 storage bootstrap/cache` (Linux/macOS). |
| Demo images missing after SQL import | Run `php artisan db:seed --class=ArtworkSeeder`. |

---

Built as a college project demonstration: working features, clean Laravel architecture, one file per page, and the Artiva design system (Ivory `#F2F3EE`, Navy `#10264D`, Ink `#142653`, Lavender `#D2CFE0`, Sky `#4C8ED5`, Night `#17233B`).
