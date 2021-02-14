	<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class service_model extends CI_Model {

	public function get_service_json()
	{
		$this->datatables->select('id_service, tgl_service, nama_karyawan,tgl_ambil,status,total_bayar');
		$this->datatables->join('karyawan', 'id_karyawan', 'left');
		$this->db->order_by('tgl_service', 'desc');
		$this->datatables->from('service');
		return $this->datatables->generate();
	}

	public function get_service($id = '')
	{
		if ($id == '') {
			$this->db->join('karyawan', 'id_karyawan');
			$this->db->join('outlet', 'karyawan.id_outlet = outlet.id_outlet');
			return $this->db->get('service')->result_array();
		}else {
			$this->db->select('*');
			$this->db->join('karyawan', 'id_karyawan');
			$this->db->join('outlet', 'karyawan.id_outlet = outlet.id_outlet');
			$this->db->where('id_service', $id);
			return $this->db->get('service')->row_array();
		}
	}

	public function delete($id)
	{

		$this->db->join('detail_penjualan', 'faktur_penjualan', 'left');
		$sv = $this->db->get_where('penjualan', ['id_service' => $id])->result_array();

		foreach ($sv as $row) {

			// tambah stok barang
			$stok_barang = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row_array()['stok'];
			$stok_barang += $row['jumlah'];

			$this->db->set('stok', $stok_barang);
			$this->db->where('id_barang', $row['id_barang']);
			$this->db->update('barang');


			$this->db->delete('detail_penjualan', ['faktur_penjualan' => $row['faktur_penjualan']]);
		}


		$this->db->delete('penjualan', ['id_service' => $id]);

		$this->db->delete('service', ['id_service' => $id]);
	}

	public function insert($post)
	{
		$this->db->trans_start();

		$data = [
			'id_service' => $post['id_service'],
			'id_karyawan' => $post['id_karyawan'],
			'tgl_service' => $post['tgl_service'],
			'tgl_ambil' => $post['tgl_ambil'],
			'jenis_barang' => $post['jenis_barang'],
			'kerusakan' => $post['kerusakan'],
			'kelengkapan' => $post['kelengkapan'],
			'garansi' => $post['garansi'],
			'ketentuan_garansi' => $post['ketentuan_garansi'],
			'keterangan' => $post['keterangan'],
			'serial_number' => $post['serial_number'],
			'nama_service' => $post['nama_service'],
			'alamat_service' => $post['alamat_service'],
			'telepon_service' => $post['telepon_service'],
			'status' => $post['status']
		];

		$this->db->insert('service', $data);

		$this->db->trans_complete();
	}

	public function update($id_service, $post)
	{
		$this->db->trans_start();

		$data = [
			'id_karyawan' => $post['id_karyawan'],
			'tgl_service' => $post['tgl_service'],
			'tgl_ambil' => $post['tgl_ambil'],
			'jenis_barang' => $post['jenis_barang'],
			'kerusakan' => $post['kerusakan'],
			'kelengkapan' => $post['kelengkapan'],
			'garansi' => $post['garansi'],
			'ketentuan_garansi' => $post['ketentuan_garansi'],
			'keterangan' => $post['keterangan'],
			'serial_number' => $post['serial_number'],
			'nama_service' => $post['nama_service'],
			'alamat_service' => $post['alamat_service'],
			'telepon_service' => $post['telepon_service'],
			'status' => $post['status']
		];

		$this->db->where('id_service', $id_service);
		$this->db->update('service', $data);

		$this->db->trans_complete();
	}

}

/* End of file service_model.php */
/* Location: ./application/modules/service/models/service_model.php */ ?>