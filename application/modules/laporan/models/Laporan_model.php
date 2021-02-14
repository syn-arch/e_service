<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class laporan_model extends CI_Model {

	public function get_laporan_penjualan_json($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->datatables->select('faktur_penjualan, nama_pelanggan, nama_petugas,nama_karyawan, total_bayar, penjualan.tgl, status, tgl_jatuh_tempo, SUM(nominal) AS cash, (total_bayar - SUM(nominal)) AS sisa_bayar');
		$this->datatables->from('penjualan');
		$this->datatables->join('pembayaran', 'faktur_penjualan','left');
		$this->datatables->join('pelanggan', 'id_pelanggan', 'left');
		$this->datatables->join('karyawan', 'id_karyawan', 'left');
		$this->datatables->join('petugas', 'id_petugas', 'left');
		if ($dari != '') {
			$this->datatables->where('DATE(penjualan.tgl) >=', $dari);
			$this->datatables->where('DATE(penjualan.tgl) <=', $sampai);
			if ($id_outlet != '') {
				$this->datatables->where('penjualan.id_outlet', $id_outlet);
			}
		}else{
			$this->datatables->where('DATE(penjualan.tgl) >=', date('Y-m-d'));
			$this->datatables->where('DATE(penjualan.tgl) <=', date('Y-m-d'));
		}

		$this->db->order_by('penjualan.tgl', 'desc');
		$this->datatables->group_by('faktur_penjualan');
		return $this->datatables->generate();
	}

	public function get_laporan_pembelian_json()
	{
		$this->datatables->select('faktur_pembelian, nama_supplier, nama_petugas, total_bayar, SUM(nominal) AS cash, pembelian.tgl, status, tgl_jatuh_tempo, (total_bayar - SUM(nominal)) AS sisa_bayar');
		$this->datatables->from('pembelian');
		$this->datatables->join('pembayaran_pembelian', 'faktur_pembelian','left');
		$this->datatables->join('supplier', 'id_supplier', 'left');
		$this->datatables->join('petugas', 'id_petugas', 'left');
		$this->datatables->group_by('faktur_pembelian');
		$this->db->order_by('faktur_pembelian', 'desc');
		return $this->datatables->generate();
	}

	public function get_riwayat_pengembalian_json()
	{
		$this->datatables->select('faktur_pengembalian, nama_pelanggan, nama_petugas, total_bayar, tgl, status, nama_outlet');
		$this->datatables->from('pengembalian');
		$this->datatables->join('outlet', 'id_outlet');
		$this->datatables->join('pelanggan', 'id_pelanggan', 'left');
		$this->datatables->join('petugas', 'id_petugas', 'left');
		if ($id_outlet = $this->session->userdata('id_outlet')) {
			$this->datatables->where('pengembalian.id_outlet', $id_outlet);
		}
		$this->db->order_by('pengembalian.tgl', 'desc');
		return $this->datatables->generate();
	}

	public function get_penjualan($id = '')
	{
		if ($id == '') {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			$this->db->join('karyawan', 'id_karyawan', 'left');
			return $this->db->get('penjualan')->result_array();
		}else {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('karyawan', 'id_karyawan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			$this->db->where('faktur_penjualan', $id);
			return $this->db->get('penjualan')->row_array();
		}
	}

	public function delete_penjualan($id)
	{
		$barang = $this->db->get_where('detail_penjualan', ['faktur_penjualan' => $id])->result_array();

		$pjl = $this->db->get_where('penjualan', ['faktur_penjualan' => $id])->row_array();

		if ($pjl['id_service'] != '') {

			$this->db->set('status', 'BELUM DITERIMA');
			$this->db->set('total_bayar', '');
			$this->db->where('id_service', $pjl['id_service']);
			$this->db->update('service');
		}

		foreach ($barang as $row) {
			$id_outlet = $this->session->userdata('id_outlet');
			if (!$id_outlet) {
				$id_outlet = $this->db->get('outlet')->row_array()['id_outlet'];
			}
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row_array()['stok'];
			$stok_barang += $row['jumlah'];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $row['id_barang']);
			$this->db->update('barang');
		}

		$pembayaran = $this->db->get_where('pembayaran', ['faktur_penjualan' => $id])->result_array();

		foreach ($pembayaran as $row) {
			if ($row['lampiran'] != '') {
				$gambar_lama = $this->db->get_where('pembayaran', ['id_pembayaran' => $row['id_pembayaran']])->row_array()['lampiran'];
				$path = 'assets/img/penjualan/' . $gambar_lama;
				unlink(FCPATH . $path);
			}
		}

		$this->db->delete('penjualan', ['faktur_penjualan' => $id]);
		$this->db->delete('detail_penjualan', ['faktur_penjualan' => $id]);
		$this->db->delete('pembayaran', ['faktur_penjualan' => $id]);
	}

	public function total_kerugian($value='')
	{
		$this->db->select_sum('total_bayar', 'total_kerugian');
		$this->db->where('status', 'ditolak');
		return $this->db->get('pengembalian')->row()->total_kerugian;
	}

	public function delete_pengembalian($id)
	{
		$barang = $this->db->get_where('detail_pengembalian', ['faktur_pengembalian' => $id])->result_array();

		$this->db->delete('pengembalian', ['faktur_pengembalian' => $id]);
		$this->db->delete('detail_pengembalian', ['faktur_pengembalian' => $id]);
	}

	public function delete_register($id)
	{
		$this->db->delete('register', ['id_register' => $id]);
	}

	public function get_pembelian($id = '')
	{
		if ($id == '') {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			return $this->db->get('pembelian')->result_array();
		}else {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			$this->db->where('faktur_pembelian', $id);
			return $this->db->get('pembelian')->row_array();
		}
	}

	public function get_pengembalian($id = '')
	{
		if ($id == '') {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			return $this->db->get('pengembalian')->result_array();
		}else {
			$this->db->join('pelanggan', 'id_pelanggan', 'left');
			$this->db->join('petugas', 'id_petugas', 'left');
			$this->db->where('faktur_pengembalian', $id);
			return $this->db->get('pengembalian')->row_array();
		}
	}

	public function delete_pembelian($id)
	{
		$barang = $this->db->get_where('detail_pembelian', ['faktur_pembelian' => $id])->result_array();

		$multi_outlet = $this->db->get('pengaturan')->row()->multi_outlet;

		foreach ($barang as $row) {
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row_array()['stok'];
			$stok_barang -= $row['jumlah'];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $row['id_barang']);
			$this->db->update('barang');
		}

		$this->db->delete('pembelian', ['faktur_pembelian' => $id]);
		$this->db->delete('detail_pembelian', ['faktur_pembelian' => $id]);
		$this->db->delete('pembayaran_pembelian', ['faktur_pembelian' => $id]);
	}

	public function get_register($id = '')
	{
		if ($id == '') {	
			$this->db->join('petugas', 'id_petugas');
			$this->db->join('outlet', 'register.id_outlet=outlet.id_outlet', 'left');
			if ($this->session->userdata('level') == "Kasir") {
				$this->db->where('id_petugas', $this->session->userdata('id_petugas'));
			}
			$this->db->order_by('mulai', 'desc');
			return $this->db->get('register')->result_array();
		}else{
			$this->db->join('petugas', 'id_petugas');
			$this->db->join('outlet', 'register.id_outlet=outlet.id_outlet', 'left');
			$this->db->where('id_register', $id);
			return $this->db->get_where('register')->row_array();
		}
	}

	public function get_penjualan_per_barang($dari = '', $sampai = '')
	{
		if ($dari == '') {
			$query = "SELECT
			barang.diskon,
			harga_jual,
			harga_pokok,
			profit,
			nama_barang,
			barcode,
			id_barang,
			SUM(jumlah) AS 'barang_terjual',
			profit * SUM(jumlah) AS 'laba',
			(harga_jual - ((barang.diskon/100) * harga_jual)) * SUM(jumlah) AS 'total',
			`penjualan`.`tgl`
			FROM penjualan
			JOIN detail_penjualan USING(faktur_penjualan)
			JOIN barang USING(id_barang)
			GROUP BY `barang`.`id_barang`
			";	
		}else{
			$query = "SELECT
			barang.diskon,
			harga_jual,
			harga_pokok,
			profit,
			nama_barang,
			barang.barcode,
			id_barang,
			SUM(jumlah) AS 'barang_terjual',
			profit * SUM(jumlah) AS 'laba',
			(harga_jual - ((barang.diskon/100) * harga_jual)) * SUM(jumlah) AS 'total',
			`penjualan`.`tgl`
			FROM penjualan
			JOIN detail_penjualan USING(faktur_penjualan)
			JOIN barang USING(id_barang)
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai'
			GROUP BY id_barang
			";
		}
		
		$result =  $this->db->query($query)->result_array();
		return $result;
	}

	public function get_total_pendapatan($dari = '', $sampai = '')
	{
		if ($dari != '') {
			$query = "SELECT 
			SUM(total_harga) AS total_bayar
			FROM penjualan 
			JOIN detail_penjualan USING(faktur_penjualan)
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai'";	
		}else{
			$query = "SELECT 
			SUM(total_harga) AS total_bayar
			FROM penjualan 
			JOIN detail_penjualan USING(faktur_penjualan)";
		}

		return  $this->db->query($query)->row()->total_bayar;
	}

	public function get_total_laba($dari = '', $sampai = '')
	{

		if ($dari != '') {

			$query = " SELECT SUM(laba_bersih) AS total_laba_bersih
			FROM(
			SELECT 
			profit * jumlah AS 'laba_bersih'
			FROM detail_penjualan 
			JOIN penjualan USING(faktur_penjualan) 
			JOIN barang USING(id_barang)
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai'
			) t
			";

		}else{

			$query = " SELECT SUM(laba_bersih) AS total_laba_bersih
			FROM(
			SELECT 
			profit * jumlah AS 'laba_bersih'
			FROM detail_penjualan 
			JOIN penjualan USING(faktur_penjualan) 
			JOIN barang USING(id_barang)
			) t
			";

		}
		
		return $this->db->query($query)->row_array()['total_laba_bersih'];
	}


	public function get_paling_banyak_dijual($dari = '', $sampai = '', $id_outlet = '')
	{			

		$this->db->select('barang.id_barang, nama_barang, SUM(detail_penjualan.jumlah) AS kuantitas');
		$this->db->join('detail_penjualan', 'faktur_penjualan');
		$this->db->join('barang', 'barang.id_barang = detail_penjualan.id_barang');
		$this->db->order_by('kuantitas', 'DESC');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->group_by('id_barang');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_paling_sering_dijual($dari = '', $sampai = '', $id_outlet = '')
	{			
		$this->db->select('barang.id_barang, nama_barang, COUNT(detail_penjualan.id_barang) AS kali');
		$this->db->join('detail_penjualan', 'faktur_penjualan');
		$this->db->join('barang', 'barang.id_barang = detail_penjualan.id_barang');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->order_by('kali', 'DESC');
		$this->db->group_by('id_barang');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_kasir($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select_sum('total_bayar', 'pendapatan');
		$this->db->select('id_petugas, nama_petugas, COUNT(faktur_penjualan) AS transaksi');
		$this->db->join('petugas', 'id_petugas');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		$this->db->group_by('id_petugas');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_karyawan($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select_sum('total_bayar', 'pendapatan');
		$this->db->select('id_karyawan, nama_karyawan, COUNT(faktur_penjualan) AS transaksi');
		$this->db->join('karyawan', 'id_karyawan');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		$this->db->group_by('id_karyawan');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_kategori($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select('barang.id_kategori, nama_kategori, COUNT(faktur_penjualan) AS penjualan');
		$this->db->select_sum('total_harga', 'pendapatan');
		$this->db->join('detail_penjualan', 'faktur_penjualan');
		$this->db->join('barang', 'id_barang');
		$this->db->join('kategori', 'barang.id_kategori=kategori.id_kategori');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->group_by('id_kategori');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_pelanggan($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select('id_pelanggan, nama_pelanggan, COUNT(faktur_penjualan) AS penjualan');
		$this->db->select_sum('total_bayar', 'pendapatan');
		$this->db->join('pelanggan', 'id_pelanggan');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->group_by('id_pelanggan');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_jenis_pelanggan($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select('jenis, COUNT(faktur_penjualan) AS penjualan');
		$this->db->select_sum('total_bayar', 'pendapatan');
		$this->db->join('pelanggan', 'id_pelanggan');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->group_by('jenis');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_per_supplier($dari = '', $sampai = '', $id_outlet = '')
	{
		$this->db->select('barang.id_supplier, nama_supplier, COUNT(faktur_penjualan) AS penjualan');
		$this->db->select_sum('total_harga', 'pendapatan');
		$this->db->join('detail_penjualan', 'faktur_penjualan');
		$this->db->join('barang', 'id_barang');
		$this->db->join('supplier', 'barang.id_supplier=supplier.id_supplier');
		if ($dari != '') {
			$this->db->where('DATE(penjualan.tgl) >=', $dari);
			$this->db->where('DATE(penjualan.tgl) <=', $sampai);
		}
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);	
		}
		$this->db->group_by('id_supplier');
		return $this->db->get('penjualan')->result_array();
	}

	public function get_omset($dari = '', $sampai = '', $id_outlet = '')
	{
		if ($id_outlet != '') {
			$id_outlet = "AND id_outlet = '$id_outlet'";
		}else{
			$id_outlet = "";
		}

		if ($dari != '') {
			$query = "
			SELECT 
			DATE(tgl) AS tgl_penjualan,
			SUM(total_bayar) AS net_sales,
			SUM(diskon) AS ttl_charge,
			(SUM(diskon) / 100 ) * SUM(total_bayar) AS harga_diskon,
			SUM(total_bayar) - (SUM(diskon) / 100 ) * SUM(total_bayar) AS ttl_sales,
			COUNT(faktur_penjualan) AS ttl_customer
			FROM penjualan a
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai' " . $id_outlet . "
			GROUP BY DATE(tgl)
			";
		}else{
			$query = "
			SELECT 
			DATE(tgl) AS tgl_penjualan,
			SUM(total_bayar) AS net_sales,
			SUM(diskon) AS ttl_charge,
			(SUM(diskon) / 100 ) * SUM(total_bayar) AS harga_diskon,
			SUM(total_bayar) - (SUM(diskon) / 100 ) * SUM(total_bayar) AS ttl_sales,
			COUNT(faktur_penjualan) AS ttl_customer
			FROM penjualan a
			GROUP BY DATE(tgl)
			";	
		}
		

		return $this->db->query($query)->result_array();
	}

	public function get_qty_beli($dari = '', $sampai = '', $id_outlet = '')
	{
		if ($id_outlet != '') {
			$id_outlet = "AND id_outlet = '$id_outlet'";
		}else{
			$id_outlet = "";
		}

		if ($dari != '') {
			$query ="
			SELECT 
			SUM(jumlah) AS ttl_qty,
			COUNT(jumlah) AS ttl_beli
			FROM penjualan
			JOIN detail_penjualan USING(faktur_penjualan)
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai' " . $id_outlet . "
			GROUP BY DATE(tgl)
			";
		}else{
			$query ="
			SELECT 
			SUM(jumlah) AS ttl_qty,
			COUNT(jumlah) AS ttl_beli
			FROM penjualan
			JOIN detail_penjualan USING(faktur_penjualan)
			GROUP BY DATE(tgl)
			";
		}

		return $this->db->query($query)->result_array();
	}

	public function get_all_pembelian($dari = '', $sampai = '')
	{

		if ($dari != '') {
			$query = "SELECT
			`barang`.`nama_barang`,
			`barang`.`barcode`,
			`barang`.`harga_pokok`,
			`barang`.`id_barang`,
			SUM(`detail_pembelian`.`jumlah`) AS 'barang_terbeli',
			`barang`.`harga_pokok` * SUM(`detail_pembelian`.`jumlah`) AS 'total'
			FROM pembelian
			JOIN detail_pembelian USING(faktur_pembelian)
			LEFT JOIN barang USING(id_barang)
			WHERE DATE(tgl) BETWEEN '$dari' AND '$sampai'
			GROUP BY `barang`.`id_barang`
			";	
		}else{
			$query = "SELECT
			`barang`.`nama_barang`,
			`barang`.`barcode`,
			`barang`.`harga_pokok`,
			`barang`.`id_barang`,
			SUM(`detail_pembelian`.`jumlah`) AS 'barang_terbeli',
			`barang`.`harga_pokok` * SUM(`detail_pembelian`.`jumlah`) AS 'total'
			FROM pembelian
			JOIN detail_pembelian USING(faktur_pembelian)
			LEFT JOIN barang USING(id_barang)
			GROUP BY `barang`.`id_barang`
			";
		}

		

		return $this->db->query($query)->result_array();
	}

	public function get_total_pembelian($dari = '', $sampai = '')
	{
		$this->db->select_sum('total_bayar', 'total');
		if ($dari != '') {
			$this->db->where('DATE(pembelian.tgl) >=', $dari);
			$this->db->where('DATE(pembelian.tgl) <=', $sampai);
		}
		return $this->db->get('pembelian')->row_array()['total'];
	}

	public function get_tanggal($dari = '', $sampai = '')
	{
		if ($dari != '') {
			$query = "SELECT '$dari' + INTERVAL a + b DAY tgl
			FROM
			(SELECT 0 a UNION SELECT 1 a UNION SELECT 2 UNION SELECT 3
			UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
			UNION SELECT 8 UNION SELECT 9 ) d,
			(SELECT 0 b UNION SELECT 10 UNION SELECT 20 
			UNION SELECT 30 UNION SELECT 40) m
			WHERE '$dari' + INTERVAL a + b DAY  <=  '$sampai'
			ORDER BY a + b";	
		}

		return $this->db->query($query)->result_array();
	}

	public function get_total_service($dari = '', $sampai = '')
	{
		$this->db->select_sum('total_bayar', 'total');
		if ($dari != '') {
			$this->db->where('DATE(service.tgl_ambil) >=', $dari);
			$this->db->where('DATE(service.tgl_ambil) <=', $sampai);
		}
		return $this->db->get('service')->row_array()['total'];
	}

	public function get_all_pengembalian()
	{
		$query = "SELECT
		`barang`.`nama_barang`,
		`barang`.`barcode`,
		`barang`.`harga_pokok`,
		`barang`.`id_barang`,
		SUM(`detail_pengembalian`.`jumlah`) AS 'barang_kembali',
		`barang`.`harga_pokok` * SUM(`detail_pengembalian`.`jumlah`) AS 'total'
		FROM pengembalian
		JOIN detail_pengembalian USING(faktur_pengembalian)
		LEFT JOIN barang USING(id_barang)
		GROUP BY `barang`.`id_barang`
		";

		return $this->db->query($query)->result_array();
	}

	public function get_total_pengembalian()
	{
		$this->db->select_sum('total_bayar', 'total');
		return $this->db->get('pengembalian')->row_array()['total'];
	}

	public function get_all_hutang()
	{
		$query = " 
		SELECT *,nama_supplier, SUM(total_bayar) AS jumlah_hutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_hutang
		FROM pembelian
		JOIN supplier USING(id_supplier)
		JOIN pembayaran_pembelian USING(faktur_pembelian)
		WHERE status = 'Belum Lunas'
		GROUP BY faktur_pembelian
		";

		return $this->db->query($query)->result_array();

	}

	public function get_total_hutang()
	{
		$query = " 
		SELECT SUM(total_bayar) AS jumlah_hutang
		FROM pembelian
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->jumlah_hutang;
	}

	public function get_sisa_hutang()
	{
		$query = " 
		SELECT *,nama_supplier, SUM(total_bayar) AS jumlah_hutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_hutang
		FROM pembelian
		JOIN supplier USING(id_supplier)
		JOIN pembayaran_pembelian USING(faktur_pembelian)
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->sisa_hutang;
	}

	public function get_telah_dibayar()
	{
		$query = " 
		SELECT *,nama_supplier, SUM(total_bayar) AS jumlah_hutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_hutang
		FROM pembelian
		JOIN supplier USING(id_supplier)
		JOIN pembayaran_pembelian USING(faktur_pembelian)
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->telah_dibayar;
	}

	public function get_all_piutang()
	{
		$query = " 
		SELECT *,nama_pelanggan, SUM(total_bayar) AS jumlah_piutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_piutang
		FROM penjualan
		JOIN pelanggan USING(id_pelanggan)
		JOIN pembayaran USING(faktur_penjualan)
		WHERE status = 'Belum Lunas'
		GROUP BY faktur_penjualan
		";

		return $this->db->query($query)->result_array();

	}

	public function get_total_piutang()
	{
		$query = " 
		SELECT SUM(total_bayar) AS jumlah_piutang
		FROM penjualan
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->jumlah_piutang;
	}

	public function get_sisa_piutang()
	{
		$query = " 
		SELECT *, SUM(total_bayar) AS jumlah_piutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_piutang
		FROM penjualan
		JOIN pelanggan USING(id_pelanggan)
		JOIN pembayaran USING(faktur_penjualan)
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->sisa_piutang;
	}

	public function get_telah_dibayar_piutang()
	{
		$query = " 
		SELECT *, SUM(total_bayar) AS jumlah_piutang,
		SUM(nominal) AS telah_dibayar,
		(SUM(total_bayar) - SUM(nominal)) AS sisa_piutang
		FROM penjualan
		JOIN pelanggan USING(id_pelanggan)
		JOIN pembayaran USING(faktur_penjualan)
		WHERE status = 'Belum Lunas'
		";

		return $this->db->query($query)->row()->telah_dibayar;
	}

}

/* End of file penjualan_model.php */
/* Location: ./application/modules/penjualan/models/penjualan_model.php */ ?>