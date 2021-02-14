<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
		$data['judul'] = "Dashboard";

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('dashboard', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function get_stok_tipis_json()
	{
		$pengaturan = $this->db->get('pengaturan')->row_array();

		header('Content-Type: application/json');
		$this->datatables->select('id_barang,nama_barang,stok,barcode');
		$this->datatables->from('barang');
		$this->datatables->where('barang.stok <=', $pengaturan['peringatan_stok']);
		$this->datatables->where('barang.stok >', 0);
		$stok_tipis = $this->datatables->generate();

		echo $stok_tipis;
	}

	public function get_stok_habis_json()
	{
		$pengaturan = $this->db->get('pengaturan')->row_array();

		header('Content-Type: application/json');
		$this->datatables->select('id_barang,nama_barang,stok,barcode');
		$this->datatables->from('barang');
		$this->datatables->where('barang.stok <=', 0);
		$stok_habis = $this->datatables->generate();

		echo $stok_habis;
	}
}
