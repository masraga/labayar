<?php
namespace Koderpedia\Labayar\Utils;

class Time {
  /**
   * Add time start from now
   * 
   * @param int $duration Add time duration
   * @param string $unit Unit time to add seconds/minutes/hours/days
   * @param bool $toUnix Result type to unix or datetime
   */
  public static function add(int $duration, string $unit, bool $toUnix = true): string|int {
    $time = 0;
    if ($unit == "seconds") {
      $time = time() + $duration;
    } else if ($unit == "minutes") {
      $time = time() + $duration * 60;
    } else if ($unit == "hours") {
      $time = time() + $duration * 60 * 60;
    } else if ($unit == "days") {
      $time = time() + $duration * 24 * 60 * 60;
    }
    
    return ($toUnix) ? $time : date("Y-m-d H:i:s", $time);
  }
}