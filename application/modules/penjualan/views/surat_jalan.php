<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Surat Jalan</title>
	<style>
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
<body onload="window.print()">
	<table cellpadding="10"  border="0" style="width: 100%;">
		<tr>
			<td colspan="2">ANNISA KOMPUTER</td>
			<td colspan="2"><b>SURAT JALAN</b></td>
		</tr>
		<tr>
			<td> </td>
		</tr>
		<tr>
			<td width="30%" colspan="1">Kode Surat Jalan</td>
			<td>: </td>
			<td><?php echo $penjualan['faktur_penjualan'] ?></td>
			<td width="70%" rowspan="2">Kepada Yth, <br> <?php echo $penjualan['nama_pelanggan'] ?></td>
		</tr>
		<tr>
			<td colspan="1">Tanggal Masuk</td>
			<td>: </td>
			<td><?php echo date('d-m-Y',strtotime($penjualan['tgl'])) ?></td>
		</tr>
		<tr>
			<td colspan="1"></td>
			<td></td>
			<td></td>
			<td rowspan="2"><?php echo $penjualan['alamat'] ?></td>
		</tr>
		<tr>
			<td colspan="1"> </td>
			<td></td>
			<td style="width: 60%"></td>
		</tr>
		<tr>
			<table border="1" cellpadding="10" cellspacing="0" style="width: 100%">
				<tr style="border: 1px solid black;">
					<td style="width: 5%">No.</td>
					<td style="width: 50%">Nama Barang</td>
					<td style="width: 10%">Qty</td>
					<td style="width: 10%">Satuan</td>
					<td style="width: 25%">Keterangan</td>
				</tr>
				<?php $no=1; foreach ($detail_penjualan as $row): ?>
				<tr>
					<td style=" border-bottom: none; border-top: none;"><?php echo $no++ ?></td>
					<td style=" border-bottom: none; border-top: none;"><?php echo $row['nama_barang'] ?></td>
					<td style=" border-bottom: none; border-top: none;"><?php echo $row['jumlah'] ?></td>
					<td style=" border-bottom: none; border-top: none;"><?php echo $row['satuan'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> </td>
				</tr>
			<?php endforeach ?>
		</table>
	</tr>
	<tr>
		<table style="width: 100%" border="0" cellpadding="20">
			<tr>
				<td style="width: 25%; text-align: center;">Menerima,</td>
				<td style="width: 25%; text-align: center;">Sopir,</td>
				<td style="width: 25%; text-align: center;">Gudang,</td>
				<td style="width: 25%; text-align: center;">Hormat Kami,</td>
			</tr>
		</table>
	</tr>
</table>

<div id="buttons" style="text-transform:uppercase;" class="no-print">
	<hr>
	<span class="col-xs-12">
		<a class="btn btn-block btn-success" href="<?php echo base_url('penjualan') ?>">Kembali Ke Penjualan</a>
	</span>
	<div style="clear:both;"></div>
</div>

</body>
</html>