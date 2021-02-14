<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	private $id_outlet;

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
		header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
		ini_set('max_execution_time', 70000);
		if ($idtl = $this->session->userdata('id_outlet')) {
			$this->id_outlet = $idtl;
		}else{
			$this->id_outlet = $this->db->get('outlet')->row()->id_outlet;
		}
	}

	public function get_biaya()
	{
		header('Content-Type: application/json');
		
		echo json_encode($this->db->get_where('biaya', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_detail_biaya()
	{
		header('Content-Type: application/json');

		$this->db->join('biaya', 'id_biaya');
		echo json_encode($this->db->get_where('detail_biaya', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_penjualan()
	{
		header('Content-Type: application/json');
		
		echo json_encode($this->db->get_where('penjualan', ['id_outlet' => $this->id_outlet])->result_array());
	}


	public function get_pelanggan()
	{
		header('Content-Type: application/json');
		
		echo json_encode($this->db->get('pelanggan')->result_array());
	}

	public function get_register()
	{
		header('Content-Type: application/json');
		
		echo json_encode($this->db->get_where('register', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_service()
	{
		header('Content-Type: application/json');

		$this->db->join('karyawan', 'id_karyawan');
		echo json_encode($this->db->get_where('service', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_detail_penjualan()
	{
		header('Content-Type: application/json');

		$this->db->join('penjualan', 'faktur_penjualan');
		echo json_encode($this->db->get_where('detail_penjualan', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_pembayaran()
	{
		header('Content-Type: application/json');

		$this->db->join('penjualan', 'faktur_penjualan');
		echo json_encode($this->db->get_where('pembayaran', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_petugas()
	{
		header('Content-Type: application/json');

		echo json_encode($this->db->get_where('petugas', ['id_outlet' => $this->id_outlet])->result_array());
	}

	public function get_stok_outlet()
	{
		header('Content-Type: application/json');

		echo json_encode($this->db->get_where('stok_outlet', ['id_outlet' => $this->id_outlet])->result_array());
	}

		// Server
	public function get_data_transaksi()
	{   
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
		header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
		header('Content-Type: application/json');

		// Master
		$biaya = $this->db->get_where('biaya', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$this->db->join('biaya', 'id_biaya');
		$detail_biaya = $this->db->get_where('detail_biaya', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$penjualan = $this->db->get_where('penjualan', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$this->db->join('penjualan', 'faktur_penjualan');
		$detail_penjualan = $this->db->get_where('detail_penjualan', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$register = $this->db->get_where('register', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$this->db->join('karyawan', 'id_karyawan', 'left');
		$service = $this->db->get_where('service', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		$pelanggan = $this->db->get('pelanggan')->result_array();


		$this->db->join('penjualan', 'faktur_penjualan');
		$pembayaran = $this->db->get_where('pembayaran', ['id_outlet' => $this->input->post('id_outlet')])->result_array();


		echo '{ 
			"pelanggan" : '. json_encode($pelanggan) .', 
			"service" : '. json_encode($service) .', 
			"register" : '. json_encode($register) .', 
			"penjualan" : '. json_encode($penjualan) .', 
			"detail_penjualan" : '. json_encode($detail_penjualan) .', 
			"biaya" : '. json_encode($biaya) .', 
			"detail_biaya" : '. json_encode($detail_biaya) .', 
			"pembayaran" : '. json_encode($pembayaran) .'
		}';
	}

	// Client + Client
	public function sync_data_transaksi()
	{
		$post = $this->input->post();

		$pelanggan = json_decode($post['pelanggan'], TRUE);
		$register = json_decode($post['register'], TRUE);
		$service = json_decode($post['service'], TRUE);
		$penjualan = json_decode($post['penjualan'], TRUE);
		$detail_penjualan = json_decode($post['detail_penjualan'], TRUE);
		$biaya = json_decode($post['biaya'], TRUE);
		$detail_biaya = json_decode($post['detail_biaya'], TRUE);
		$pembayaran = json_decode($post['pembayaran'], TRUE);
		$id_outlet = $post['id_outlet'];

		$this->db->trans_start();

		if ($pelanggan) {
			foreach ($pelanggan as $row) {
				$data_pelanggan = [
					'id_pelanggan' => $row['id_pelanggan'],
					'barcode' => $row['barcode'],
					'nama_pelanggan' => $row['nama_pelanggan'],
					'alamat' => $row['alamat'],
					'telepon' => $row['telepon'],
					'jk' => $row['jk'],
					'jenis' => $row['jenis']
				];
				$cek_pelanggan = $this->db->get_where('pelanggan', ['id_pelanggan' => $row['id_pelanggan']])->row_array();
				if ($cek_pelanggan) {
					$this->db->where('id_pelanggan', $row['id_pelanggan']);
					$this->db->update('pelanggan', $data_pelanggan);
				}else{
					$this->db->insert('pelanggan', $data_pelanggan);
				}
			}
		}

		$hapus_pelanggan = $this->db->get('pelanggan')->result_array();
		foreach ($hapus_pelanggan as $row) {
			if (!in_array($row['id_pelanggan'] ,array_column($pelanggan, 'id_pelanggan'))) {
				$this->db->delete('pelanggan', ['id_pelanggan' => $row['id_pelanggan']]);
			}
		}

		if ($detail_penjualan) {
			foreach ($detail_penjualan as $row) {
				$data_detail_penjualan = [
					'faktur_penjualan' => $row['faktur_penjualan'],
					'id_barang' => $row['id_barang'],
					'jumlah' => $row['jumlah'],
					'type_golongan' => $row['type_golongan'],
					'total_harga' => $row['total_harga']
				];
				$cek_detail_penjualan = $this->db->get_where('detail_penjualan',$data_detail_penjualan)->row_array();
				if ($cek_detail_penjualan) {
					$this->db->where('faktur_penjualan', $row['faktur_penjualan']);
					$this->db->where('id_barang', $row['id_barang']);
					$this->db->where('jumlah', $row['jumlah']);
					$this->db->where('total_harga', $row['total_harga']);
					$this->db->update('detail_penjualan', $data_detail_penjualan);
				}else{
					$this->db->insert('detail_penjualan', $data_detail_penjualan);

					// kurangi stok barang
					$stok_barang = $this->db->get_where('stok_outlet', ['id_outlet' => $id_outlet ,'id_barang' => $row['id_barang']])->row_array()['stok'];
					$stok_barang -= $row['jumlah'];

					$this->db->set('stok', $stok_barang);
					$this->db->where('id_barang', $row['id_barang']);
					$this->db->where('id_outlet', $id_outlet);
					$this->db->update('stok_outlet');

				}
			}
		}


		$this->db->join('penjualan', 'faktur_penjualan');
		$hapus_detail_penjualan = $this->db->get_where('detail_penjualan', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_detail_penjualan as $row) {
			if (!in_array($row['faktur_penjualan'], array_column($detail_penjualan, 'faktur_penjualan'))) {

				$stok_barang = $this->db->get_where('stok_outlet', ['id_outlet' => $id_outlet ,'id_barang' => $row['id_barang']])->row_array()['stok'];
				$stok_barang += $row['jumlah'];

				$this->db->set('stok', $stok_barang);
				$this->db->where('id_barang', $row['id_barang']);
				$this->db->where('id_outlet', $id_outlet);
				$this->db->update('stok_outlet');

				$this->db->delete('detail_penjualan', ['faktur_penjualan' => $row['faktur_penjualan']]);
			}
		}

		if ($detail_biaya) {
			foreach ($detail_biaya as $row) {
				$data_detail_biaya = [
					'id_biaya' => $row['id_biaya'],
					'nama_biaya' => $row['nama_biaya'],
					'harga' => $row['harga'],
					'qty' => $row['qty'],
					'total_harga' => $row['total_harga']
				];
				$cek_detail_biaya = $this->db->get_where('detail_biaya',$data_detail_biaya)->row_array();
				if ($cek_detail_biaya) {
					$this->db->where('id_biaya', $row['id_biaya']);
					$this->db->where('nama_biaya', $row['nama_biaya']);
					$this->db->where('harga', $row['harga']);
					$this->db->where('qty', $row['qty']);
					$this->db->where('total_harga', $row['total_harga']);
					$this->db->update('detail_biaya', $data_detail_biaya);
				}else{
					$this->db->insert('detail_biaya', $data_detail_biaya);

				}
			}
		}


		$this->db->join('biaya', 'id_biaya');
		$hapus_detail_biaya = $this->db->get_where('detail_biaya', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_detail_biaya as $row) {
			if (!in_array($row['id_biaya'], array_column($detail_biaya, 'id_biaya'))) {
				$this->db->delete('detail_biaya', ['id_biaya' => $row['id_biaya']]);
			}
		}

		if ($biaya) {
			foreach ($biaya as $row) {
				$data_biaya = [
					'id_biaya' => $row['id_biaya'],
					'id_petugas' => $row['id_petugas'],
					'id_outlet' => $row['id_outlet'],
					'total_bayar' => $row['total_bayar'],
					'cash' => $row['cash'],
					'tgl' => $row['tgl'],
					'keterangan' => $row['keterangan']
				];
				$cek_biaya = $this->db->get_where('biaya', ['id_biaya' => $row['id_biaya']])->row_array();
				if ($cek_biaya) {
					$this->db->where('id_biaya', $row['id_biaya']);
					$this->db->update('biaya', $data_biaya);
				}else{
					$this->db->insert('biaya', $data_biaya);
				}
			}
		}

		$hapus_biaya = $this->db->get_where('biaya', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_biaya as $row) {
			if (!in_array($row['id_biaya'] ,array_column($biaya, 'id_biaya'))) {
				$this->db->delete('biaya', ['id_biaya' => $row['id_biaya']]);
			}
		}

		if ($pembayaran) {
			foreach ($pembayaran as $row) {
				$data_pembayaran =  [
					'id_pembayaran' => $row['id_pembayaran'],
					'faktur_penjualan' => $row['faktur_penjualan'],
					'tgl' => $row['tgl'],
					'dibayar_dengan' => $row['dibayar_dengan'],
					'nominal' => $row['nominal'],
					'nominal' => $row['nominal'],
					'no_kredit' => $row['no_kredit'],
					'no_debit' => $row['no_debit'],
					'lampiran' => $row['lampiran']
				];
				$cek_pembayaran = $this->db->get_where('pembayaran', ['id_pembayaran' => $row['id_pembayaran']])->row_array();
				if ($cek_pembayaran) {
					$this->db->where('id_pembayaran', $row['id_pembayaran']);
					$this->db->update('pembayaran', $data_pembayaran);
				}else{
					$this->db->insert('pembayaran', $data_pembayaran);
				}
			}
		}


		$this->db->join('penjualan', 'faktur_penjualan');
		$hapus_pembayaran = $this->db->get_where('pembayaran', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_pembayaran as $row) {
			if (!in_array($row['id_pembayaran'] ,array_column($pembayaran, 'id_pembayaran'))) {
				$this->db->delete('pembayaran', ['id_pembayaran' => $row['id_pembayaran']]);
			}
		}

		if ($penjualan) {
			foreach ($penjualan as $row) {
				$data_penjualan = [
					'faktur_penjualan' => $row['faktur_penjualan'],
					'tgl' => $row['tgl'],
					'tgl_jatuh_tempo' => $row['tgl_jatuh_tempo'],
					'id_petugas' => $row['id_petugas'],
					'id_karyawan' => $row['id_karyawan'],
					'id_pelanggan' => $row['id_pelanggan'],
					'id_outlet' => $row['id_outlet'],
					'total_bayar' => $row['total_bayar'],
					'diskon' => $row['diskon'],
					'potongan' => $row['potongan'],
					'status' => $row['status']
				];
				$cek_penjualan = $this->db->get_where('penjualan', ['faktur_penjualan' => $row['faktur_penjualan']])->row_array();
				if ($cek_penjualan) {
					$this->db->where('faktur_penjualan', $row['faktur_penjualan']);
					$this->db->update('penjualan', $data_penjualan);
				}else{
					$this->db->insert('penjualan', $data_penjualan);
				}
			}
		}

		$hapus_penjualan = $this->db->get_where('penjualan', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_penjualan as $row) {
			if (!in_array($row['faktur_penjualan'] ,array_column($penjualan, 'faktur_penjualan'))) {
				$this->db->delete('penjualan', ['faktur_penjualan' => $row['faktur_penjualan']]);
			}
		}

		if ($register) {
			foreach ($register as $row) {
				$data_register = [
					'id_petugas' => $row['id_petugas'],
					'id_outlet' => $row['id_outlet'],
					'uang_awal' => $row['uang_awal'],
					'total_uang' => $row['total_uang'],
					'mulai' => $row['mulai'],
					'berakhir' => $row['berakhir'],
					'status' => $row['status']
				];

				$cek_register = $this->db->get_where('register', ['mulai' => $row['mulai']])->row_array();
				if ($cek_register) {
					$this->db->where('mulai', $row['mulai']);
					$this->db->update('register', $data_register);
				}else{
					$this->db->insert('register', $data_register);
				}
			}
		}

		$hapus_register = $this->db->get_where('register', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_register as $row) {
			if (!in_array($row['mulai'] ,array_column($register, 'mulai'))) {
				$this->db->delete('register', ['mulai' => $row['mulai']]);
			}
		}

		if ($service) {
			foreach ($service as $row) {
				$data_service = [
					'id_service' => $row['id_service'],
					'id_karyawan' => $row['id_karyawan'],
					'id_pelanggan' => $row['id_pelanggan'],
					'tgl_service' => $row['tgl_service'],
					'tgl_ambil' => $row['tgl_ambil'],
					'status' => $row['status'],
					'jenis_barang' => $row['jenis_barang'],
					'kerusakan' => $row['kerusakan'],
					'kelengkapan' => $row['kelengkapan'],
					'garansi' => $row['garansi'],
					'ketentuan_garansi' => $row['ketentuan_garansi'],
					'keterangan' => $row['keterangan'],
					'total_bayar' => $row['total_bayar'],
					'cash' => $row['cash']
				];
				$cek_service = $this->db->get_where('service', ['id_service' => $row['id_service']])->row_array();
				if ($cek_service) {
					$this->db->where('id_service', $row['id_service']);
					$this->db->update('service', $data_service);
				}else{
					$this->db->insert('service', $data_service);
				}
			}
		}

		$this->db->join('karyawan', 'id_karyawan');
		$hapus_service = $this->db->get_where('service', ['id_outlet' => $id_outlet])->result_array();
		foreach ($hapus_service as $row) {
			if (!in_array($row['id_service'] ,array_column($service, 'id_service'))) {

				$this->db->join('detail_penjualan', 'faktur_penjualan', 'left');
				$sv = $this->db->get_where('penjualan', ['id_service' => $row['id_service']])->result_array();

				foreach ($sv as $row) {

					// tambah stok barang
					$stok_barang = $this->db->get_where('stok_outlet', ['id_outlet' => $row['id_outlet'], 'id_barang' => $row['id_barang']])->row_array()['stok'];
					$stok_barang += $row['jumlah'][$i];

					$this->db->set('stok', $stok_barang);
					$this->db->where('id_barang', $row['id_barang']);
					$this->db->where('id_outlet', $row['id_outlet']);
					$this->db->update('stok_outlet');


					$this->db->delete('detail_penjualan', ['faktur_penjualan' => $row['faktur_penjualan']]);
				}


				$this->db->delete('penjualan', ['id_service' => $row['id_service']]);

				$this->db->delete('service', ['id_service' => $row['id_service']]);
			}
		}

		
		$this->db->trans_complete();
	}

	// Server
	public function get_data_master()
	{   
		header('Content-Type: application/json');
		// Master
		$kategori = $this->db->get('kategori')->result_array();
		$barang = $this->db->get('barang')->result_array();
		$supplier = $this->db->get('supplier')->result_array();
		$petugas = $this->db->get('petugas')->result_array();
		$karyawan = $this->db->get('karyawan')->result_array();

		echo '{ 
			"kategori" : '. json_encode($kategori) .', 
			"barang" : '. json_encode($barang) .', 
			"petugas" : '. json_encode($petugas) .', 
			"karyawan" : '. json_encode($karyawan) .', 
			"supplier" : '. json_encode($supplier) . '
		}';
	}

	// Client
	public function download_data_master()
	{
		$kategori = json_decode($this->input->post('kategori'), TRUE);
		$barang = json_decode($this->input->post('barang'), TRUE);
		$supplier = json_decode($this->input->post('supplier'), TRUE);
		$petugas = json_decode($this->input->post('petugas'), TRUE);
		$karyawan = json_decode($this->input->post('karyawan'), TRUE);

		$this->db->trans_start();

		$this->db->query("DELETE FROM kategori");
		$this->db->query("DELETE FROM supplier");
		$this->db->query("DELETE FROM barang");
		$this->db->query("DELETE FROM petugas");
		$this->db->query("DELETE FROM karyawan");

		foreach ($petugas as $row) {
			$this->db->insert('petugas', [
				'id_petugas' => $row['id_petugas'],
				'id_outlet' => $row['id_outlet'],
				'nama_petugas' => $row['nama_petugas'],
				'alamat' => $row['alamat'],
				'jk' => $row['jk'],
				'telepon' => $row['telepon'],
				'email' => $row['email'],
				'password' => $row['password'],
				'gambar' => $row['gambar'],
				'id_role' => $row['id_role']
			]);
		}

		foreach ($karyawan as $row) {
			$this->db->insert('karyawan', [
				'id_karyawan' => $row['id_karyawan'],
				'id_outlet' => $row['id_outlet'],
				'nama_karyawan' => $row['nama_karyawan'],
				'alamat' => $row['alamat'],
				'jk' => $row['jk'],
				'telepon' => $row['telepon'],
				'email' => $row['email'],
				'jabatan' => $row['jabatan'],
				'gambar' => $row['gambar']
			]);
		}

		foreach ($kategori as $row) {
			$data_kategori = [
				'id_kategori' => $row['id_kategori'],
				'nama_kategori' => $row['nama_kategori']
			];
			$this->db->insert('kategori', $data_kategori);
		}

		foreach ($supplier as $row) {
			$data_supplier = [
				'id_supplier' => $row['id_supplier'],
				'nama_supplier' => $row['nama_supplier'],
				'alamat' => $row['alamat'],
				'telepon' => $row['telepon']
			];
			$this->db->insert('supplier', $data_supplier);
		}

		foreach ($barang as $row) {
			$data_barang = [
				'id_barang' => $row['id_barang'],
				'id_kategori' => $row['id_kategori'],
				'id_supplier' => $row['id_supplier'],
				'satuan' => $row['satuan'],
				'barcode' => $row['barcode'],
				'nama_barang' => $row['nama_barang'],
				'nama_pendek' => $row['nama_pendek'],
				'harga_pokok' => $row['harga_pokok'],
				'golongan_1' => $row['golongan_1'],
				'profit_1' => $row['profit_1'],
				'golongan_2' => $row['golongan_2'],
				'profit_2' => $row['profit_2'],
				'golongan_3' => $row['golongan_3'],
				'profit_3' => $row['profit_3'],
				'golongan_4' => $row['golongan_4'],
				'profit_4' => $row['profit_4'],
				'stok' => $row['stok'],
				'diskon' => $row['diskon'],
				'gambar' => $row['gambar']
			];

			$this->db->insert('barang', $data_barang);
		}

		$this->db->trans_complete();
	}

	// Server
	public function get_stok()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
		header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
		header('Content-Type: application/json');
		$stok_outlet = $this->db->get_where('stok_outlet', ['id_outlet' => $this->input->post('id_outlet')])->result_array();

		echo json_encode($stok_outlet);
	}

	// Client
	public function update_stok()
	{
		$id_outlet = $this->input->post('id_outlet');
		$stok_outlet = $this->input->post('stok_outlet');

		$stok = json_decode($stok_outlet, TRUE);

		$this->db->trans_start();
		$this->db->query("DELETE FROM stok_outlet WHERE id_outlet = '$id_outlet' ");
		foreach ($stok as $row) {
			$data_stok_outlet = [
				'id_barang' => $row['id_barang'],
				'id_outlet' => $id_outlet,
				'stok' => $row['stok']
			];
			$this->db->insert('stok_outlet', $data_stok_outlet);
		}
		$this->db->trans_complete();	
	}

}

/* End of file Api.php */
/* Location: ./application/controllers/Api.php */ ?>