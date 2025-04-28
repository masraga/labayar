# Labayar

Labayar is a CRM library to handling many case in CRM app, such as make a payment, check order status, and other. With labayar you have no worry with payment logic, since labayar solve all your payment problem. Labayar is usefull if your build POS and Ecommerce application.

### Main Feature

- Pay transaction over the counter (chasier)
- Pay transaction with payment gateway
- Sales graph monthly / yearly
- Download invoice as PDF
- Check payment status with modern UI

### Screenshot

![Snap UI](https://raw.githubusercontent.com/masraga/labayar/refs/heads/dev-master/screenshot/snap.png)
![Payment Status UI](https://raw.githubusercontent.com/masraga/labayar/refs/heads/dev-master/screenshot/payment-status.png)
![Invoice page](https://raw.githubusercontent.com/masraga/labayar/refs/heads/dev-master/screenshot/invoice.png)
![graphic sales](https://raw.githubusercontent.com/masraga/labayar/refs/heads/dev-master/screenshot/graph.png)
![Payment page](https://raw.githubusercontent.com/masraga/labayar/refs/heads/dev-master/screenshot/payment-list.png)

### Requirements

- PHP 8
- Laravel 8 or above (labayar only support in laravel for now)
- Composer
- MySql

### Installations

1. Create laravel project you can check from [laravel official page](https://laravel.com/docs/12.x/installation).
2. Open laravel in your favorite text editor, prefer use vscode.
3. Ensure [composer](https://getcomposer.org/) is installed in your machine.
4. Open terminal/cmd, and ensure terminal path is same with your laravel project, and and run this command

```sh
composer require koderpedia/labayar
```

5. Publish all asset in vendor, run this in your terminal/cmd

```sh
php artisan vendor:publish --provider=Koderpedia\Labayar\LabayarServiceProvider
```

6. set your .env database and ensure mysql event is actived [learn more](https://vijaymrami.wordpress.com/2016/01/28/how-to-schedule-mysql-query-using-mysql-event-in-phpmyadmin/)

```sh
php artisan migrate
```

7. Setup your store information. Open .env file and add this below

| Key                   | Description   |
| --------------------- | ------------- |
| LABAYAR_STORE_NAME    | Store name    |
| LABAYAR_STORE_OWNER   | Store owner   |
| LABAYAR_STORE_ADDRESS | Store address |
| LABAYAR_STORE_PHONE   | Store phone   |
| LABAYAR_STORE_EMAIL   | Store email   |

8. run project

```sh
php artisan serve
```

### Basic usage

#### Creating manual transaction

You can create invoice to your order, add this to your controller. fill all value variable **$payload** with yours

```php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createTransaction(Request $request){
        $labayar = new Payment();
        $payload = [
            "orderId" => "inv-0007",
            "customer" => [
                "name" => "raga",
                "email" => "real.ragamulia@gmail.com",
                "phone" => "081234567890",
            ],
            "items" => [
                [
                    "productId" => "prd0001",
                    "name" => "Jambu air",
                    "quantity" => 2,
                    "price" => "20000",
                ],
                [
                    "productId" => "prd0002",
                    "name" => "Rambutan",
                    "quantity" => 2,
                    "price" => 20000,
                ],
            ],
            "expiry" => ["unit" => "minutes", "duration" => 10],
            "payAmount" => 90000,
        ];
        $transaction = $labayar->createInvoice($payload);
        return redirect("/api/labayar/orders");
    }
}
```

#### Pay transaction with core API

Pay transaction that you create before. add this to your controller

```php
public function payInvoice(Request $request){
    $payload = [
        "paymentId" => "inv-0007-1745133149",
        "amount" => 400000,
    ];
    $payment = Payment::pay($payload);
    return $payment
}
```

Note: Get **paymentId** payload from create transaction response

#### Pay transaction with built in page

if you too busy to build common payment page, dont worry, labayar can handle that. redirect your laravel project to this url below.

```sh
/api/labayar/orders
```

if your default laravel url is http://localhost:8000

```sh
http://localhost:8000/api/labayar/orders
```

After that click an order you want to pay.

#### Create transaction with payment gateway

Currently labayar providing tripay payment gateway for handling transaction. We will always update more payment gateway soon.
Before use tripay payment gateway, add this key to your .env
| Key | Description |
| ----------- | ----------- |
| TRIPAY_IS_PRODUCTION | Use tripay production mode |
| TRIPAY_MERCHANT_CODE | Tripay merchant code |
| TRIPAY_API_KEY | Tripay API key |
| TRIPAY_PRIVATE_KEY | Tripay private key |
| TRIPAY_RETURN_URL | Tripay return url after payment success |

Add this to your controller

```php
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function loadSnapLabayar(Request $request)
    {
        $payment = new Payment("tripay");
        $payload = [
            "orderId" => "inv-" . time(),
            "customer" => [
                "name" => "Raga mulia kusuma",
                "email" => "real.ragamulia@gmail.com",
                "phone" => "0891234567890"
            ],
            "expiry" => [
                "unit" => "minutes",
                "duration" => 60
            ],
            "items" => [
                [
                    "productId" => "prod12345",
                    "name" => "Mangga harum manis pekanbaru",
                    "quantity" => 10,
                    "price" => 10000
                ],
                [
                    "productId" => "prod12345",
                    "name" => "Jeruk harum manis pekanbaru",
                    "quantity" => 10,
                    "price" => 10000
                ],
                [
                    "productId" => "prod12345",
                    "name" => "Strawberry harum manis pekanbaru",
                    "quantity" => 10,
                    "price" => 10000
                ],
            ]
        ];
        return $payment->UISnapLabayar($payload);
    }
}

```

> **Note:** All **$payload** value is similar with manual transaction value. But you dont need send **payAmount** key

After that, you can add this controller method to your routes/web.php

```php
Route::get("/snap", [PaymentController::class, "loadSnapLabayar"]);
```

Open your browser and go to page **{{your_base_url}}/snap**. and enjoy :D

##### Useful URL

Below is useful url will help to save your development time
| Endpoint | Description |
| ----------- | ----------- |
| /api/labayar/orders | Show all your orders |
| /api/labayar/payments/{invoiceId} | Show all your payment in each order |
| /api/labayar/payments/graph | Show sales graph |

##### API Reference

- [postman](https://www.postman.com/orange-resonance-534979/workspace/labayar/collection/15555730-d6515741-160e-42a9-865a-1fbe98643e7f?action=share&creator=15555730)
