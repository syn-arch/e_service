<form method="POST" enctype="multipart/form-data">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-danger">
        <div class="box-header with-border">
          <div class="pull-left">
            <div class="box-title">
              <h4><?php echo $judul ?></h4>
            </div>
          </div>
        </div>
        <div class="box-body">
         <div class="row">
          <div class="col-md-6">
            <div class="form-group <?php if(form_error('id_service')) echo 'has-error'?>">
              <label for="id_service">ID service</label>
              <input readonly="" type="text" id="id_service" name="id_service" class="form-control id_service " placeholder="ID service" value="<?php echo 'SV-'.acak(10) ?>">
              <?php echo form_error('id_service', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('id_karyawan')) echo 'has-error'?>">
              <label for="id_karyawan">Karyawan</label>
              <select required name="id_karyawan" id="id_karyawan" class="form-control select2">
                <option value="">-- Pilih karyawan --</option>
                <?php foreach ($karyawan as $row): ?>
                  <option value="<?php echo $row['id_karyawan'] ?>"><?php echo $row['nama_karyawan'] ?></option>
                <?php endforeach ?>
              </select>
              <?php echo form_error('id_karyawan', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group">
              <label for="nama_service">Nama Pelanggan</label>
              <input type="text" id="nama_service" name="nama_service" class="form-control nama_service <?php if(form_error('nama_service')) echo 'is-invalid'?>" placeholder="Nama Pelanggan" value="<?php echo set_value('nama_service') ?>">
              <?php echo form_error('nama_service', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group">
              <label for="alamat_service">Alamat Pelanggan</label>
              <input type="text" id="alamat_service" name="alamat_service" class="form-control alamat_service <?php if(form_error('alamat_service')) echo 'is-invalid'?>" placeholder="Alamat Pelanggan" value="<?php echo set_value('alamat_service') ?>">
              <?php echo form_error('alamat_service', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group">
              <label for="telepon_service">Telepon Service</label>
              <input type="text" id="telepon_service" name="telepon_service" class="form-control telepon_service <?php if(form_error('telepon_service')) echo 'is-invalid'?>" placeholder="Telepon Service" value="<?php echo set_value('telepon_service') ?>">
              <?php echo form_error('telepon_service', '<small style="color:red">','</small>') ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group <?php if(form_error('tgl_service')) echo 'has-error'?>">
              <label for="tgl_service">Tanggal Service</label>
              <input required="" type="datetime-local" id="tgl_service" name="tgl_service" class="form-control tgl_service " placeholder="Tanggal">
              <?php echo form_error('tgl_service', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('tgl_ambil')) echo 'has-error'?>">
              <label for="tgl_ambil">Tanggal Diambil</label>
              <input type="datetime-local" id="tgl_ambil" name="tgl_ambil" class="form-control tgl_ambil " placeholder="Tanggal Diambil" value="<?php echo set_value('tgl_ambil') ?>">
              <?php echo form_error('tgl_ambil', '<small style="color:red">','</small>') ?>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group <?php if(form_error('jenis_barang')) echo 'has-error'?>">
              <label for="jenis_barang">Jenis Barang</label>
              <input type="text" id="jenis_barang" name="jenis_barang" class="form-control jenis_barang" placeholder="Jenis Barang" value="<?php echo set_value('jenis_barang') ?>">
              <?php echo form_error('jenis_barang', '<small style="color:red">','</small>') ?>
            </div> 
            <div class="form-group <?php if(form_error('kerusakan')) echo 'has-error'?>">
              <label for="kerusakan">Kerusakan</label>
              <input type="text" id="kerusakan" name="kerusakan" class="form-control kerusakan " placeholder="Kerusakan" value="<?php echo set_value('kerusakan') ?>">
              <?php echo form_error('kerusakan', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('kelengkapan')) echo 'has-error'?>">
              <label for="kelengkapan">Kelengkapan</label>
              <input type="text" id="kelengkapan" name="kelengkapan" class="form-control kelengkapan " placeholder="Kelengkapan" value="<?php echo set_value('kelengkapan') ?>">
              <?php echo form_error('kelengkapan', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text" id="serial_number" name="serial_number" class="form-control serial_number <?php if(form_error('serial_number')) echo 'is-invalid'?>" placeholder="Serial Number" value="<?php echo set_value('serial_number') ?>">
              <?php echo form_error('serial_number', '<small style="color:red">','</small>') ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group <?php if(form_error('garansi')) echo 'has-error'?>">
              <label for="garansi">Garansi</label>
              <input type="text" id="garansi" name="garansi" class="form-control garansi " placeholder="Garansi" value="<?php echo set_value('garansi') ?>">
              <?php echo form_error('garansi', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('ketentuan_garansi')) echo 'has-error'?>">
              <label for="ketentuan_garansi">Ketentuan Garansi</label>
              <input type="text" id="ketentuan_garansi" name="ketentuan_garansi" class="form-control ketentuan_garansi " placeholder="Ketentuan Garansi" value="<?php echo set_value('ketentuan_garansi') ?>">
              <?php echo form_error('ketentuan_garansi', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('keterangan')) echo 'has-error'?>">
              <label for="keterangan">Keterangan</label>
              <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control keterangan" placeholder="Keterangan"><?php echo set_value('garansi') ?></textarea>
              <?php echo form_error('keterangan', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('status')) echo 'has-error'?>">
              <label for="status">Status</label>
              <select name="status" id="status" class="form-control">
                <option selected="" value="BELUM DITERIMA">BELUM DITERIMA</option>
                <option value="TELAH DITERIMA">TELAH DITERIMA</option>
              </select>
              <?php echo form_error('status', '<small style="color:red">','</small>') ?>
            </div>
          </div>
        </div>
        <br><br>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <button type="submit" class="btn btn-danger btn-block">Konfirmasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
