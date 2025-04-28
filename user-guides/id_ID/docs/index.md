# Labayar

## Minimum spesifikasi

- PHP 8.0 atau diatasnya
- Laravel framework 8 atau diatasnya
- Mysql

## Instalasi

Buka terminal / command prompt project laravel dan ketik:

```sh
composer require koderpedia/labayar
```

## Quick Start

<a href="https://github.com/masraga/labayar-starter-kit" target="_blank">Laravel quick start</a>

## Setup

Publish vendor asset

```sh
php artisan vendor:publish --provider=Koderpedia\Labayar\LabayarServiceProvider
```

pastikan folder ini, ada di project kamu

```
   my-project/
   ├── config/
   │   ├── tripay.php
   │   └── labayar.php
   ├── database/
   │   └── migrations/
   │     ├── 2025_04_07_00001_labayar_migration.php
   │     └── 2025_04_28_00001_labayar_product_item.php
   │   └── seeders/
   │     └── LabayarSeeder.php
   ├── public/
   │   └── labayar-assets/
   │     ├── css/
   └──   └── images/

```

Pastikan **Event scheduler** aktif di MySql kamu. Jika tidak ingin menggunakan event scheduler, kamu bisa buka file **database/migrations/2025_04_07_00001_labayar_migration.php** lalu hapus bagian ini

```php title="database/migrations/2025_04_07_00001_labayar_migration.php"
$expired = Constants::$paymentExpired;
DB::unprepared("
  CREATE EVENT IF NOT EXISTS set_expired_payment
  ON SCHEDULE EVERY 5 MINUTE
  DO
    UPDATE labayar_invoice_payments
    SET payment_status = {$expired}
    WHERE expired_at < NOW()

");
```

Pengaturan file .env

**Pengaturan toko untuk invoice**

| Keyword               | Deskripsi         |
| --------------------- | ----------------- |
| LABAYAR_STORE_NAME    | Nama toko         |
| LABAYAR_STORE_OWNER   | Nama pemilik toko |
| LABAYAR_STORE_ADDRESS | Alamat toko       |
| LABAYAR_STORE_PHONE   | Telepon toko      |
| LABAYAR_STORE_EMAIL   | Email toko        |

**Pengaturan payment gateway tripay (optional)**

Dapatkan info lengkap mengenai payment gateway tripay <a href="https://tripay.id" target="_blank">di sini</a>

| Keyword              | Deskripsi                                                  |
| -------------------- | ---------------------------------------------------------- |
| TRIPAY_IS_PRODUCTION | Environtment tripay sandbox / production                   |
| TRIPAY_MERCHANT_CODE | Kode merchant tripay                                       |
| TRIPAY_API_KEY       | API key tripay                                             |
| TRIPAY_PRIVATE_KEY   | Private key tripay                                         |
| TRIPAY_RETURN_URL    | Redirect URL ketika berhasil melakukan checkout pembayaran |

Lakukan migrasi pada database melalui terminal atau cmd

```sh
php artisan migrate
```

Jalankan default seeder untuk labayar

```sh
php artisan db:seed --class=LabayarSeeder
```

Jalankan project laravel

```sh
php artisan serve
```
