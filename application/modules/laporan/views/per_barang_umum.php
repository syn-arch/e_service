<div class="box box-danger">
	<div class="box-header with-border">
		<div class="pull-left">
			<h4 class="box-title"><?php echo $judul ?></h4>
		</div>
		<div class="pull-right">
			<?php if ($dari = $this->input->get('dari') && $sampai = $this->input->get('sampai')): ?>
			<a target="_blank" href="<?php echo base_url('laporan/cetak_per_barang/umum/' . $dari . '/' . $sampai . '/' . $this->input->get('id_outlet')) ?>" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
			<a href="<?php echo base_url('laporan/export_per_barang/umum/'. $dari . '/' . $sampai . '/' . $this->input->get('id_outlet')) ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
			<?php else: ?>
				<a href="<?php echo base_url('laporan/cetak_per_barang/umum') ?>" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
			<a href="<?php echo base_url('laporan/export_per_barang/umum') ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
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
						<input type="date" name="sampai" id="dari" class="form-control">
					</div>
					<div class="form-group <?php if(form_error('id_outlet')) echo 'has-error'?>">
						<label for="id_outlet">Outlet</label>
						<select name="id_outlet" id="id_outlet" class="form-control">
							<option value="">Semua Outlet</option>
							<?php foreach ($outlet as $row): ?>
								<option value="<?php echo $row['id_outlet'] ?>"><?php echo $row['nama_outlet'] ?></option>
							<?php endforeach ?>
						</select>
						<?php echo form_error('id_outlet', '<small style="color:red">','</small>') ?>
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
								<th>Total</th>
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
			<div class="row">
				<div class="col-md-6 table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Total Pendapatan Umum</th>
								<th><?php echo "Rp. " . number_format($total_umum) ?></th>
							</tr>
							<tr>
								<th>Total Pendapatan Member</th>
								<th><?php echo "Rp. " . number_format($total_member) ?></th>
							</tr>
							<tr>
								<th>Total Keseluruhan</th>
								<th><?php echo "Rp. " . number_format($total_umum + $total_member) ?></th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-6 table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Total Laba Umum</th>
								<th><?php echo "Rp. " . number_format($laba_umum) ?></th>
							</tr>
							<tr>
								<th>Total Laba Member</th>
								<th><?php echo "Rp. " . number_format($laba_member) ?></th>
							</tr>
							<tr>
								<th>Total Keseluruhan</th>
								<th><?php echo "Rp. " . number_format($laba_umum + $laba_member) ?></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>