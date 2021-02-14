<div class="box box-danger">
	<div class="box-header with-border">
		<div class="pull-left">
			<h4 class="box-title"><?php echo $judul ?></h4>
		</div>
		<div class="pull-right">
			<?php if ($this->input->get('dari') && $this->input->get('sampai')): ?>
			<a href="<?php echo base_url('laporan/export_service/' . $this->input->get('dari') . '/' . $this->input->get('sampai')) ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
			<?php else: ?>
				<a href="<?php echo base_url('laporan/export_service') ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
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
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Tanggal</th>
								<th>Masuk</th>
								<th>Keluar</th>
								<th>Pendapatan</th>
							</tr>
						</thead>
						<tbody>

							<?php $no=1; foreach ($laporan_keluar as $index => $row): ?>
							<tr>
								<td><?= $no++ ?></td>
								<td><?= $row['tgl'] ?></td>
								<td><?php echo $laporan_masuk[$index]['masuk'] ?></td>
								<td><?= $row['keluar'] ?></td>
								<td><?= "Rp. " .  number_format($row['pendapatan']) ?></td>
							</tr>
							<?php 

							$tgl_service = $row['tgl'];

							$query = "
							SELECT 
							nama_karyawan,
							SUM(k) as keluar, 
							SUM(total_bayar) as pendapatan from (
							SELECT id_karyawan, nama_karyawan, tgl_service, total_bayar, if(status = 'TELAH DITERIMA',1,0) k FROM service JOIN karyawan USING(id_karyawan) WHERE DATE(tgl_service) = '$tgl_service'
							) as t
							group by t.id_karyawan
							";

							$laporan_teknisi = $this->db->query($query)->result_array();

							$query1 = "
							SELECT 
							SUM(m) as masuk from (
							SELECT tgl_service, id_karyawan, total_bayar, if(status = 'BELUM DITERIMA',1,0) as m from service JOIN karyawan USING(id_karyawan) WHERE DATE(tgl_service) = '$tgl_service'
							) as t
							group by t.id_karyawan";

							$lp_masuk = $this->db->query($query1)->result_array();

							?>
							<?php foreach ($laporan_teknisi as $index => $row): ?>
								<tr>
									<td></td>
									<td><?php echo $row['nama_karyawan'] ?></td>
									<td><?php echo $lp_masuk[$index]['masuk'] ?></td>
									<td><?php echo $row['keluar'] ?></td>
									<td><?php echo "Rp. " . number_format($row['pendapatan']) ?></td>
								</tr>
							<?php endforeach ?>
						<?php endforeach ?>

					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Total service</th>
							<th><?= "Rp. " . number_format($total_service) ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
</div>