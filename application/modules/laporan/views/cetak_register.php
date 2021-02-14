<?php 
$pengaturan = $this->db->get('pengaturan')->row_array(); 

if ($id_outlet = $this->session->userdata('id_outlet')) {
    $outlet = $this->db->get_where('outlet', ['id_outlet' => $id_outlet])->row_array();
}else{
    $outlet = $this->db->get('outlet')->row_array();
}


?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $judul ?></title>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('vendor/lte/') ?>dist/css/AdminLTE.min.css">
    <style type="text/css" media="all">
        table {
            font-size: 12px;
        }
        body { color: #000; }
        #wrapper { max-width: 520px; margin: 0 auto; padding-top: 20px; }
        .btn { margin-bottom: 5px; }
        .table { border-radius: 3px; }
        .table th { background: #f5f5f5; }
        .table th, .table td { vertical-align: middle !important; }
        h3 { margin: 5px 0; }

        @media print {
            .no-print { display: none; }
            #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
        }
        tfoot tr th:first-child { text-align: right; }
    </style>
</head>
<body>
    <div id="wrapper">
        <div id="receiptData" style="width: auto; max-width: 580px; min-width: 250px; margin: 0 auto;">
            <div class="no-print">
            </div>
            <div id="receipt-data">
                <div>
                    <div style="text-align:center;">
                        <p style="text-align:center;">
                            <strong><?php echo $outlet['nama_outlet'] ?></strong><br>
                            <?php echo $outlet['alamat'] ?><br>
                            <?php echo $outlet['email'] ?><br>
                            <?php echo $outlet['telepon'] ?>
                        </p>
                    </div>
                    <div style="clear:both;"></div>
                    <table class="table table-condensed">
                        <tr>
                            <th>Nama Petugas</th>
                            <td><?php echo $register['nama_petugas'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Outlet</th>
                            <td><?php echo $outlet['nama_outlet'] ?></td>
                        </tr>
                        <tr>
                            <th>Mulai</th>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($register['mulai'])) ?></td>
                        </tr>
                        <tr>
                            <th>Berakhir</th>
                            <?php if ($register['berakhir'] == '0000-00-00 00:00:00'): ?>
                                <td></td>
                                <?php else: ?>
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($register['berakhir'])) ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <th>Saldo Awal</th>
                                <td><?php echo "Rp. " . number_format($register['uang_awal']) ?></td>
                            </tr>
                            <tr>
                                <th>Saldo Akhir</th>
                                <?php if ($register['total_uang'] == 0): ?>
                                    <?php 
                                    $id_petugas = $this->session->userdata('id_petugas');

                                    $q1 = "SELECT SUM(total_uang) AS 'total' FROM `register` WHERE DATE(mulai) = DATE(NOW()) AND status = 'close' AND id_petugas = '$id_petugas' ";
                                    $total_uang = $this->db->query($q1)->row()->total ?? 0;

                                    $q2 = "SELECT SUM(uang_awal) AS 'uang_awal' FROM `register` WHERE DATE(mulai) = DATE(NOW()) AND status = 'close' AND id_petugas = '$id_petugas' ";
                                    $uang_awal = $this->db->query($q2)->row()->uang_awal ?? 0;

                                    $total_tarik = $total_uang - $uang_awal;

                                    $query = "SELECT SUM(total_bayar) AS 'hari_ini' FROM `penjualan` WHERE DATE(tgl) = DATE(NOW()) AND id_petugas = '$id_petugas' ";
                                    $hari_ini = $this->db->query($query)->row()->hari_ini ?? 0;

                                    ?>
                                    <td><?php echo "Rp. " . number_format($register['uang_awal'] + ($hari_ini - $total_tarik)) ?></td>
                                    <?php else: ?>
                                        <td><?php echo "Rp. " . number_format($register['total_uang']) ?></td>
                                    <?php endif ?>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- start -->
                    <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
                        <hr>
                        <?php if ($this->uri->segment(3)): ?>
                            <span class="pull-right col-xs-12">
                               <a href="<?php echo base_url('laporan/cetak_thermal_register/') . $register['id_register'] ?>" class="btn btn-info btn-block">Cetak (Thermal)</a>
                           </span>    
                           <?php else : ?>
                            <span class="pull-right col-xs-12">
                               <a href="<?php echo base_url('laporan/cetak_thermal_register') ?>" class="btn btn-info btn-block">Cetak (Thermal)</a>
                           </span>
                       <?php endif ?>

                       <span class="pull-right col-xs-12">
                        <button onclick="window.print();" class="btn btn-block btn-primary">Cetak</button>
                    </span>
                    <span class="col-xs-12">
                        <a class="btn btn-block btn-danger" href="<?php echo base_url('logout') ?>">Logout</a>
                    </span>
                    <?php if ($this->session->userdata('id_outlet')): ?>
                        <span class="col-xs-12">
                            <a class="btn btn-block btn-warning" href="<?php echo base_url('penjualan') ?>">Kembali Ke Penjualan</a>
                        </span>
                        <?php else: ?>
                            <span class="col-xs-12">
                                <a class="btn btn-block btn-success" href="<?php echo base_url('laporan/register') ?>">Kembali Ke Laporan</a>
                            </span>
                        <?php endif ?>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>
        </body>
        </html>
