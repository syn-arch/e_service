<div class="box box-danger">
	<div class="box-header with-border">
		<div class="pull-left">
			<h4 class="box-title"><?php echo $judul ?></h4>
		</div>
		<div class="pull-right">
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
		<?php if ($this->input->get('dari')): ?>
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

								$

								<?php $no=1; foreach ($tanggal as $row): ?>
								<tr>
									<td><?= $no++ ?>.</td>
									<td><?= $tgl = $row['tgl'] ?></td>
									<?php 
									$q1 = "SELECT * FROM service WHERE DATE(tgl_service) = '$tgl'";
									$masuk = $this->db->query($q1)->num_rows();
									$q2 = "SELECT * FROM service WHERE DATE(tgl_ambil) = '$tgl'";
									$keluar = $this->db->query($q2)->num_rows();
									$q3 = "SELECT SUM(total_bayar) AS pendapatan FROM service WHERE DATE(tgl_ambil) = '$tgl'";
									$pendapatan = $this->db->query($q3)->row()->pendapatan;?>
									<td><?php echo $masuk ?></td>
									<td><?php echo $keluar ?></td>
									<td><?php echo "Rp. " . number_format($pendapatan) ?></td>
								</tr>

								<?php $i=1; foreach ($this->db->get('karyawan')->result_array() as $row): ?>
								<?php $id_karyawan = $row['id_karyawan'] ?>
								<tr>
									<td><?= $i++ ?>)</td>
									<td><?= $row['nama_karyawan'] ?></td>
									<?php 
									$q1 = "SELECT * FROM service WHERE DATE(tgl_service) = '$tgl' AND id_karyawan = '$id_karyawan' ";
									$masuk = $this->db->query($q1)->num_rows();
									$q2 = "SELECT * FROM service WHERE DATE(tgl_ambil) = '$tgl' AND id_karyawan = '$id_karyawan' ";
									$keluar = $this->db->query($q2)->num_rows();
									$q3 = "SELECT SUM(total_bayar) AS pendapatan FROM service WHERE DATE(tgl_ambil) = '$tgl'  AND id_karyawan = '$id_karyawan'";
									$pendapatan = $this->db->query($q3)->row()->pendapatan;?>
									<td><?php echo $masuk ?></td>
									<td><?php echo $keluar ?></td>
									<td><?php echo "Rp. " . number_format($pendapatan) ?></td>
								</tr>
							<?php endforeach ?>

						<?php endforeach; ?>

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
<?php endif ?>
</div>
</div>