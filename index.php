<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\FileProcessor;
use App\Helpers;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $filename = __DIR__ . '/uploads/' . $_FILES['file']['name'];
  move_uploaded_file($_FILES['file']['tmp_name'], $filename);

  $fileProcessor = new FileProcessor();
  try {
    $data = $fileProcessor->processFile($filename);

    if (empty($data)) {
      throw new Exception('No data found in file');
    }
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>File Processor</title>
</head>
<body>
  <main>
    <h1>File Processor</h1>
    <form action="" method="post" enctype="multipart/form-data">
      <p>Upload your file to be processed:</p>
      <input required type="file" name="file" accept=".csv">
      <input class="submit-button" type="submit" value="Process">
    </form>

    <?php if (isset($error)): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if (isset($fileProcessor) && isset($data)): ?>
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
            <td><?= Helpers::valueToDollars($row['cost']) ?></td>
            <td><?= Helpers::valueToDollars($row['price']) ?></td>
            <td class="<?= Helpers::isNegative($row['qty']) ? 'negative' : 'positive' ?>">
              <?= $row['qty'] ?>
            </td>
            <td class="<?= Helpers::isNegative($row['profitMargin']) ? 'negative' : 'positive' ?>">
              <?= Helpers::valueToPercentage($row['profitMargin']) ?>
            </td>
            <td class="<?= Helpers::isNegative($row['totalProfitUSD']) ? 'negative' : 'positive' ?>">
              <?= Helpers::valueToDollars($row['totalProfitUSD']) ?>
            </td>
            <td class="<?= Helpers::isNegative($row['totalProfitCAD']) ? 'negative' : 'positive' ?>">
              <?= Helpers::valueToDollars($row['totalProfitCAD']) ?>
            </td>
          </tr>
        <?php endforeach; ?>

        <!-- Footer-->
        <?php
        $totalQty = $fileProcessor->getTotalQty();
        $averageProfitMargin = $fileProcessor->getAverageProfitMargin();
        $totalProfitUSD = $fileProcessor->getTotalProfitUSD();
        $totalProfitCAD = $fileProcessor->getTotalProfitCAD();
        ?>
        <tr>
          <td colspan="2"></td>
          <td><?= Helpers::valueToDollars($fileProcessor->getAveragePrice()) ?></td>
          <td class="<?= Helpers::isNegative($totalQty) ? 'negative' : 'positive' ?>">
            <?= $totalQty ?>
          </td>
          <td class="<?= Helpers::isNegative($averageProfitMargin) ? 'negative' : 'positive' ?>">
            <?= Helpers::valueToPercentage($averageProfitMargin) ?>
          </td>
          <td class="<?= Helpers::isNegative($totalProfitUSD) ? 'negative' : 'positive' ?>">
            <?= Helpers::valueToDollars($totalProfitUSD) ?>
          </td>
          <td class="<?= Helpers::isNegative($totalProfitCAD) ? 'negative' : 'positive' ?>">
            <?= Helpers::valueToDollars($totalProfitCAD) ?>
          </td>
        </tr>
      </table>
    <?php endif; ?>
  </main>
</body>
</html>

