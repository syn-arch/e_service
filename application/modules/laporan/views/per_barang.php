<div class="box box-danger">
	<div class="box-header with-border">
		<div class="pull-left">
			<h4 class="box-title"><?php echo $judul ?></h4>
		</div>
		<div class="pull-right">
			<?php if ($dari && $sampai): ?>
				<a target="_blank" href="<?php echo base_url('laporan/cetak_per_barang/' . $dari . '/' . $sampai ) ?>" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
				<a href="<?php echo base_url('laporan/export_per_barang/'. $dari . '/' . $sampai ) ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
				<?php else: ?>
					<a target="_blank" href="<?php echo base_url('laporan/cetak_per_barang/') ?>" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
					<a href="<?php echo base_url('laporan/export_per_barang/') ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
				<?php endif ?>
				<a href="<?= base_url('laporan/penjualan') ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Kembali</a>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-lg-6">
					<form action="">
						<div class="form-group">
							<label for="">Dari Tanggal</label>
							<input type="date" name="dari" id="dari" class="form-control">
						</div>
						<div class="form-group">
							<label for="">Sampai Tanggal</label>
							<input type="date" name="sampai" id="sampai" class="form-control">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-danger btn-block">Submit</button>
						</div>
					</form>
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table datatable">
							<thead>
								<tr>
									<th>#</th>
									<th>Kode</th>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th>Terjual</th>
									<th>Harga Beli</th>
									<th>Harga Jual</th>
									<th>Diskon</th>
									<th>Profit</th>
									<th class="sum">Total</th>
									<th>Laba</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=1; foreach ($laporan as $row): ?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $row['id_barang'] ?></td>
									<td><?= $row['barcode'] ?></td>
									<td><?= $row['nama_barang'] ?></td>
									<td><?= $row['barang_terjual'] ?></td>
									<td><?= number_format($row['harga_pokok']) ?></td>
									<td><?= number_format($row['harga_jual']) ?></td>
									<td><?= number_format($row['diskon']) ?></td>
									<td><?= number_format($row['profit']) ?></td>
									<td><?= number_format($row['total']) ?></td>
									<td class="laba"><?= number_format($row['laba']) ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="box-footer">
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
		</div>
	</div>
</div>