<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class barang extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		cek_login();
		$this->load->model('barang/barang_model');
		$this->load->model('kategori/kategori_model');
		$this->load->model('supplier/supplier_model');
	}

	public function get_barang_json()
	{
		header('Content-Type: application/json');
		echo $this->barang_model->get_barang_json();
	}

	public function get_harga_barang_json()
	{
		header('Content-Type: application/json');
		echo $this->barang_model->get_harga_barang_json();
	}

	public function get_barang_by_id($id_barang = '', $id_outlet = '')
	{
		$this->db->select('id_barang,nama_barang,barcode,barang.stok AS stok_komputer');
		$this->db->join('barang', 'id_barang');
		$this->db->join('outlet', 'id_outlet');
		if ($id_outlet != '') {
			$this->db->where('id_outlet', $id_outlet);
		}
		$this->db->where('id_barang', $id_barang);
		$this->db->or_where('barcode', $id_barang);
		echo json_encode($this->db->get('barang')->row_array());
	}

	public function get_barang_by_outlet_json($id_outlet)
	{
		header('Content-Type: application/json');
		echo $this->barang_model->get_barang_by_outlet_json($id_outlet);
	}

	public function get_barang($id)
	{
		echo json_encode($this->barang_model->get_barang($id));
	}

	public function get_barang_by_kategori($id = 'SEMUA')
	{
		if ($id == 'SEMUA') {
			
			$result = $this->db->get('barang')->result_array();
		}else{
			
			$result = $this->db->get_where('barang', ['id_kategori' => $id])->result_array();
		}

		echo json_encode($result);
	}

	public function get_barang_by_name($name = '#####')
	{
		$this->db->like('nama_barang', urldecode($name));
		$this->db->or_like('nama_pendek', urldecode($name));
		$result = $this->db->get_where('barang')->result_array();

		echo json_encode($result);
	}

	public function get_barang_by_barcode($barcode = '#####')
	{
		$this->db->where('barcode', urldecode($barcode));
		$this->db->or_where('id_barang', urldecode($barcode));
		$result = $this->db->get_where('barang')->result_array();

		echo json_encode($result);
	}

	public function hapus($id)
	{
		$this->barang_model->delete($id);
		$this->session->set_flashdata('success', 'dihapus');
		redirect('master/barang','refresh');
	}

	public function index()
	{
		$data['judul'] = "Data Barang";

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('barang/index', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function tambah()
	{
		$valid = $this->form_validation;
		$valid->set_rules('nama_barang', 'nama barang', 'required');
		$valid->set_rules('satuan', 'satuan', 'required');
		$valid->set_rules('id_kategori', 'kategori', 'required');
		$valid->set_rules('id_supplier', 'supplier', 'required');
		$valid->set_rules('harga_pokok', 'harga pokok', 'required');
		$valid->set_rules('id_barang', 'id barang', 'required');
		$valid->set_rules('nama_pendek', 'nama pendek', 'required');

		if ($valid->run()) {
			$this->barang_model->insert($this->input->post());
			$this->session->set_flashdata('success', 'ditambah');
			redirect('master/barang','refresh');
		}

		$data['judul'] = "Tambah barang";
		$data['kategori'] = $this->kategori_model->get_kategori();
		$data['supplier'] = $this->supplier_model->get_supplier();

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('barang/tambah', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function ubah($id)
	{
		$valid = $this->form_validation;
		$valid->set_rules('nama_barang', 'nama barang', 'required');
		$valid->set_rules('satuan', 'satuan', 'required');
		$valid->set_rules('id_kategori', 'kategori', 'required');
		$valid->set_rules('id_supplier', 'supplier', 'required');
		$valid->set_rules('harga_pokok', 'harga pokok', 'required');
		$valid->set_rules('id_barang', 'id_barang', 'required');
		$valid->set_rules('nama_pendek', 'nama pendek', 'required');

		if ($valid->run()) {
			$this->barang_model->update($id, $this->input->post());
			$this->session->set_flashdata('success', 'diubah');
			redirect('master/barang','refresh');
		}

		$data['judul'] = "Ubah barang";
		$data['barang'] = $this->barang_model->get_barang($id);
		$data['kategori'] = $this->kategori_model->get_kategori();
		$data['supplier'] = $this->supplier_model->get_supplier();

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('barang/ubah', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function export()
	{
		$barang = $this->barang_model->get_barang();
		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', 'Kode barang')
		->setCellValue('B1', 'Kode Kategori')
		->setCellValue('C1', 'Kode Supplier')
		->setCellValue('D1', 'Satuan')
		->setCellValue('E1', 'Barcode')
		->setCellValue('F1', 'Nama Barang')
		->setCellValue('G1', 'Nama Pendek')
		->setCellValue('H1', 'Harga Pokok')
		->setCellValue('I1', 'Diskon')
		->setCellValue('J1', 'Stok')
		->setCellValue('K1', 'Harga Jual')
		->setCellValue('L1', 'Profit')
		;
		// Miscellaneous glyphs, UTF-8
		$i=2; 
		foreach($barang as $row) {
			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A' . $i, $row['id_barang'])
			->setCellValue('B' . $i, $row['id_kategori'])
			->setCellValue('C' . $i, $row['id_supplier'])
			->setCellValue('D' . $i, $row['satuan'])
			->setCellValue('E' . $i, $row['barcode'])
			->setCellValue('F' . $i, $row['nama_barang'])
			->setCellValue('G' . $i, $row['nama_pendek'])
			->setCellValue('H' . $i, $row['harga_pokok'])
			->setCellValue('I' . $i, $row['diskon'])
			->setCellValue('J' . $i, $row['stok'])
			->setCellValue('K' . $i, $row['harga_jual'])
			->setCellValue('L' . $i, $row['profit']);
			$i++;
		}                           

		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data barang.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function template()
	{
		$barang = $this->barang_model->get_barang();
		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', 'Kode barang')
		->setCellValue('B1', 'Kode Kategori')
		->setCellValue('C1', 'Kode Supplier')
		->setCellValue('D1', 'Satuan')
		->setCellValue('E1', 'Barcode')
		->setCellValue('F1', 'Nama Barang')
		->setCellValue('G1', 'Nama Pendek')
		->setCellValue('H1', 'Harga Pokok')
		->setCellValue('I1', 'Diskon')
		->setCellValue('J1', 'Stok')
		->setCellValue('K1', 'Harga Jual')
		;                      

		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data barang.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function import()
	{
		$file = explode('.', $_FILES['excel']['name']);
		$extension = end($file);

		if($extension == 'csv') {
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		} else {
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}

		$spreadsheet = $reader->load($_FILES['excel']['tmp_name']);
		$sheetData = $spreadsheet->getActiveSheet()->toArray();
		for($i = 1;$i < count($sheetData); $i++)
		{
			if ($sheetData[$i]['0'] != '') {

				$profit =  $sheetData[$i]['10'] - $sheetData[$i]['7'];

				$data = [
					'id_barang' => $sheetData[$i]['0'],
					'id_kategori' => $sheetData[$i]['1'],
					'id_supplier' => $sheetData[$i]['2'],
					'satuan' => $sheetData[$i]['3'],
					'barcode' => $sheetData[$i]['4'],
					'nama_barang' => $sheetData[$i]['5'],
					'nama_pendek' => $sheetData[$i]['6'],
					'harga_pokok' => $sheetData[$i]['7'],
					'diskon' => $sheetData[$i]['8'],
					'stok' => $sheetData[$i]['9'],
					'harga_jual' => $sheetData[$i]['10'],
					'profit' => $profit
				];

				$this->db->trans_start();

				if ($this->db->get_where('barang', ['id_barang' => $sheetData[$i]['0']])->row_array()) {
					$this->db->where('id_barang', $sheetData[$i]['0']);
					$this->db->update('barang', $data);
				}else{
					$this->db->insert('barang', $data);
				}

				$this->db->trans_complete();
			}
		}
		$this->session->set_flashdata('success', 'Di import');
		redirect('master/barang','refresh');
	}

	public function hapus_semua()
	{
		$this->db->query('DELETE FROM barang');
		$this->session->set_flashdata('success', 'Di dihapus');
		redirect('master/barang','refresh');
	}

}

/* End of file barang.php */
/* Location: ./application/modules/barang/controllers/barang.php */ ?>