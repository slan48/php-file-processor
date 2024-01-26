<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\FileProcessor;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $filename = __DIR__ . '/uploads/' . $_FILES['file']['name'];
  move_uploaded_file($_FILES['file']['tmp_name'], $filename);

  $fileProcessor = new FileProcessor();
  try {
    $data = $fileProcessor->processFile($filename);
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>File Processor</title>
</head>
<body>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file" accept=".csv">
    <input type="submit" value="Process">
  </form>

  <?php if (isset($data)): ?>
    <table>
      <!-- Header -->
      <tr>
        <th>SKU</th>
        <th>Cost</th>
        <th>Price</th>
        <th>QTY</th>
        <th>Profit Margin</th>
        <th>Total Profit (USD)</th>
        <th>Total Profit (CAD)</th>
      </tr>
      <!-- Body -->
      <?php foreach ($data as $row): ?>
        <tr>
          <td><?= $row['sku'] ?></td>
          <td><?= $row['cost'] ?></td>
          <td><?= $row['price'] ?></td>
          <td><?= $row['qty'] ?></td>
          <td><?= $row['profitMargin'] ?></td>
          <td><?= $row['totalProfitUSD'] ?></td>
          <td><?= $row['totalProfitCAD'] ?></td>
        </tr>
      <?php endforeach; ?>

      <!-- Footer: Footer: Average Price, total qty, average profit margin, total profit (USD), total profit (CAD)-->
      <tr>
        <td colspan="2">Average Price</td>
        <td><?= $fileProcessor->getAveragePrice($data) ?></td>
        <td><?= $fileProcessor->getTotalQty($data) ?></td>
        <td><?= $fileProcessor->getAverageProfitMargin($data) ?></td>
        <td><?= $fileProcessor->getTotalProfitUSD($data) ?></td>
        <td><?= $fileProcessor->getTotalProfitCAD($data) ?></td>
      </tr>

    </table>
  <?php endif; ?>
</body>
</html>

