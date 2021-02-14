<form method="POST" action="<?php echo base_url('utilitas/print_label') ?>">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h4 class="box-title">Masukan Barang Yang Akan Di Cetak</h4>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="input-group input-group">
									<input type="text" class="form-control cari_brg" placeholder="Barcode Atau Kode Barang">
									<span class="input-group-btn">
										<button type="button" data-toggle="modal" data-target="#modal-barang" class="btn btn-info btn-flat"><i class="fa fa-plus"></i></button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table" width="100%">
									<thead>
										<tr>
											<th>Kode Barang</th>
											<th>Barcode</th>
											<th>Nama Barang</th>
											<th><i class="fa fa-trash"></i></th>
										</tr>
									</thead>
									<tbody class="barang-barang">

									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group <?php if(form_error('jumlah')) echo 'has-error'?>">
								<input  required="" type="text" id="jumlah" name="jumlah" class="form-control jumlah " placeholder="Jumlah label yang akan dicetak" value="<?php echo set_value('jumlah') ?>">
								<?php echo form_error('jumlah', '<small style="color:red">','</small>') ?>
							</div>
							<button type="submit" class="btn btn-danger btn-block">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal -->
<div class="modal fade" id="modal-barang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="exampleModalLongTitle">Cari Barang</h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table" id="table-tambah-barang" width="100%">
								<thead>
									<tr>
										<th>Kode Barang</th>
										<th>Nama Barang</th>
										<th><i class="fa fa-plus"></i></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>