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
          <div class="pull-right">
            <div class="box-title">
              <a href="<?php echo base_url('master/barang') ?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
          </div>
        </div>
        <div class="box-body">
         <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="form-group <?php if(form_error('id_barang')) echo 'has-error'?>">
              <label for="id_barang">ID barang</label>
              <input readonly="" type="text" id="id_barang" name="id_barang" class="form-control id_barang " placeholder="ID barang" value="<?php echo autoID('BRG', 'barang') ?>">
              <?php echo form_error('id_barang', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('nama_barang')) echo 'has-error'?>">
              <label for="nama_barang">Nama Barang</label>
              <input type="text" id="nama_barang" name="nama_barang" class="form-control nama_barang " placeholder="Nama Barang" value="<?php echo set_value('nama_barang') ?>">
              <?php echo form_error('nama_barang', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('nama_pendek')) echo 'has-error'?>">
              <label for="nama_pendek">Nama Pendek</label>
              <input type="text" id="nama_pendek" name="nama_pendek" class="form-control nama_pendek " placeholder="Nama Pendek" value="<?php echo set_value('nama_pendek') ?>">
              <?php echo form_error('nama_pendek', '<small style="color:red">','</small>') ?>
            </div>
            <div class="form-group <?php if(form_error('id_kategori')) echo 'has-error'?>">
             <label for="id_kategori">Kategori</label>
             <select name="id_kategori" id="id_kategori" class="form-control select2">
               <option value="">-- Silahkan Pilih Kategori ---</option>
               <?php foreach ($kategori as $row): ?>
                 <option value="<?php echo $row['id_kategori'] ?>" <?php echo set_value('id_kategori') == $row['id_kategori'] ? 'selected' : '' ?>><?php echo $row['nama_kategori'] ?></option>
               <?php endforeach ?>
             </select>
             <?php echo form_error('id_kategori', '<small style="color:red">','</small>') ?>
           </div>
           <div class="form-group <?php if(form_error('id_supplier')) echo 'has-error'?>">
            <label for="id_supplier">Supplier</label>
            <select name="id_supplier" id="id_supplier" class="form-control select2">
              <option value="">-- Silahkan Pilih Supplier ---</option>
              <?php foreach ($supplier as $row): ?>
               <option value="<?php echo $row['id_supplier'] ?>" <?php echo set_value('id_supplier') == $row['id_supplier'] ? 'selected' : '' ?>><?php echo $row['nama_supplier'] ?></option>
             <?php endforeach ?>
           </select>
           <?php echo form_error('id_supplier', '<small style="color:red">','</small>') ?>
         </div>
         <div class="form-group <?php if(form_error('satuan')) echo 'has-error'?>">
          <label for="satuan">Satuan</label>
          <input type="text" id="satuan" name="satuan" class="form-control satuan " placeholder="Satuan" value="<?php echo set_value('satuan') ?>">
          <?php echo form_error('satuan', '<small style="color:red">','</small>') ?>
        </div>
        <div>
          <div class="form-group <?php if(form_error('harga_pokok')) echo 'has-error'?>">
            <label for="harga_pokok">Harga Pokok</label>
            <input type="number" id="harga_pokok" name="harga_pokok" class="form-control harga_pokok " placeholder="Harga Pokok" value="<?php echo set_value('harga_pokok') ?>">
            <?php echo form_error('harga_pokok', '<small style="color:red">','</small>') ?>
          </div>
          <div class="row">
            <div class="col-md-6">
             <div class="form-group <?php if(form_error('harga_jual')) echo 'has-error'?>">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" id="harga_jual" name="harga_jual" class="form-control harga_jual " placeholder="Harga Jual" value="<?php echo set_value('harga_jual') ?>">
              <?php echo form_error('harga_jual', '<small style="color:red">','</small>') ?>
             </div>
            </div>
            <div class="col-md-6">
             <div class="form-group <?php if(form_error('profit')) echo 'has-error'?>">
              <label for="profit">Profit</label>
              <input readonly="" type="number" id="profit" name="profit" class="form-control profit " placeholder="Profit" value="<?php echo set_value('profit') ?>">
              <?php echo form_error('profit', '<small style="color:red">','</small>') ?>
            </div>
           
        </div>
      </div>  
      </div>
      <div class="form-group <?php if(form_error('stok')) echo 'has-error'?>">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" class="form-control stok " placeholder="Stok" value="<?php echo set_value('stok') ?>">
        <?php echo form_error('stok', '<small style="color:red">','</small>') ?>
      </div>
      <div class="form-group <?php if(form_error('diskon')) echo 'has-error'?>">
        <label for="diskon">Diskon</label>
        <input type="number" id="diskon" name="diskon" class="form-control diskon " placeholder="Diskon" value="0">
        <?php echo form_error('diskon', '<small style="color:red">','</small>') ?>
      </div>
      <div class="form-group <?php if(form_error('gambar')) echo 'has-error'?>">
        <label for="gambar">Gambar</label>
        <input type="file" id="gambar" name="gambar" class="form-control gambar " placeholder="Gambar" value="<?php echo set_value('gambar') ?>">
        <?php echo form_error('gambar', '<small style="color:red">','</small>') ?>
      </div>
      <div class="form-group <?php if(form_error('barcode')) echo 'has-error'?>">
        <label for="barcode">Barcode</label>
        <input type="number" id="barcode" name="barcode" class="form-control barcode " placeholder="Barcode" value="<?php echo set_value('barcode') ?>">
        <?php echo form_error('barcode', '<small style="color:red">','</small>') ?>
      </div>
      <div class="form-group">
       <button type="submit" class="btn btn-danger btn-block">Submit</button>
     </div>
   </div>
 </div>
</div>
</div>
</div>
</div>
</form>