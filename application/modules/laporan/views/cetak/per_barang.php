<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="base_url" content="<?php echo base_url() ?>">
  <title><?php echo $judul ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="icon" href="<?php echo base_url('assets/img/favicon.png') ?>" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/select2/dist/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>dist/css/skins/skin-red.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    * {
      font-size: 10px;
    }
  </style>
</head>
<body>

  <h4 class="text-center">Laporan Penjualan Per Barang</h4>

  <div class="row">
    <div class="col-md-12">
     <table class="table">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Barcode</th>
          <th>Nama Barang</th>
          <th>Terjual</th>
          <th>Harga Beli</th>
          <th>Harga Jual</th>
          <th>Profit</th>
          <th>Total</th>
          <th>Laba</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; foreach ($laporan as $row): ?>
        <tr>
          <td><?= $row['id_barang'] ?></td>
          <td><?= $row['barcode'] ?></td>
          <td><?= $row['nama_barang'] ?></td>
          <td><?= $row['barang_terjual'] ?></td>
          <td><?= number_format($row['harga_pokok']) ?></td>
          <td><?= number_format($row['harga_jual']) ?></td>
          <td><?= number_format($row['profit']) ?></td>
          <td><?= number_format($row['total']) ?></td>
          <td><?= number_format($row['laba']) ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
</div>

<div class="row">
  <div class="col-md-12">
    <table class="table">
      <tr>
        <th>Pendapatan</th>
        <td><?php echo "Rp. " . number_format($pendapatan_1) ?></td>
        <th>Laba</th>
        <td><?php echo "Rp. " . number_format($laba_1) ?></td>
      </tr>
    </table>
  </div>
</div>


</body>
</html>