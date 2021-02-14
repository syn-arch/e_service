<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class barang_model extends CI_Model {

	public function get_barang_json()
	{
		$this->datatables->select('id_barang, harga_jual,barcode,nama_barang, diskon, harga_pokok, nama_kategori, nama_supplier, satuan,stok');
		$this->datatables->from('barang');
		$this->datatables->join('kategori', 'barang.id_kategori=kategori.id_kategori', 'left');
		$this->datatables->join('supplier', 'barang.id_supplier=supplier.id_supplier', 'left');
		return $this->datatables->generate();
	}

	public function get_harga_barang_json()
	{
		$this->datatables->select('id_barang, barcode,nama_barang, diskon, harga_pokok, harga_jual , nama_kategori, nama_supplier, satuan, diskon, barang.stok AS stok_q');
		$this->datatables->from('barang');
		$this->datatables->join('kategori', 'barang.id_kategori=kategori.id_kategori', 'left');
		$this->datatables->join('supplier', 'barang.id_supplier=supplier.id_supplier', 'left');
		return $this->datatables->generate();
	}

	public function get_barang_by_outlet_json($id_outlet)
	{
		$this->datatables->select('id_barang,id_barang, barcode, nama_barang, barang.stok AS barang');
		$this->datatables->from('barang');
		$this->datatables->join('outlet', 'id_outlet');
		$this->datatables->join('barang', 'id_barang');
		$this->datatables->where('id_outlet', $id_outlet);
		return $this->datatables->generate();
	}

	public function get_barang($id = '')
	{
		if ($id == '') {
			$this->db->join('kategori', 'id_kategori', 'left');
			$this->db->join('supplier', 'id_supplier', 'left');
			return $this->db->get('barang')->result_array();
		}else {
			$this->db->join('kategori', 'id_kategori', 'left');
			$this->db->join('supplier', 'id_supplier', 'left');
			$this->db->where('id_barang', $id);
			$this->db->or_where('barcode', $id);
			return $this->db->get('barang')->row_array();
		}
	}

	public function delete($id)
	{
		$brg = $this->db->get_where('barang', ['id_barang' => $id])->row_array();

		if ($brg['gambar'] != '') {
			delImage('barang', $id);	
		}
		
		$this->db->delete('barang', ['id_barang' => $id]);
	}

	public function insert($post)
	{
		$profit = $post['harga_jual'] - $post['harga_pokok'];

		$data = [
			'id_barang' => $post['id_barang'],
			'nama_barang' => $post['nama_barang'],
			'id_kategori' => $post['id_kategori'],
			'id_supplier' => $post['id_supplier'],
			'satuan' => $post['satuan'],
			'barcode' => $post['barcode'],
			'harga_pokok' => $post['harga_pokok'],
			'harga_jual' => $post['harga_jual'],
			'diskon' => $post['diskon'],
			'stok' => $post['stok'],
			'nama_pendek' => $post['nama_pendek'],
			'profit' => $profit
		];

		
		if ($_FILES['gambar']['name']) {
			$data['gambar'] = _upload('gambar', 'master/tambah_barang', 'barang');
		}

		$this->db->insert('barang', $data);
	}

	public function update($id, $post)
	{
		$profit = $post['harga_jual'] - $post['harga_pokok'];

		$data = [
			'nama_barang' => $post['nama_barang'],
			'id_kategori' => $post['id_kategori'],
			'id_supplier' => $post['id_supplier'],
			'satuan' => $post['satuan'],
			'diskon' => $post['diskon'],
			'barcode' => $post['barcode'],
			'harga_pokok' => $post['harga_pokok'],
			'harga_jual' => $post['harga_jual'],
			'diskon' => $post['diskon'],
			'stok' => $post['stok'],
			'nama_pendek' => $post['nama_pendek'],
			'profit' => $profit
		];

		if ($_FILES['gambar']['name']) {
			$data['gambar'] = _upload('gambar', 'master/ubah_barang/' . $id, 'barang');
			delImage('barang', $id);
		}

		$this->db->where('id_barang', $id);
		$this->db->update('barang', $data);
	}

}

/* End of file barang_model.php */
/* Location: ./application/modules/barang/models/barang_model.php */ ?>