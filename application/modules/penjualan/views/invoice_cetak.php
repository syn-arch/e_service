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
    <style type="text/css" media="all">
        @font-face {
            font-family: dot-metrix;
            src: url(<?php echo base_url('assets/font/dot.TTF') ?>);
        }
        *{
            /*font-size: 12px;*/
            font-family: dot-metrix;
        }
        
        @media print {
            * {
                font-family: dot-metrix;
            }
            .no-print { 
                display: none; 
            }

        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <table border="0" cellspacing="0" cellpadding="10" style="width: 100%;">
                    <tr>
                        <td style="width: 70%">
                            <table cellspacing="0" style="width: 100%">
                                <tr>
                                    <td><h3><b><?php echo $outlet['nama_outlet'] ?></b></h3></td>
                                </tr>
                                <tr>
                                    <td><?php echo $outlet['alamat'] ?><br><br></td>
                                </tr>
                                <?php if ($penjualan['jenis'] == 'member'): ?>
                                    <tr>
                                        <td>Ship to</td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $penjualan['nama_pelanggan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $penjualan['alamat'] ?></td>
                                    </tr>
                                    <?php else :  ?>
                                        <?php if ($penjualan['nama_pengiriman'] != '' || $penjualan['alamat_pengiriman'] != ''): ?>
                                           <tr>
                                            <td>Ship to</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $penjualan['nama_pengiriman'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $penjualan['alamat_pengiriman'] ?></td>
                                        </tr>   
                                    <?php endif ?>
                                <?php endif ?>
                            </table>
                        </td>
                        <td style="width: 30%;">
                            <table style=" width: 100%">
                                <tr>
                                    <td style="width: 50%"><h3>Sales Invoice</h3></td>
                                </tr>
                                <tr>
                                    <td width="30%">Invoice No.</td>
                                    <td width="70%">: <?php echo $penjualan['faktur_penjualan'] ?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Tanggal</td>
                                    <td width="70%">: <?php echo date('d-m-Y',strtotime($penjualan['tgl'])) ?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Kasir</td>
                                    <td width="70%">: <?php echo $penjualan['nama_karyawan'] ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%">
                                <tr style="border: 1px solid black; text-align: center;">
                                    <td>No</td>
                                    <td style="width: 35%">Deskripsi Barang</td>
                                    <td style="width: 10%">Harga</td>
                                    <td style="width: 5%">Diskon</td>
                                    <td style="width: 10%">Qty</td>
                                    <td style="width: 35%">Subtotal</td>
                                </tr>
                                <?php $no=1; foreach ($detail_penjualan as $row): ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td style=" border-bottom: 1px;padding: 2px;"><?php echo $row['nama_barang'] ?></td>
                                        <td style=" border-bottom: 1px;padding: 2px;"><?php echo number_format($row['harga_jual']) ?></td>
                                        <td style=" border-bottom: 1px;padding: 2px;"><?php echo $row['diskon'] ?></td>
                                        <td style=" border-bottom: 1px;padding: 2px;"><?php echo $row['jumlah'] ?></td>
                                        <td style=" border-bottom: 1px;padding: 2px;"><?php echo number_format($row['total_harga']) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <table cellspacing="0" style="width: 100%; text-align: right;margin-top: 10px">
                                <?php if ($penjualan['id_service'] != ''): ?>
                                    <tr>
                                        <td>Harga Jasa :</td>
                                        <td><?php echo "Rp. " . number_format($penjualan['harga_jasa']) ?></td>
                                    </tr>   
                                <?php endif ?>
                                <tr>
                                    <td>Total Belanja :</td>
                                    <td><?php echo "Rp. " . number_format($total_belanja + $penjualan['harga_jasa']) ?></td>
                                </tr>
                                <tr>
                                    <td>Diskon :</td>
                                    <td><?php echo $penjualan['diskon'] . ' %' ?></td>
                                </tr>   
                                <tr>
                                    <td>Potongan :</td>
                                    <td><?php echo "Rp. " . number_format($penjualan['potongan']) ?></td>
                                </tr>
                                <tr>
                                    <td>Total Bayar :</td>
                                    <td><?php echo "Rp. " . number_format($penjualan['total_bayar']) ?></td>
                                </tr>
                                <tr>
                                    <td>Cash :</td>
                                    <td><?php echo "Rp. " . number_format($total_bayar) ?></td>
                                </tr>   
                                <?php if ($total_bayar >= $penjualan['total_bayar']): ?>
                                    <tr>
                                        <td>Kembalian :</td>
                                        <td><?php echo "Rp. " . number_format($total_bayar - $penjualan['total_bayar']) ?></td>
                                    </tr>
                                    <?php else : ?>
                                      <tr>
                                        <td>Harus Dibayar :</td>
                                        <td><?php echo "Rp. " . number_format($penjualan['total_bayar'] - $total_bayar) ?></td>
                                    </tr>
                                <?php endif; ?>   
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- start -->
    <div id="buttons" style="text-transform:uppercase;" class="no-print">
        <hr>
        <span class="pull-right col-xs-12">
            <a href="<?php echo base_url('penjualan/cetak_thermal/') . $penjualan['faktur_penjualan'] ?>" class="btn btn-info btn-block">Cetak (Thermal)</a>
        </span>
        <span class="pull-right col-xs-12">
            <button onclick="window.print();" class="btn btn-block btn-primary">CETAK</button>
        </span>
        <span class="col-xs-12">
            <a class="btn btn-block btn-success" href="<?php echo base_url('penjualan') ?>">Kembali Ke Penjualan</a>
        </span>
        <div style="clear:both;"></div>
    </div>
</div>
</div>
</body>
</html>
