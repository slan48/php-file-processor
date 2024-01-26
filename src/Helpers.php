<?php

namespace App;

class Helpers
{
  /**
   * Convert a value to dollars
   * @param float $value
   * @return string
   */
  public static function valueToDollars(float $value): string
  {
    return '$' . number_format($value, 2);
  }

  /**
   * Convert a value to a percentage
   * @param float $value
   * @return string
   */
  public static function valueToPercentage(float $value): string
  {
    return number_format($value) . '%';
  }

  /**
   * Check if value is negative
   * @param float $value
   * @return bool
   */
  public static function isNegative(float $value): bool
  {
    return $value < 0;
  }
}
