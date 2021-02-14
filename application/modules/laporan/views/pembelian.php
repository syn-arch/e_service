<div class="box box-danger">
	<div class="box-header with-border">
		<div class="pull-left">
			<h4 class="box-title"><?php echo $judul ?></h4>
		</div>
		<div class="pull-right">
			<?php if ($this->input->get('dari') && $this->input->get('sampai')): ?>
				<a href="<?php echo base_url('laporan/export_pembelian/' . $this->input->get('dari') . '/' . $this->input->get('sampai')) ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
				<?php else: ?>
					<a href="<?php echo base_url('laporan/export_pembelian') ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
				<?php endif ?>
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
						<div class="form-group">
							<button type="submit" class="btn btn-danger btn-block">Submit</button>
						</div>
					</form>
				</div>
			</div>
			<br>
			<br>
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
									<th>Harga</th>
									<th>Jumlah</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>

								<?php $no=1; foreach ($laporan as $row): ?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $row['id_barang'] ?></td>
									<td><?= $row['barcode'] ?></td>
									<td><?= $row['nama_barang'] ?></td>
									<td><?= "Rp. " . number_format($row['harga_pokok']) ?></td>
									<td><?= $row['barang_terbeli'] ?></td>
									<td><?= "Rp. " . number_format($row['total']) ?></td>
								</tr>
							<?php endforeach ?>

						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total Pembelian</th>
								<th><?= "Rp. " . number_format($total_pembelian) ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>