<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Invoice Service</title>
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
	<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<td rowspan="4" style="width: 10%; text-align: center; background: #5151"><h1 style="font-size: 40px;">A</h1><p>Annisa</p></td>
			<td rowspan="4" style="width: 35%;">Jual beli, tukar tambah komputer & printer <br>
				Upgrade & accesories <br>
				Jl. Siliwangi depan station - Ciwidey <br>
				Telp. 022 85921579 <br>
				<hr style="float: left; width: 90%; border-bottom: 2px solid black; transform: translate(0,30px);">
			</td>
			<td>Nama Customer</td>
			<td style="width: 2% ">:</td>
			<td style="width: 35%; border-bottom: 1px solid #000;"><?php echo $service['nama_service'] ?></td>
		</tr>
		<tr>
			<td>No. Telp.</td>
			<td>:</td>
			<td style="border-bottom: 1px solid #000;"><?php echo $service['telepon_service'] ?></td>
		</tr>
		<tr>
			<td>Alamat</td>
			<td>:</td>
			<td style="border-bottom: 1px solid #000;"><?php echo $service['alamat_service'] ?></td>
		</tr>
		<tr>
			<td>Tgl. Customer</td>
			<td>:</td>
			<td style="border-bottom: 1px solid #000;"><?php echo date('d-m-Y',strtotime($service['tgl_service'])) ?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: left;">
				<p style=" border: 1px solid black; width: 250px; padding: 10px;">
					NO. SERVICE : <?php echo $service['id_service'] ?>
				</p>
			</td>
			<td colspan="3" style="text-align: right; padding: 30px 0 30px 0;"><u><b>TANDA TERIMA</b></u></td>
		</tr>
		<tr>
			<table border="1" cellpadding="10" cellspacing="0" style="width: 100%">
				<tr style="border: 1px solid black;">
					<td style="width: 16.6%">JENIS BARANG</td>
					<td style="width: 16.6%">KERUSAKAN</td>
					<td style="width: 16.6%">KELENGKAPAN</td>
					<td style="width: 16.6%">SERIAL NUMBER</td>
					<td style="width: 16.6%">GARANSI</td>
					<td style="width: 16.6%">KETENTUAN GARANSI</td>
					<td style="width: 16.6%">KETERANGAN</td>
				</tr>
				<tr>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['jenis_barang'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['kerusakan'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['kelengkapan'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['serial_number'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['garansi'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['ketentuan_garansi'] ?></td>
					<td style=" border-bottom: none; border-top: none;"> &bullet; <?php echo $service['keterangan'] ?></td>
				</tr>
				</tr>
			</table>
		</tr>
		<tr>
			<td> &nbsp;</td>
		</tr>
		<tr>
			<table style="width: 100%" border="0" cellpadding="10" cellspacing="0">
				<tr>
					<td style="width: 50%; border: 1px solid black;">
						Catatan: <br>
						&bullet; Barang yang tidak diambil selama 30 hari setelah pemberitahuan hilang rusak bukan tanggung jawab kami
						<br>
						&bullet; Setiap barang yang masuk apabila di cancel kena biaya cek 25.000
					</td>
					<td style="width: 25%; text-align: center;">Tanda Terima, <br> <br> <br> <br>
						<?php if ($service['nama_service'] != 'Umum'): ?>
							<?php echo $service['nama_service'] ?>
						<?php endif ?>
						<hr style="width: 70%; border: 1px solid black;"></td>
						<td style="width: 25%; text-align: center;">Hormat Kami, <br> <br> <br> <br> 
							<?php echo $service['nama_karyawan'] ?> 
							<hr style="width: 70%; border: 1px solid black;"></td>
						</tr>
					</table>
				</tr>
			</table>
			<div id="buttons" style="text-transform:uppercase;" class="no-print">
				<hr>
				<span class="col-xs-12">
					<a class="btn btn-block btn-success" href="<?php echo base_url('service/tambah') ?>">Kembali Ke Service</a>
				</span>
				<div style="clear:both;"></div>
			</div>
		</body>
		</html>