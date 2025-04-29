<?php

namespace Koderpedia\Labayar\Libraries;

require __DIR__ . "/../../../vendor/autoload.php";

use Dompdf\Dompdf;

class PDF extends Dompdf
{
  public function __construct()
  {
    parent::__construct();
  }
}
