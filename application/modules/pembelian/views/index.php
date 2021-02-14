<style>
  .pembelian-item {
    display:block;
    height:150px;
    overflow:auto;
  }
  .thead-item, .pembelian-item tr {
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

      <form action="<?php echo base_url('pembelian/proses') ?>" method="POST" class="form-pembelian" enctype="multipart/form-data">
        <input type="hidden" name="id_petugas" value="<?php echo $this->session->userdata('id_petugas'); ?>">
        <input type="hidden" name="faktur_pembelian" value="<?php echo faktur_no(true) ?>">
        <div class="form-group">
          <select name="id_supplier" id="id_supplier" class="form-control select2 supplier">
            <?php foreach ($supplier as $row): ?>
              <option value="<?php echo $row['id_supplier'] ?>"><?php echo $row['nama_supplier'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="form-group <?php if(form_error('barcode')) echo 'has-error'?>">
          <input autofocus="" type="text" id="barcode" name="barcode" class="form-control barcode" placeholder="Barcode" autocomplete="off">
        </div>
        <div class="form-group table-responsive">
          <table class="table">
            <thead class="thead-item">
              <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th><i class="fa fa-gear"></i></th>
              </tr>
            </thead>
            <tbody class="pembelian-item">

            </tbody>
          </table>
        </div>
      </div>
      <div class="row" style="margin-top: -30px">
        <div class="col-md-12 table-responsive">
          <table class="table">
            <tr>
              <th>Tgl Pembelian</th>
              <td><input required="" type="datetime-local" class="form-control" name="tgl_pembelian" ></td>
            </tr>
            <tr>
              <th>Referensi</th>
              <td><input type="text" class="form-control" name="referensi" placeholder="Referensi"></td>
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
              <td><input type="date" class="form-control" name="tgl_jatuh_tempo"></td>
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
              <th>Total Bayar</th>
              <td><input readonly="" type="text" class="form-control jumlah_bayar" name="jumlah_bayar" value="Rp. 0"></td>
            </tr>
            <tr>
              <th>Cash</th>
              <td><input type="number" class="form-control cash" name="cash"></td>
            </tr>
            <tr>
              <th>Kembalian</th>
              <td><input readonly="" type="text" class="form-control kembalian"></td>
            </tr>
            <tr>
              <td colspan="2">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Konfirmasi</button>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <button type="submit" class="btn btn-danger btn-block btn-flat batal">Batal</button>
              </td>
            </tr>
          </table>
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
            <?php echo faktur_no(true) ?>
          </div>
          <div class="pull-right">
            <input type="text" class="jumlah_bayar" style="text-align: right;border:none; font-size: 30px" value="Rp. 0">
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
            <input type="search" name="cari_barang" id="cari_barang" class="form-control cari_barang_nama" placeholder="Cari barang">
            <span class="input-group-btn">
              <button type="button" data-toggle="modal" data-target="#modal-barang" class="btn btn-info btn-flat cari_barang"><i class="fa fa-search"></i></button>
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


<script>
  <?php 

  echo "const judul = " . "'$judul'";

   ?>
</script>