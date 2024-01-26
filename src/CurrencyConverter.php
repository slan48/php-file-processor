<?php

namespace App;

use Dotenv\Dotenv;

class CurrencyConverter {

  private string $base_url;
  private string $apikey;

  private float $conversion_rate;

  /**
   * @throws \Exception
   */
  public function __construct(
    public string $from_currency,
    public string $to_currency,
  )
  {
    $this->base_url = $_ENV['CURRENCY_CONVERTER_BASE_URL'];
    $this->apikey = $_ENV['CURRENCY_CONVERTER_API_KEY'];

    if (empty($this->base_url) || empty($this->apikey)) {
      throw new \Exception('Currency converter API not configured');
    }

    $this->conversion_rate = $this->getConversionRate();
  }

  /**
   * Get the conversion rate from the API
   * @return float
   * @throws \Exception
   */
  private function getConversionRate(): float
  {
    $from_currency_encoded = urlencode($this->from_currency);
    $to_currency_encoded = urlencode($this->to_currency);
    $query =  "{$from_currency_encoded}_{$to_currency_encoded}";

    $json = file_get_contents("{$this->base_url}/api/v7/convert?q={$query}&compact=ultra&apiKey={$this->apikey}");

    if (empty($json)) {
      throw new \Exception('Unable to get conversion rate');
    }

    $obj = json_decode($json, true);

    if (empty($obj["$query"])) {
      throw new \Exception('Unable to get conversion rate');
    }

    return floatval($obj["$query"]);
  }

  /**
   * Convert amount from one currency to another
   * @param $amount
   * @return float
   */
  public function convertCurrency($amount): float
  {
    $total = $amount * $this->conversion_rate;
    return number_format($total, 2, '.', '');
  }
}
