# Pembayaran

Labayar mendukung beberapa pembayaran, diantaranya:

- Pembayaran tunai di kasir
- Pembayaran menggunakan tripay payment gateway

## Pengaturan

Tambahkan satu controller **PaymentContorller**

```sh
php artisan make:controller PaymentController
```

Lalu tambahkan satu fungsi yang nantinya akan kita gunakan sebagai action untuk membuat invoice

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        // disini nantinya kita akan menambahkan logika untuk membuat invoice
    }
}
```

Tambahkan satu **view** dengan nama **chasier.blade.php** untuk kebutuhan generate invoice. halaman ini hanya contoh, kamu dapat mengembangkan sesuka kamu. Halaman ini hanya akan menyajikan field wajib yang harus berikan ketika ingin generate invoice.

Tampilkan views tersebut ke browser

```php title="routes/web.php"
Route::get('/', function () {
  return view('chasier');
});
```

didalam view **chasier.blade.php** kamu bisa menambahkan potongan kode dibawah ini

```php title="resources/views/chasier.blade.php"
<form action="/invoice/generate" method="post">
  @csrf
  <fieldset>
    <legend>Customer</legend>
    <label for="">Nama customer</label><br>
    <input type="text" name="customerName" value="Raga mulia kusuma"><br>
    <label for="">Telepon customer</label><br>
    <input type="text" name="customerPhone" value="081234567890"><br>
    <label for="">Email customer</label><br>
    <input type="text" name="customerEmail" value="real.ragamulia@gmail.com"><br>
  </fieldset>
  <fieldset>
    <legend>Pesanan</legend>
    <label for="">ID produk</label><br>
    <input type="text" name="productId[]" value="product123" readonly><br>
    <label for="">Nama produk</label><br>
    <input type="text" name="productName[]" value="Jeruk bali asli" readonly><br>
    <label for="">Jumlah beli</label><br>
    <input type="number" name="productQty[]" value="2" readonly><br>
    <label for="">Harga satuan</label><br>
    <input type="number" name="productPrice[]" value="10000" readonly><br>
  </fieldset>
  <fieldset>
    <legend>Jumlah bayar</legend>
    <div>* jumlah bayar hanya bisa digunakan untuk jenis pembayaran langsung (cash)</div>
    <input type="number" name="payAmount" value="30000" readonly>
  </fieldset>
  <button type="submit">Bayar</button>
</form>
```

Perhatikan pada tag **form**, disana ada attribute **method** dan **action**. Pada bagian method, kamu bisa isi dengan **POST** dan action **/invoice/generate**. Lalu kamu bisa kembali ke web router, dan menambahkan potongan kode ini

```php title="routes/web.php"
use App\Http\Controllers\PaymentController;
Route::post("/invoice/generate", [PaymentController::class, "createInvoice"]);
```

Kembali ke controller **PaymentController** dan pada method **createInvoice** kamu bisa tambahan logika untuk memproses seluruh inputan dari **chasier.blade.php**

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        /**
         * ID unik untuk setiap data invoice, kamu bisa mengkreasikan dengan ID mu sendiri
        */
        $orderId = "inv-0001";
        $customer = [
          "name" => $request->customerName,
          "phone" => $request->customerPhone,
          "email" => $request->customerEmail,
        ];
        $amount = $request->payAmount;
        $items = [];
        for ($i = 0; $i < count($request->productId); $i++) {
          $items[] = [
            "productId" => $request->productId[$i],
            "price" => $request->productPrice[$i],
            "quantity" => $request->productQty[$i],
            "name" => $request->productName[$i],
          ];
        }
        /**
         * Digunakan untuk mengatur batas jatuh tempo pembayaran. Untuk key unit kamu bisa memasukkan * minutes/hours/days
        */
        $expiry = [
          "unit" => "minutes",
          "duration" => 60
        ];

        $payload = [
          "orderId" => $orderId,
          "customer" => $customer,
          "items" => $items,
          "expiry" => $expiry,
          "payAmount" => $amount
        ];

        //Inisiasi logika pembayaran disini
    }
}
```

!!! warning
Perhatikan variable **$payload**, untuk pembayaran dengan payment gateway kamu tidak perlu mengirimkan data **payAmount**

!!! tip
Pada dasarnya untuk setiap data yang dikirim untuk generate invoice adalah sama, jadi kamu bisa menggunakan **$payload** yang sama untuk generate invoice manual / payment gateway

## Invoice manual (cash)

Buka controller **PaymentController** dan tambahkan potongan kode dibawah ini:

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        ...
        //Inisiasi logika pembayaran disini
        $payment = new Payment();
        $payment->createInvoice($payload);
        // redirect
    }
}
```

## Invoice payment gateway

Untuk pembayaran payment gateway ini, kamu hanya perlu menbamhahkan satu parameter pada instance **Payment**.

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        ...
        //Inisiasi logika pembayaran disini
        $payment = new Payment("tripay");
        $payment->createInvoice($payload);
        // redirect
    }
}
```

!!! tip
Kamu bisa menambahkan payment gateway tripay hanya dengan menambahkan satu parameter **tripay**. Ini berlaku untuk payment gateway lainnya, hanya saja untuk saat ini, labayar hanya mendukung payment gateway tripay.

Sejauh ini kamu sudah berhasil melakukan generate invoice di sistem. Kamu bisa check di database pada table **labayar_invoices**, maka akan terdefinisi satu record baru.

!!! tip
Jika kamu tidak ingin membuat halaman order sendiri, kamu bisa menggunakan halaman order bawaan dari labayar, dengan melakukan redirect ke api labayar

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        ...
        // redirect
        return redirect("/api/labayar/orders");
    }
}
```

## Pembayaran invoice (manual)

Kamu bisa melunasi pembayaran dengan metode tunai / payment gateway dengan hanya satu fungsi. Pada controller **PaymentController** kamu bisa menambahkan

```php title="app/Http/Controllers/PaymentController.php"
use Koderpedia\Labayar\Payment;
class PaymentController extends Controller
{
    public function payInvoice(Request $request)
    {
        $paymentId = "inv-00001-123234"; // kamu bisa mendapatkan data ini dari table mysql labayar_invoice_payments field orderId
        $amount = 40000; // jumlah ini harus >= nett total invoice
        return Payment::pay(compact("paymentId", "amount"))
    }
}
```

!!! warning
Untuk payment gateway fungsi pembayaran invoice ini tidak wajib, karena invoice akan otomatis lunas, ketika invoice dibayar menggunakan metode bayar dari payment gateway
