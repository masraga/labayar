# Labayar
Labayar is a CRM library to handling many case in CRM app, such as make a payment, check order status, and other. With labayar you have no worry with payment logic, since labayar solve all your payment problem. Labayar is usefull if your build POS and Ecommerce application.
### Requirements
- Laravel (labayar only support in laravel for now)
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
6. migrate database
```sh
php artisan migrate
```
7. run project 
```sh
php artisan serve
```
### Basic usage
#### Creating manual transaction
You can create invoice to your order, add this to your controller
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
        return response()->json($transaction);
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

##### API Reference
- [postman](https://www.postman.com/orange-resonance-534979/workspace/labayar/collection/15555730-d6515741-160e-42a9-865a-1fbe98643e7f?action=share&creator=15555730)
