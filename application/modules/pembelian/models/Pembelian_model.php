<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pembelian_model extends CI_Model {

	private function updateStatus($faktur_pembelian){

		$total = $this->db->get_where('pembelian', ['faktur_pembelian' => $faktur_pembelian])->row_array()['total_bayar'];

		$this->db->select_sum('nominal', 'total_pembayaran');
		$this->db->where('faktur_pembelian', $faktur_pembelian);
		$total_pembayaran = $this->db->get('pembayaran_pembelian')->row_array()['total_pembayaran'];

		if ($total_pembayaran >= $total) {
			$this->db->set('status', 'Lunas');
			$this->db->where('faktur_pembelian', $faktur_pembelian);
			$this->db->update('pembelian');
		}else{
			$this->db->set('status', 'Belum Lunas');
			$this->db->where('faktur_pembelian', $faktur_pembelian);
			$this->db->update('pembelian');
		}
	}

	public function get_pembayaran($id = '', $single = false)
	{
		if ($single == true) {
			return $this->db->get_where('pembayaran_pembelian', ['id_pembayaran' => $id])->row_array();
		}

		if ($id == '') {
			return $this->db->get('pembayaran_pembelian')->result_array();
		}else{
			return $this->db->get_where('pembayaran_pembelian', ['faktur_pembelian' => $id])->result_array();
		}
	}

	public function tambah_pembayaran($post)
	{
		$pembayaran = [
			'faktur_pembelian' => $post['faktur_pembelian'],
			'dibayar_dengan' => $post['metode_pembayaran'],
			'nominal' => $post['nominal'],
			'no_debit' => $post['no_debit'],
			'no_kredit' => $post['no_kredit']
		];

		if ($_FILES['lampiran']['name']) {
			$pembayaran['lampiran'] = _upload('lampiran', 'pembelian/tambah_pembayaran/' . $post['faktur_pembelian'], 'pembelian');
		}

		$this->db->insert('pembayaran_pembelian', $pembayaran);

		$this->updateStatus($post['faktur_pembelian']);
	}

	public function ubah_pembayaran($id, $post)
	{
		$pembayaran = [
			'faktur_pembelian' => $post['faktur_pembelian'],
			'dibayar_dengan' => $post['metode_pembayaran'],
			'nominal' => $post['nominal'],
			'no_debit' => $post['no_debit'],
			'no_kredit' => $post['no_kredit']
		];

		if ($_FILES['lampiran']['name']) {
			$gambar_lama = $this->db->get_where('pembayaran_pembelian', ['id_pembayaran' => $id])->row_array()['lampiran'];
			$path = 'assets/img/pembelian/' . $gambar_lama;
			unlink(FCPATH . $path);
			$pembayaran['lampiran'] = _upload('lampiran', 'pembelian/ubah_pembayaran/' . $id . $post['faktur_pembelian'], 'pembelian');
		}

		$this->db->where('id_pembayaran', $id);
		$this->db->update('pembayaran_pembelian', $pembayaran);

		$this->updateStatus($post['faktur_pembelian']);
	}

	public function hapus_pembayaran($id, $faktur_pembelian)
	{
		$gambar_lama = $this->db->get_where('pembayaran_pembelian', ['id_pembayaran' => $id])->row_array()['lampiran'];
		$path = 'assets/img/pembelian/' . $gambar_lama;
		unlink(FCPATH . $path);
		$this->db->delete('pembayaran_pembelian', ['id_pembayaran' => $id]);
		
		$this->updateStatus($faktur_pembelian);
	}

	public function proses($post)
	{
		$faktur = faktur_no(true);

		$this->db->trans_start();

		$multi_outlet = $this->db->get('pengaturan')->row()->multi_outlet;

		for ($i=0; $i < count($post['id_barang']); $i++) { 
			$harga = $this->db->get_where('barang', ['id_barang' => $post['id_barang'][$i]])->row_array()['harga_pokok'];
			$total_harga = $harga * $post['jumlah'][$i];

			$this->db->insert('detail_pembelian', [
				'faktur_pembelian' => $faktur,
				'id_barang' => $post['id_barang'][$i],
				'jumlah' => $post['jumlah'][$i],
				'total_harga' => $total_harga
			]);

				// tambah stok ke tabel barang
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $post['id_barang'][$i]])->row_array()['stok'];
			$stok_barang += $post['jumlah'][$i];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $post['id_barang'][$i]);
			$this->db->update('barang');

			
		}

		$this->db->select_sum('total_harga');
		$total_bayar = $this->db->get_where('detail_pembelian', ['faktur_pembelian' => $post['faktur_pembelian']])->row_array()['total_harga'];

		if ($post['cash'] >= $total_bayar) {
			$status = 'Lunas';
		}else{
			$status = 'Belum Lunas';
		}

		$this->db->insert('pembelian', [
			'faktur_pembelian' => $faktur,
			'id_petugas' => $post['id_petugas'],
			'tgl_pembelian' => $post['tgl_pembelian'],
			'referensi' => $post['referensi'],
			'tgl_jatuh_tempo' => $post['tgl_jatuh_tempo'],
			'id_supplier' => $post['id_supplier'],
			'total_bayar' => $total_bayar,
			'status' => $status
		]);

		$pembayaran = [
			'faktur_pembelian' => $faktur,
			'dibayar_dengan' => $post['metode_pembayaran'],
			'nominal' => $post['cash'],
			'no_debit' => $post['no_debit'],
			'no_kredit' => $post['no_kredit']
		];

		if ($_FILES['lampiran']['name']) {
			$pembayaran['lampiran'] = _upload('lampiran', 'pembelian', 'pembelian');
		}

		$this->db->insert('pembayaran_pembelian', $pembayaran);

		$this->db->trans_complete();
	}

	public function proses_update($post)
	{
		$barang = $this->db->get_where('detail_pembelian', ['faktur_pembelian' => $post['faktur_pembelian']])->result_array();

		$multi_outlet = $this->db->get('pengaturan')->row()->multi_outlet;

		$this->db->trans_start();

		foreach ($barang as $row) {
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row_array()['stok'];
			$stok_barang -= $row['jumlah'];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $row['id_barang']);
			$this->db->update('barang');
		}	

		$this->db->delete('detail_pembelian', ['faktur_pembelian' => $post['faktur_pembelian']]);

		$faktur = $post['faktur_pembelian'];


		$multi_outlet = $this->db->get('pengaturan')->row()->multi_outlet;

		for ($i=0; $i < count($post['id_barang']); $i++) { 
			$harga = $this->db->get_where('barang', ['id_barang' => $post['id_barang'][$i]])->row_array()['harga_pokok'];
			$total_harga = $harga * $post['jumlah'][$i];

			$this->db->insert('detail_pembelian', [
				'faktur_pembelian' => $faktur,
				'id_barang' => $post['id_barang'][$i],
				'jumlah' => $post['jumlah'][$i],
				'total_harga' => $total_harga
			]);

				// tambah stok ke tabel barang
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $post['id_barang'][$i]])->row_array()['stok'];
			$stok_barang += $post['jumlah'][$i];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $post['id_barang'][$i]);
			$this->db->update('barang');
			
		}

		$this->db->select_sum('total_harga');
		$total_bayar = $this->db->get_where('detail_pembelian', ['faktur_pembelian' => $post['faktur_pembelian']])->row_array()['total_harga'];

		$data_pembelian = [
			'id_petugas' => $post['id_petugas'],
			'tgl_pembelian' => $post['tgl_pembelian'],
			'referensi' => $post['referensi'],
			'tgl_jatuh_tempo' => $post['tgl_jatuh_tempo'],
			'id_supplier' => $post['id_supplier'],
			'total_bayar' => $total_bayar
		];

		$this->db->where('faktur_pembelian', $post['faktur_pembelian']);
		$this->db->update('pembelian', $data_pembelian);
		$this->updateStatus($post['faktur_pembelian']);

		$this->db->trans_complete();
	}

	public function get_pembelian($id)
	{
		$this->db->join('petugas', 'id_petugas', 'left');
		$this->db->join('supplier', 'id_supplier', 'left');
		return $this->db->get_where('pembelian', ['faktur_pembelian' => $id])->row_array();
	}

	public function get_detail_pembelian($id)
	{
		$this->db->join('barang', 'id_barang', 'left');
		return $this->db->get_where('detail_pembelian', ['faktur_pembelian' => $id])->result_array();
	}

	public function get_total_bayar($id)
	{
		$this->db->select_sum('nominal', 'total_bayar');
		$this->db->join('pembayaran_pembelian', 'faktur_pembelian');
		$this->db->where('faktur_pembelian', $id);
		return $this->db->get('pembelian')->row()->total_bayar;
	}

}

/* End of file pembelian_model.php */
/* Location: ./application/modules/pembelian/models/pembelian_model.php */ ?>