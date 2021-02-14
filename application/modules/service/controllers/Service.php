<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class service extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		cek_login();
		$this->load->model('service/service_model');
		$this->load->model('pelanggan/pelanggan_model');
		$this->load->model('karyawan/karyawan_model');
	}

	public function get_service_json()
	{
		header('Content-Type: application/json');
		echo $this->service_model->get_service_json();
	}

	public function get_service($id_service)
	{
		header('Content-Type: application/json');
		echo json_encode($this->service_model->get_service($id_service));
	}
	
	public function index()
	{
		$data['judul'] = "Riwayat Service";

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('service/index', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function tambah()
	{
		$valid = $this->form_validation;
		$valid->set_rules('id_service', 'id service', 'required');

		if ($valid->run()) {
			$this->service_model->insert($this->input->post());
			$this->session->set_flashdata('success', 'ditambah');
			redirect('service/invoice/' . $this->input->post('id_service'),'refresh');
		}

		$data['judul'] = "Service Baru";
		$data['pelanggan'] = $this->pelanggan_model->get_pelanggan();
		$data['karyawan'] = $this->karyawan_model->get_karyawan();

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('service/tambah', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function ubah($id_service)
	{
		$valid = $this->form_validation;
		$valid->set_rules('id_service', 'id service', 'required');

		if ($valid->run()) {
			$this->service_model->update($id_service, $this->input->post());
			$this->session->set_flashdata('success', 'ditambah');
			redirect('service','refresh');
		}

		$data['judul'] = "Ubah service";
		$data['karyawan'] = $this->karyawan_model->get_karyawan();
		$data['pelanggan'] = $this->pelanggan_model->get_pelanggan();
		$data['service'] = $this->service_model->get_service($id_service);

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('service/ubah', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function invoice($id_service)
	{
		$data['judul'] = "Invoice Service";
		$data['service'] = $this->service_model->get_service($id_service);

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('service/invoice', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function invoice_cetak($id_service)
	{
		$data['judul'] = "Invoice Service";
		$data['service'] = $this->service_model->get_service($id_service);

		$this->load->view('service/invoice_cetak', $data, FALSE);
	}

	public function hapus($id = '')
	{
		$this->service_model->delete($id);
		$this->session->set_flashdata('success', 'dihapus');
		redirect('service','refresh');
	}

	public function export_service()
	{
		$service = $this->service_model->get_service();

		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', 'Kode service')
		->setCellValue('B1', 'Tanggal Service')
		->setCellValue('C1', 'Tanggal Diambil')
		->setCellValue('D1', 'Karyawan')
		->setCellValue('E1', 'Status')
		->setCellValue('F1', 'Total Bayar')
		;
		
		$i=2; 
		foreach($service as $row) {
			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A' . $i, $row['id_service'])
			->setCellValue('B' . $i, $row['tgl_service'])
			->setCellValue('C' . $i, $row['tgl_ambil'])
			->setCellValue('D' . $i, $row['nama_karyawan'])
			->setCellValue('E' . $i, $row['status'])
			->setCellValue('F' . $i, $row['total_bayar'])
			;
			$i++;
		}                           

		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data service.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

}

/* End of file service.php */
/* Location: ./application/modules/service/controllers/service.php */ ?>