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

      <form action="<?php echo base_url('penjualan/proses_update') ?>" method="POST" class="form-penjualan" enctype="multipart/form-data">
        <input type="hidden" name="id_outlet" value="<?php echo $penjualan['id_outlet']; ?>">
        <input type="hidden" name="id_petugas" value="<?php echo $this->session->userdata('id_petugas'); ?>">
        <input type="hidden" name="faktur_penjualan" value="<?php echo $penjualan['faktur_penjualan'] ?>">
        <input type="hidden" name="id_service" value="">
        <?php if ($penjualan['jenis'] == 'member'): ?>
          <input type="hidden" class="member" name="member" value="1">
          <?php else: ?>
            <input type="hidden" class="member" name="member" value="0">
          <?php endif ?>
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
              <select required="" name="id_pelanggan" id="id_pelanggan" class="form-control pelanggan">
                <?php foreach ($pelanggan as $row): ?>
                  <option 
                  <?php  
                  echo $penjualan['id_pelanggan'] == $row['id_pelanggan'] ? 'selected' : ''; ?> 

                  value="<?php echo $row['id_pelanggan'] ?>">
                  <?php echo $row['nama_pelanggan'] ?>
                <?php endforeach ?>
              </select>
              <span class="input-group-btn">
                <button type="button"  class="btn btn-info btn-flat"><i class="fa fa-users"></i></button>
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group input-group">
              <select required="" name="id_karyawan" id="id_karyawan" class="form-control select2 karyawan karyawan-wrapper">
                <?php foreach ($karyawan as $row): ?>
                  <option <?php echo $penjualan['id_karyawan'] == $row['id_karyawan'] ? 'selected' : '' ?> value="<?php echo $row['id_karyawan'] ?>"><?php echo $row['nama_karyawan'] ?></option>
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

                  <?php if ($kunci_penjualan == 1): ?>
                    <?php foreach ($detail_penjualan as $row): ?>
                      <?php 

                      $harga_jual = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row()->harga_jual;

                      $rpdiskon = ($row['diskon'] / 100) * $harga_jual;
                      $tot = $harga_jual - $rpdiskon;
                      $harga_brg = $tot;
                      $harga_asli = $harga_jual;
                      ?>
                      <tr data-id="<?php echo $row['id_barang'] ?>">
                        <input type="hidden" name="id_barang[]" value="<?php echo $row['id_barang'] ?>">
                        <input type="hidden" name="type_golongan[]" value="<?php echo $row['type_golongan'] ?>">
                        <td><?php echo $row['nama_pendek'] ?></td>
                        <td><?php echo number_format($harga_asli) ?></td>
                        <td><?php echo $row['diskon'] ?></td>
                        <td><input readonly class="form-control qty" name="jumlah[]" data-id="<?php echo $row['id_barang'] ?>" data-harga="<?php echo $harga_brg ?>" type="number" value="<?php echo $row['jumlah'] ?>" style="width: 5em"></td>
                        <td class="subtotal" data-kode="<?php echo $row['id_barang'] ?>"><?php echo number_format($row['total_harga']) ?></td>
                        <td>
                          <a class="btn btn-danger fa fa-trash hapus_kunci_brg" data-type="hapus" data-harga="<?php echo $harga_brg ?>" data-qty="<?php echo $row['jumlah'] ?>" data-id="<?php echo $row['id_barang'] ?>"></a>
                          <a class="btn btn-warning fa fa-edit ubah_kunci_brg" data-type="ubah" data-harga="<?php echo $harga_brg ?>" data-qty="<?php echo $row['jumlah'] ?>" data-id="<?php echo $row['id_barang'] ?>"></a>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?>

                  <?php if ($kunci_penjualan == 0): ?>
                    <?php foreach ($detail_penjualan as $row): ?>
                      <?php 

                      $harga_jual = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row()->harga_jual;

                      $rpdiskon = ($row['diskon'] / 100) * $harga_jual;
                      $tot = $harga_jual - $rpdiskon;
                      $harga_brg = $tot;
                      $harga_asli = $harga_jual;
                      ?>
                      <tr data-id="<?php echo $row['id_barang'] ?>">
                        <input type="hidden" name="id_barang[]" value="<?php echo $row['id_barang'] ?>">
                        <td><?php echo $row['nama_pendek'] ?></td>
                        <td><?php echo number_format($harga_asli) ?></td>
                        <td><?php echo $row['diskon'] ?></td>
                        <td><input class="form-control qty" name="jumlah[]" data-id="<?php echo $row['id_barang'] ?>" data-harga="<?php echo $harga_brg ?>" type="number" value="<?php echo $row['jumlah'] ?>" style="width: 5em"></td>
                        <td class="subtotal" data-kode="<?php echo $row['id_barang'] ?>"><?php echo number_format($row['total_harga'], '0','','.') ?></td>
                        <td><a class="btn btn-danger btn-flat hapus-barang" data-id="<?php echo $row['id_barang'] ?>" data-harga="<?php echo $harga_brg ?>"><i class="fa fa-trash"></i></a></td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?>

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
                  <td><input type="number" class="form-control diskon" name="diskon" autocomplete="off" value="<?php echo $penjualan['diskon'] ?>"></td>
                  <td><input type="number" class="form-control potongan" name="potongan" autocomplete="off" value="<?php echo $penjualan['potongan'] ?>"></td>
                </tr>
                <tr>
                  <th>Harga Jasa</th>
                  <td colspan="2"><input type="text" class="form-control harga_jasa" name="harga_jasa" value="<?php echo $penjualan['harga_jasa'] ?>"></td>
                </tr>
                <tr>
                  <th>Jumlah Bayar</th>
                  <td colspan="2"><input readonly="" type="text" class="form-control jumlah_bayar" name="jumlah_bayar" value="<?php echo "Rp. " . number_format($penjualan['total_bayar']) ?>"></td>
                </tr>
                <tr>
                  <th>Jatuh Tempo</th>
                  <td colspan="2"><input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" class="form-control" value="<?php echo $penjualan['tgl_jatuh_tempo'] ?>"></td>
                </tr>
                <tr>
                  <th>Nama</th>
                  <td colspan="2"><input type="text" placeholder="Nama" value="<?php echo $penjualan['nama_pengiriman'] ?>" name="nama_pengiriman" id="nama_pengiriman" class="form-control"></td>
                </tr>
                <tr>
                  <th>Alamat</th>
                  <td colspan="2"><input type="text" placeholder="Alamat" value="<?php echo $penjualan['alamat_pengiriman'] ?>" name="alamat_pengiriman" id="alamat_pengiriman" class="form-control"></td>
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
              <?php echo $penjualan['faktur_penjualan']; ?>
            </div>
            <div class="pull-right">
              <input type="text" class="total_jumlah_bayar" style="text-align: right;border:none; font-size: 30px" value="<?php echo "Rp. " . number_format($penjualan['total_bayar']) ?>">
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
              <div class="form-group">
                <div class="input-group input-group">
                  <select name="golongan" id="golongan" class="form-control select2 golongan">
                    <option value="golongan_1">Golongan 1</option>
                    <option value="golongan_2">Golongan 2</option>
                    <option value="golongan_3">Golongan 3</option>
                    <option value="golongan_4">Golongan 4</option>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-info btn-flat"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
              <div class="input-group input-group">
                <input readonly="" data-toggle="modal" data-target="#modal-barang" type="search" name="cari_barang" id="cari_barang" class="form-control cari_barang" placeholder="Cari barang">
                <span class="input-group-btn">
                  <button data-toggle="modal" data-target="#modal-barang" type="button" class="btn btn-info btn-flat cari_barang" title="Cari Barang"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
          </div>
          <div class="box-body">
           <div class="form-group">
            <select name="id_kategori" id="id_kategori" class="form-control select2 kategori" disabled="">
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

  echo "const pengaturan = " . json_encode($pengaturan) . "; ";

  echo "const judul = '" . $judul. "'; ";

  echo "const total_bayar_rp = " . $penjualan['total_bayar']. "; ";


  ?>
</script>   