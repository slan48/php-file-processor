<?php

namespace App;

class FileProcessor
{
  private array $data = [];
  private CurrencyConverter $currencyConverter;

  /**
   * Process the CSV file and return an array of results
   * @param string $filename
   * @return array|null
   * @throws \Exception
   */
  public function processFile(string $filename): ?array
  {
    $csvData = $this->readCSV($filename);

    if (empty($csvData)) {
      return null;
    }

    foreach ($csvData as $row) {
      $sku = $row['sku'] ?? null;
      if (empty($sku)) {
        return throw new \Exception('SKU is required');
      }

      $qty = (int)$row['qty'] ?? null;
      if (empty($qty)) {
        return throw new \Exception('Quantity is required');
      }

      $cost = (float)$row['cost'] ?? null;
      if (empty($cost)) {
        return throw new \Exception('Cost is required');
      }

      $price = (float)$row['price'] ?? null;
      if (empty($price)) {
        return throw new \Exception('Price is required');
      }

      $profitMargin = ($price - $cost) / $cost * 100;
      $totalProfitUSD = $qty * ($price - $cost);
      $totalProfitCAD = $this->convertToCAD($totalProfitUSD);

      $this->data[] = array(
        'sku' => $sku,
        'cost' => $cost,
        'price' => $price,
        'qty' => $qty,
        'profitMargin' => $profitMargin,
        'totalProfitUSD' => $totalProfitUSD,
        'totalProfitCAD' => $totalProfitCAD,
      );
    }

    return $this->data;
  }

  /**
   * Read the CSV file and return an array of data
   * @param string $filename
   * @return array
   */
  private function readCSV(string $filename): array
  {
    $csv = array_map('str_getcsv', file($filename));
    $header = array_shift($csv);
    $csvData = array();

    foreach ($csv as $row) {
      $csvData[] = array_combine($header, $row);
    }

    return $csvData;
  }

  /**
   * Convert the given amount from USD to CAD
   * @param float $amountUSD
   * @return float
   * @throws \Exception
   */
  private function convertToCAD(float $amountUSD): float
  {
    if (empty($this->currencyConverter)) {
      $this->currencyConverter = new CurrencyConverter('USD', 'CAD');
    }

    return $this->currencyConverter->convertCurrency($amountUSD);
  }

  /**
   * Calculate the average price
   * @return float
   */
  public function getAveragePrice(): float
  {
    $total = 0;
    foreach ($this->data as $row) {
      $total += $row['price'];
    }
    return $total / count($this->data);
  }

  /**
   * Calculate the total quantity
   * @return int
   */
  public function getTotalQty(): int
  {
    $total = 0;
    foreach ($this->data as $row) {
      $total += $row['qty'];
    }
    return $total;
  }

  /**
   * Calculate the average profit margin
   * @return float
   */
  public function getAverageProfitMargin(): float
  {
    $total = 0;
    foreach ($this->data as $row) {
      $total += $row['profitMargin'];
    }
    return $total / count($this->data);
  }

  /**
   * Calculate the total profit in USD
   * @return float
   */
  public function getTotalProfitUSD(): float
  {
    $total = 0;
    foreach ($this->data as $row) {
      $total += $row['totalProfitUSD'];
    }
    return $total;
  }

  /**
   * Calculate the total profit in CAD
   * @return float
   */
  public function getTotalProfitCAD(): float
  {
    $total = 0;
    foreach ($this->data as $row) {
      $total += $row['totalProfitCAD'];
    }
    return $total;
  }
}
