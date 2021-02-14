<style>
  .penjualan-item {
    display:block;
    height:250px;
    overflow:auto;
  }
  .thead-item, .penjualan-item tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
  }
  thead {
    width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
  }
  table {
    width:400px;
  }
  .font_small {
    font-size: 14px;
  }

</style>

<div class="row">
  <div class="col-md-5">
    <div class="box box-danger">
      <div class="box-header with-border">
        <div class="pull-left">
         Tanggal : <?php echo date('d-m-Y') ?>
       </div>
       <div class="pull-right">
         Kasir : <?php echo $this->session->userdata('nama_petugas'); ?>
       </div>
     </div>
     <div class="box-body">

      <form action="<?php echo base_url('penjualan/proses') ?>" method="POST" class="form-penjualan" enctype="multipart/form-data">
        <input type="hidden" name="id_petugas" value="<?php echo $this->session->userdata('id_petugas'); ?>">
        <input type="hidden" name="faktur_penjualan" value="<?php echo 'SL-'.acak(10) ?>">
        <input type="hidden" class="member" name="member" value="0">
        <input type="hidden" name="id_service" value="">
        <div class="pelanggan_baru"></div>
        <div class="form-group">
          <div class="input-group input-group">
            <input type="text" class="form-control barcode_pelanggan" name="barcode_pelanggan" placeholder="Barcode Pelanggan">
            <span class="input-group-btn">
              <button type="button" class="btn btn-info btn-flat"><i class="fa fa-barcode"></i></button>
            </span>
          </div>
        </div>
        <div class="form-group">
          <div class="input-group input-group">
            <select required="" name="id_pelanggan" id="id_pelanggan" class="form-control pelanggan pelanggan-wrapper">
              <?php foreach ($pelanggan as $row): ?>
                <option 
                <?php  
                $this->db->where('id_pelanggan', $row['id_pelanggan']);
                $this->db->where('status', 'Belum Lunas');
                echo $this->db->get('penjualan')->num_rows() >= 3 ? 'disabled' : '' ?> value="<?php echo $row['id_pelanggan'] ?>">
                <?php echo $row['nama_pelanggan'] ?>
              </option>
            <?php endforeach ?>
          </select>
          <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat"><i class="fa fa-users"></i></button>
          </span>
        </div>
      </div>
      <div class="form-group">
        <div class="input-group input-group">
          <select required="" name="id_karyawan" id="id_karyawan" class="form-control select2 karyawan karyawan-wrapper" width="100%">
            <option value="">-- Pilih Karyawan --</option>
            <?php foreach ($karyawan as $row): ?>
              <option <?php echo $this->session->userdata('id_karyawan') == $row['id_karyawan'] ? 'selected' : '' ?> value="<?php echo $row['id_karyawan'] ?>"><?php echo $row['nama_karyawan'] ?></option>
            <?php endforeach ?>
          </select>
          <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat"><i class="fa fa-users"></i></button>
          </span>
        </div>
      </div>
      <div class="form-group">
        <div class="input-group input-group">
          <select required="" name="id_service" id="id_service" class="form-control select2 service service-wrapper" width="100%">
            <option value="">-- Pilih service --</option>
            <?php foreach ($service as $row): ?>
              <option value="<?php echo $row['id_service'] ?>"> <?php echo $row['id_service'] ?> | <?php echo $row['nama_service'] ?></option>
            <?php endforeach ?>
          </select>
          <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat"><i class="fa fa-users"></i></button>
          </span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <input type="text" class="form-control qty_brg" placeholder="Qty" autofocus="">
          </div>
        </div>
        <div class="col-md-10">
          <div class="form-group <?php if(form_error('barcode')) echo 'has-error'?>">
            <input type="text" id="barcode" name="barcode" class="form-control barcode" placeholder="Barcode" autocomplete="off">
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="table-responsive">
          <table class="table">
            <thead class="thead-item">
              <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th><i class="fa fa-gear"></i></th>
              </tr>
            </thead>
            <tbody class="penjualan-item">

            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row" style="margin-top: -30px">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>Diskon (%) | Potongan</th>
              <td><input type="number" class="form-control diskon" name="diskon" autocomplete="off" value="0"></td>
              <td><input type="number" class="form-control potongan" name="potongan" autocomplete="off" value="0"></td>
            </tr>
            <tr class="harga_jasa_wrap">
              <th>Harga Jasa</th>
              <td colspan="2"><input type="number" class="form-control harga_jasa" name="harga_jasa" value="0"></td>
            </tr>
            <tr class="garansi_wrap">
              <th>Garansi</th>
              <td colspan="2"><input type="text" class="form-control garansi" name="garansi" placeholder="Garansi"></td>
            </tr>
            <tr class="ketentuan_garansi_wrap">
              <th>Ketentuan Garansi</th>
              <td colspan="2"><input type="text" class="form-control ketentuan_garansi" name="ketentuan_garansi" placeholder="Ketentuan Garansi"></td>
            </tr>
            <tr>
              <th>Jumlah Bayar</th>
              <td colspan="2"><input readonly="" type="text" class="form-control jumlah_bayar" name="jumlah_bayar" value="Rp. 0"></td>
            </tr>
            <tr>
              <th>Metode</th>
              <td colspan="2">
                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control metode_pembayaran">
                  <option value="Cash" selected>Cash</option>
                  <option value="Debit">Debit</option>
                  <option value="Kredit">Kredit</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>Jatuh Tempo</th>
              <td colspan="2"><input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" class="form-control"></td>
            </tr>
            <tr>
              <th>Cash</th>
              <td colspan="2"><input placeholder="Cash" required="true" type="number" class="form-control cash" name="cash"></td>
            </tr>
            <tr>
              <th>Nama</th>
              <td colspan="2"><input type="text" placeholder="Nama" name="nama_pengiriman" id="nama_pengiriman" class="form-control"></td>
            </tr>
            <tr>
              <th>Alamat</th>
              <td colspan="2"><input type="text" placeholder="Alamat" name="alamat_pengiriman" id="alamat_pengiriman" class="form-control"></td>
            </tr>
            <tr class="no_kredit">
              <th>No Kredit</th>
              <td colspan="2">
                <input type="text" name="no_kredit" id="no_kredit" class="form-control" placeholder="No Kredit">
              </td>
            </tr>
            <tr class="no_debit">
              <th>No Debit</th>
              <td colspan="2">
                <input type="text" name="no_debit" id="no_debit" class="form-control" placeholder="No ebit">
              </td>
            </tr>
            <tr class="lampiran">
              <th>Lampiran</th>
              <td colspan="2">
                <input type="file" name="lampiran" id="lampiran" class="form-control">
              </td>
            </tr>
            <tr>
              <th>Kembalian</th>
              <td colspan="2"><input readonly="" type="text" class="form-control kembalian"></td>
            </tr>
            <tr>
              <td colspan="3">
                <button type="submit" class="btn btn-primary btn-block btn-flat konfirmasi-penjualan">Konfirmasi</button>
              </td>
            </tr>
            <tr>
              <td colspan="3">
                <button type="submit" class="btn btn-danger btn-block btn-flat batal">Batal</button>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </form>
</div>
</div>
<div class="col-md-7">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-danger">
        <div class="box-header with-border">
          <div class="pull-left">
            <?php echo 'SL-'.acak(10) ?>
          </div>
          <div class="pull-right">
            <input type="text" class="total_jumlah_bayar" style="text-align: right;border:none; font-size: 30px" value="Rp. 0">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-danger">
        <div class="box-header with-border">
          <div class="pull-left">
            <h4>Data Barang</h4>
          </div>
          <div class="pull-right">
            <div class="input-group input-group">
              <input type="search" name="cari_barang" id="cari_barang" class="form-control cari_barang_name" placeholder="Cari barang">
              <span class="input-group-btn">
                <button data-toggle="modal" data-target="#modal-barang" type="button" class="btn btn-info btn-flat cari_barang" title="Cari Barang"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="box-body">
         <div class="form-group">
          <select name="id_kategori" id="id_kategori" class="form-control select2 kategori">
            <option value="pilih">-- Silahkan pilih kategori --</option>
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori as $row): ?>
              <option value="<?php echo $row['id_kategori'] ?>"><?php echo $row['nama_kategori'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">    
              <table class="table text-center">
                <thead>
                  <tr>
                    <th width="15%">Gambar</th>
                    <th width="40%">Nama</th>
                    <th width="20%">Harga</th>
                    <th width="10%"><i class="fa fa-edit"></i></th>
                  </tr>
                </thead>
                <tbody class="barang-kategori">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<!-- tambah pelanggan -->
<div class="modal fade" id="tambah-pelanggan" tabindex="-1" role="dialog" aria-labelledby="tambah-pelangganTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLongTitle">Tambah Pelanggan</h4>
      </div>
      <div class="modal-body">
        <form method="POST" class="tambah-pelanggan">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group <?php if(form_error('id')) echo 'has-error'?>">
                <label for="id">ID pelanggan</label>
                <input readonly="" type="text" id="id" name="id" class="form-control id " placeholder="ID pelanggan" value="<?php echo autoID('PLG', 'pelanggan') ?>">
                <?php echo form_error('id', '<small style="color:red">','</small>') ?>
              </div>
              <div class="form-group <?php if(form_error('nama_pelanggan')) echo 'has-error'?>">
               <label for="nama_pelanggan">Nama pelanggan</label>
               <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control nama_pelanggan" placeholder="Nama pelanggan" value="<?php echo set_value('nama_pelanggan') ?>">
               <?php echo form_error('nama_pelanggan', '<small style="color:red">','</small>') ?>
             </div>
             <div class="form-group <?php if(form_error('jk')) echo 'has-error'?>">
               <label for="jk">Jenis Kelamin</label><br>
               <select name="jk" id="jk" class="form-control">
                 <option value="">-- Silahkan Pilih Jenis Kelamin --</option>
                 <option value="L" <?php echo set_value('jk') == "L" ? 'selected' : '' ?>>Laki-Laki</option>
                 <option value="P" <?php echo set_value('jk') == "P" ? 'selected' : '' ?>>Perempuan</option>
               </select>
               <?php echo form_error('jk', '<small style="color:red">','</small>') ?>
             </div>
             <div class="form-group <?php if(form_error('telepon')) echo 'has-error'?>">
               <label for="telepon">Telepon</label>
               <input type="text" id="telepon" name="telepon" class="form-control telepon " placeholder="Telepon" value="<?php echo set_value('telepon') ?>">
               <?php echo form_error('telepon', '<small style="color:red">','</small>') ?>
             </div>
           </div>
           <div class="col-md-6">
             <div class="form-group <?php if(form_error('jenis')) echo 'has-error'?>">
               <label for="jenis">Jenis Pelanggan</label><br>
               <select name="jenis" id="jenis" class="form-control">
                 <option value="">-- Silahkan Pilih Jenis Pelanggan --</option>
                 <option value="Umum" <?php echo set_value('jenis') == "Umum" ? 'selected' : '' ?>>Umum</option>
                 <option value="Member" <?php echo set_value('jenis') == "Member" ? 'selected' : '' ?>>Member</option>
               </select>
               <?php echo form_error('jenis', '<small style="color:red">','</small>') ?>
             </div>
             <div class="form-group <?php if(form_error('alamat')) echo 'has-error'?>" >
               <label for="alamat">Alamat</label>
               <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control " placeholder="alamat"><?php echo set_value('alamat') ?></textarea>
               <?php echo form_error('alamat', '<small style="color:red">','</small>') ?>
             </div>

           </div>
         </div>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </form>
    </div>
  </div>
</div>
</div>

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
              <table class="table" id="table-cari-barang" width="100%">
                <thead>
                  <tr>
                    <th>Kode Barang</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
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

<input type="hidden" name="ubah_brg" class="ubah_brg">
<input type="hidden" name="id_brg" class="id_brg">
<input type="hidden" name="qty_brg" class="qty_brg">
<input type="hidden" name="harga_brg" class="harga_brg">

<div class="modal fade" id="input_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="exampleModalLongTitle">Masukan Password</h5>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <input type="password" class="form-control input_password" placeholder="Masukan Password">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>


<script>
  <?php 

  $pengaturan = $this->db->get('pengaturan')->row_array();

  echo "const pengaturan = " . json_encode($pengaturan). "; ";

  echo "const judul = '" . $judul . "';";

  ?>
</script>