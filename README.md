# Beauty & Blooms — Laravel 12

This project uses Laravel 12, PHP 8.2+, MySQL, Blade, and the existing `index4.html` storefront design. It does not use Breeze, install front-end packages, or require `npm install`/Vite to run. The storefront CSS and JavaScript are served directly from `public/`.

## 1. Configure MySQL and run the app

Create a database in MySQL:

```sql
CREATE DATABASE beauty_and_blooms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

From the Laravel project directory, run:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

If this project already has a populated `.env`, do not overwrite it with the `cp` command. Set the following values in `.env` for your local MySQL account:

```dotenv
APP_NAME="Beauty & Blooms"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beauty_and_blooms
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Then run the migrations, sample-data seeder, and public image-storage link:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`. No npm command is needed.

The local-only seeded admin account is:

- Email: `admin@beauty.test`
- Password: `ChangeMe123!`

Change that password immediately after first sign-in. Outside local development, set `ADMIN_EMAIL` and a unique `ADMIN_PASSWORD` of at least 12 characters in the deployment environment before running `php artisan db:seed`. The seeder refuses to create an admin with a development default outside the local environment.

For password-reset messages in local development, Laravel's default log mailer writes the reset URL to the application log. Configure SMTP environment variables before enabling mail in production.

## 2. Artisan commands used to scaffold the application

These are the commands used to generate the models, migrations, controllers, and middleware. They are listed for reference; **do not rerun them in this already-scaffolded project** because Artisan will create duplicate files.

```bash
php artisan make:model Category -m
php artisan make:model Product -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:migration add_role_to_users_table --table=users
php artisan make:controller StorefrontController
php artisan make:controller AuthController
php artisan make:controller CheckoutController
php artisan make:controller OrderHistoryController
php artisan make:controller Admin/ProductController --resource
php artisan make:controller Admin/CategoryController --resource
php artisan make:controller Admin/OrderController
php artisan make:middleware AdminMiddleware
```

Core migration/model files:

- `database/migrations/*create_categories_table.php`, `*remove_image_path_from_categories_table.php`, and `*restore_image_path_on_categories_table.php` — categories with optional image uploads
- `database/migrations/*create_products_table.php` — catalog, pricing, inventory, descriptions, options, and image paths
- `database/migrations/*create_orders_table.php` and `*create_order_items_table.php` — customer delivery details and immutable item/price snapshots
- `database/migrations/*add_role_to_users_table.php` — customer/admin authorization
- `app/Models/Category.php`, `Product.php`, `Order.php`, `OrderItem.php`, and `User.php`
- `database/seeders/DatabaseSeeder.php` — five categories, eleven sample products, and the local admin account

## 3. Features

- The supplied `BB/index4.html` visual design is converted to the Laravel storefront at `/`. Product and category data now comes from MySQL.
- Admins can create, edit, activate/deactivate, and delete products and categories. Product and optional category image inputs upload JPG, PNG, or WebP files to the local `public` storage disk; the storefront renders those uploaded files through `/storage/...`, not image URLs. Category images can be omitted to use the local placeholder.
- Customers can register, sign in, sign out, request password-reset links, and review their orders.
- Checkout requires an authenticated customer and the demo card fields. The server verifies the fixed test number (`4242 4242 4242 4242`), future expiry, CVC, active products, valid options, stock, and prices; it calculates shipping and totals and creates the order/items in a database transaction. Card values are validated by the server but are never saved. This is not real payment processing; no external payment provider is configured.
- Admins can see customer/order details, update fulfilment status, and cancel an order. Cancelling restores its reserved inventory.
- Customer/admin pages and storefront styles share `public/css/storefront.css`.
- `routes/web.php` protects the admin panel with authentication and the `admin` role middleware. Customer registration cannot assign an admin role.

Admin routes:

- `/admin` — dashboard
- `/admin/products` — product management
- `/admin/categories` — category management
- `/admin/orders` — order management

## 4. Image uploads

The app stores uploaded image paths in MySQL and the image files under `storage/app/public/products` or `storage/app/public/categories`. The public disk serves those files at `/storage/...`; `php artisan storage:link` also exposes them at `public/storage` for web servers that support symbolic links. The SVG in `public/images/product-placeholder.svg` is a local fallback until an admin uploads a real image.

Category images are optional. Upload JPG, PNG, or WebP category images up to 3 MB in the admin form; oversized files trigger an immediate toast and are also validated by the server. If omitted, category tiles use the local placeholder. Product images are uploaded and managed from the product admin form.

If you change `APP_URL`, clear cached configuration:

```bash
php artisan config:clear
```

Never use `php artisan migrate:fresh` on a database containing data; it drops every table.

## 5. Validation

Run the focused commerce tests and PHP formatter:

```bash
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=StorefrontCommerceTest
vendor/bin/pint --test
```
