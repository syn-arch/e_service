<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// default
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// auth
$route['login'] = 'auth';
$route['logout'] = 'auth/logout';

// akses petugas
$route['petugas/akses'] = 'akses';
$route['petugas/tambah_akses'] = 'akses/tambah';
$route['petugas/hapus_akses/(:any)'] = 'akses/hapus/$1';
$route['petugas/ubah_akses/(:any)'] = 'akses/ubah/$1';
$route['petugas/ubah_akses_role/(:any)/(:any)'] = 'akses/ubah_akses/$1/$2';

// outlet
$route['master/outlet'] = 'outlet';
$route['master/tambah_outlet'] = 'outlet/tambah';
$route['master/hapus_outlet/(:any)'] = 'outlet/hapus/$1';
$route['master/ubah_outlet/(:any)'] = 'outlet/ubah/$1';
$route['master/get_outlet_json'] = 'outlet/get_outlet_json';
$route['master/export_outlet'] = 'outlet/export';
$route['master/template_outlet'] = 'outlet/template';
$route['master/import_outlet'] = 'outlet/import';

// kategori
$route['master/kategori'] = 'kategori';
$route['master/tambah_kategori'] = 'kategori/tambah';
$route['master/hapus_kategori/(:any)'] = 'kategori/hapus/$1';
$route['master/ubah_kategori/(:any)'] = 'kategori/ubah/$1';
$route['master/get_kategori_json'] = 'kategori/get_kategori_json';
$route['master/export_kategori'] = 'kategori/export';
$route['master/template_kategori'] = 'kategori/template';
$route['master/import_kategori'] = 'kategori/import';

// supplier
$route['master/supplier'] = 'supplier';
$route['master/tambah_supplier'] = 'supplier/tambah';
$route['master/hapus_supplier/(:any)'] = 'supplier/hapus/$1';
$route['master/ubah_supplier/(:any)'] = 'supplier/ubah/$1';
$route['master/get_supplier_json'] = 'supplier/get_supplier_json';
$route['master/export_supplier'] = 'supplier/export';
$route['master/template_supplier'] = 'supplier/template';
$route['master/import_supplier'] = 'supplier/import';

// karyawan
$route['master/karyawan'] = 'karyawan';
$route['master/tambah_karyawan'] = 'karyawan/tambah';
$route['master/hapus_karyawan/(:any)'] = 'karyawan/hapus/$1';
$route['master/ubah_karyawan/(:any)'] = 'karyawan/ubah/$1';
$route['master/get_karyawan_json'] = 'karyawan/get_karyawan_json';
$route['master/export_karyawan'] = 'karyawan/export';
$route['master/template_karyawan'] = 'karyawan/template';
$route['master/import_karyawan'] = 'karyawan/import';

// pelanggan
$route['master/pelanggan'] = 'pelanggan';
$route['master/tambah_pelanggan'] = 'pelanggan/tambah';
$route['master/hapus_pelanggan/(:any)'] = 'pelanggan/hapus/$1';
$route['master/ubah_pelanggan/(:any)'] = 'pelanggan/ubah/$1';
$route['master/get_pelanggan_json'] = 'pelanggan/get_pelanggan_json';
$route['master/get_pelanggan/(:any)'] = 'pelanggan/get_pelanggan/$1';
$route['master/export_pelanggan'] = 'pelanggan/export';
$route['master/template_pelanggan'] = 'pelanggan/template';
$route['master/import_pelanggan'] = 'pelanggan/import';

// barang
$route['master/barang'] = 'barang';
$route['master/tambah_barang'] = 'barang/tambah';
$route['master/hapus_barang/(:any)'] = 'barang/hapus/$1';
$route['master/ubah_barang/(:any)'] = 'barang/ubah/$1';
$route['master/get_barang_json'] = 'barang/get_barang_json';
$route['master/get_barang/(:any)'] = 'barang/get_barang/$1';
$route['master/get_barang_by_supplier/(:any)'] = 'barang/get_barang_by_supplier/$1';
$route['master/get_barang_by_kategori/(:any)'] = 'barang/get_barang_by_kategori/$1';
$route['master/get_barang_by_name/(:any)'] = 'barang/get_barang_by_name/$1';
$route['master/get_barcode/(:any)'] = 'barang/get_barcode/$1';
$route['master/export_barang'] = 'barang/export';
$route['master/template_barang'] = 'barang/template';
$route['master/import_barang'] = 'barang/import';
$route['master/hapus_semua_barang'] = 'barang/hapus_semua';

// cetak register
$route['penjualan/cetak_register'] = 'laporan/cetak_register';

// riwayat
$route['penjualan/riwayat_penjualan'] = 'laporan/riwayat_penjualan';
$route['pembelian/riwayat_pembelian'] = 'laporan/riwayat_pembelian';
$route['pengembalian/riwayat_pengembalian'] = 'laporan/riwayat_pengembalian';