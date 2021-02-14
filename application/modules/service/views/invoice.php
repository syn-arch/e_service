<section class="invoice">
	<!-- title row -->
	<div class="row">
		<div class="col-xs-12">
			<h2 class="page-header">
				<i class="fa fa-shopping-cart"></i> E-POS - Invoice Service
				<small class="pull-right">Tanggal: <?php echo date('d-m-Y', strtotime($service['tgl_service'])) ?></small>
			</h2>
		</div>
		<!-- /.col -->
	</div>
	<!-- info row -->
	<div class="row invoice-info">
		<div class="col-sm-4 invoice-col">
			<address>
				Karyawan : <?php echo $service['nama_karyawan'] ?><br>
				Pelanggan : <?php echo $service['nama_service'] ?><br>
				Alamat : <?php echo $service['alamat_service'] ?><br>
				Telepon : <?php echo $service['telepon_service'] ?><br>
			</address>
		</div>
	</div>
	<!-- /.row -->

	<!-- Table row -->
	<div class="row">
		<div class="col-xs-12 table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Jenis Barang</th>
						<th>Kerusakan</th>
						<th>Kelengkapan</th>
						<th>Serial Number</th>
						<th>Garansi</th>
						<th>Ketentuan Garansi</th>
						<th>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo $service['jenis_barang'] ?></td>
						<td><?php echo $service['kerusakan'] ?></td>
						<td><?php echo $service['kelengkapan'] ?></td>
						<td><?php echo $service['serial_number'] ?></td>
						<td><?php echo $service['garansi'] ?></td>
						<td><?php echo $service['ketentuan_garansi'] ?></td>
						<td><?php echo $service['keterangan'] ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<!-- /.col -->
	</div>
	
	<!-- /.row -->
	<!-- this row will not appear when printing -->
	<div class="row no-print">
		<div class="col-xs-12">
			<a href="<?php echo base_url('service/invoice_cetak/' . $service['id_service']) ?>" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Cetak</a>
			<a style="margin-right: 1em" href="<?php echo base_url('service') ?>"  class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> Kembali</a>
		</div>
	</div>
</section>