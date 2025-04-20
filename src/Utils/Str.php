<?php
namespace Koderpedia\Labayar\Utils;

class Str {
  public static function toCurrency(int $number): string{
    return "Rp".number_format($number, 0, ".", ".");
  }

  public static function toInt(string $text): int {
    $numbers = [];
    preg_match_all("/\d+/", $text, $numbers);
    return (int) implode("", $numbers[0]);
  }
}