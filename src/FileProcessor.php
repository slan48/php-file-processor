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
      return null; // Handle empty file
    }

    foreach ($csvData as $row) {
      // Implement your logic to calculate totals and profit margins per row
      $qty = (int)$row['qty'];
      $cost = (float)$row['cost'];
      $price = (float)$row['price'];

      $profitMargin = ($price - $cost) / $cost * 100;
      $totalProfitUSD = $qty * ($price - $cost);

      // Convert total profit to CAD (assuming you have a CurrencyConverter class)
      $totalProfitCAD = $this->convertToCAD($totalProfitUSD);

      // Store the results for each row
      $this->data[] = array(
        'sku' => $row['sku'],
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
