# Referensi API Labayar

## Pembayaran

Pembayaran invoice manual

```php
use Koderpedia\Labayar\Payment;
<?php
$paymentId = "inv-00001-12334";
$amount = 40000;
Payment::pay(["paymentId" => $paymentId, "amount" => $amount])
?>
```

| Payload   | Deskripsi                                         | Tipe data |
| --------- | ------------------------------------------------- | --------- |
| paymentId | ID pembayaran dari table labayar_invoice_payments | string    |
| amount    | Jumlah yang dibayarkan                            | integer   |

API List order pembayaran

```php
use Koderpedia\Labayar\Payment;
<?php
$filter = [
  "invoiceId" => "inv-0001",
  "keyword" => "inv-0001",
  "createdAtRange" => [
    "dateStart" => new Date
    "dateEnd" => new Date
  ],
  "oneRow" => true
];
Payment::APIListOrder()
?>
```

| Payload        | Deskripsi                                     | Tipe data |
| -------------- | --------------------------------------------- | --------- |
| invoiceId      | ID invoice dari labayar_invoices              | string    |
| keyword        | Pencarian data berdasarkan keyword id invoice | string    |
| createdAtRange | Filter tanggal invoice dibuat                 | array     |
| oneRow         | Hanya menampilkan satu data                   | boolean   |
