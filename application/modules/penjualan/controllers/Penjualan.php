<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require ('./vendor/autoload.php');

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class penjualan extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		cek_login();
		$this->load->model('penjualan/penjualan_model');
		$this->load->model('barang/barang_model');
		$this->load->model('pelanggan/pelanggan_model');
		$this->load->model('kategori/kategori_model');
	}

	public function verify_password()
	{
		$password = $this->input->post('password');

		$pw = $this->db->get('pengaturan')->row()->password_penjualan;

		if ($password == $pw) {
			echo 'true';
		}else{
			echo 'false';
		}
	}

	public function get_penjualan($id)
	{		
		echo json_encode($this->penjualan_model->get_penjualan($id));
	}

	public function get_penjualan_by_pelanggan($id)
	{		
		
		echo json_encode($this->penjualan_model->get_penjualan_by_pelanggan($id));
	}

	public function get_limit_pelanggan($id)
	{		
		if ($this->penjualan_model->get_limit_pelanggan($id) >= 3) {
			echo 'true';
			die;
		}else{
			echo 'false' ;
			die;

		}

	}


	public function get_detail_penjualan($id)
	{
		$this->db->select('id_barang,nama_barang,jumlah,diskon');
		$this->db->join('barang', 'id_barang');
		$detail_penjualan = $this->db->get_where('detail_penjualan', ['faktur_penjualan' => $id])->result_array();
		echo json_encode($detail_penjualan);

	}

	public function index()
	{
		$petugas = $this->session->userdata('id_outlet');

		!empty($petugas) ? $id_outlet = $petugas : $id_outlet = $this->db->get('outlet')->row()->id_outlet;

		$data['judul'] = "Penjualan";
		$data['pelanggan'] = $this->pelanggan_model->get_pelanggan();

		$this->db->where('id_outlet', $id_outlet);
		$data['karyawan'] = $this->db->get('karyawan')->result_array();
		$data['kategori'] = $this->kategori_model->get_kategori();
		$data['service'] = $this->db->get_where('service', ['status' => 'BELUM DITERIMA'])->result_array();

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/index', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function ubah($id)
	{
		$data['judul'] = "Ubah Penjualan";
		$data['barang'] = $this->barang_model->get_barang();
		$data['pelanggan'] = $this->pelanggan_model->get_pelanggan();
		$data['kategori'] = $this->kategori_model->get_kategori();
		$data['kategori'] = $this->kategori_model->get_kategori();

		$data['penjualan'] = $this->penjualan_model->get_penjualan($id);
		$data['detail_penjualan'] = $this->penjualan_model->get_detail_penjualan($id);
		$data['kunci_penjualan'] = $this->db->get('pengaturan')->row()->kunci_penjualan;
		$data['service'] = $this->db->get('service')->result_array();

		$petugas = $this->session->userdata('id_outlet');

		!empty($petugas) ? $id_outlet = $petugas : $id_outlet = $this->db->get('outlet')->row()->id_outlet;
		$this->db->where('id_outlet', $id_outlet);
		$data['karyawan'] = $this->db->get('karyawan')->result_array();

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/ubah', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function surat_jalan($id)
	{
		$data['judul'] = "Surat Jalan";
		$data['penjualan'] = $this->penjualan_model->get_penjualan($id);
		$data['detail_penjualan'] = $this->penjualan_model->get_detail_penjualan($id);

		$this->load->view('penjualan/surat_jalan', $data, FALSE);
	}

	public function proses()
	{
		$array = array(
			'id_karyawan' => $_POST['id_karyawan']
		);
		
		$this->session->set_userdata( $array );

		if ($_POST['cash'] == '') {
			$this->session->set_flashdata('error', 'Anda belum memasukan cash');
			redirect('penjualan','refresh');
		}

		if (!isset($_POST['id_barang'])) {
			$this->session->set_flashdata('error', 'Anda belum memasukan item barang');
			redirect('penjualan','refresh');
		}

		$this->penjualan_model->proses($this->input->post());

		$pengaturan = $this->db->get('pengaturan')->row_array();

		if ($pengaturan['print_otomatis'] == 1) {
			$this->cetak_thermal($faktur, false);
		}
		redirect('penjualan/invoice_cetak/' . $this->input->post('faktur_penjualan'),'refresh');
	}

	public function proses_update()
	{
		if (!isset($_POST['id_barang'])) {
			$this->session->set_flashdata('error', 'Anda belum memasukan item barang');
			redirect('penjualan','refresh');
		}

		$this->penjualan_model->proses_update($this->input->post());

		$pengaturan = $this->db->get('pengaturan')->row_array();

		if ($pengaturan['print_otomatis'] == 1) {
			$this->cetak_thermal($faktur, false);
		}
		redirect('penjualan/invoice_cetak/' . $this->input->post('faktur_penjualan'),'refresh');
	}

	public function invoice($id)
	{
		$data['judul'] = "Invoice " . $id;
		$data['penjualan'] = $this->penjualan_model->get_penjualan($id);
		$data['total_belanja'] = $this->penjualan_model->get_total_belanja($id);
		$data['total_bayar'] = $this->penjualan_model->get_total_bayar($id);

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/invoice', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function invoice_cetak($id)
	{
		$data['judul'] = "Invoice " . $id;
		$data['total_belanja'] = $this->penjualan_model->get_total_belanja($id);
		$data['penjualan'] = $this->penjualan_model->get_penjualan($id);
		$data['detail_penjualan'] = $this->penjualan_model->get_detail_penjualan($id);
		$data['total_bayar'] = $this->penjualan_model->get_total_bayar($id);

		$this->load->view('penjualan/invoice_cetak', $data, FALSE);
	}

	public function cetak_thermal($id, $redirect = true)
	{
		$pengaturan = $this->db->get('pengaturan')->row_array();

		if ($id_outlet = $this->session->userdata('id_outlet')) {
			$outlet = $this->db->get_where('outlet', ['id_outlet' => $id_outlet])->row_array();
		}else{
			$outlet = $this->db->get('outlet')->row_array();
		}

		$total_belanja = $this->penjualan_model->get_total_belanja($id);
		$penjualan = $this->penjualan_model->get_penjualan($id);
		$total_bayar = $this->penjualan_model->get_total_bayar($id);

		try {
			
			// windows
			$connector = new WindowsPrintConnector($pengaturan['nama_printer']);

			$printer = new Printer($connector);
			$printer -> pulse();
			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			$printer -> text($outlet['nama_outlet'] ."\n");
			$printer -> text($outlet['alamat'] . "\n");
			$printer -> feed(1);
			$printer -> text("Telp: " . $outlet['telepon'] ."\n");
			$printer -> text("Email: " . $outlet['email'] . "\n");
			$printer -> text("---------------------------------------\n");
			$printer -> setJustification(Printer::JUSTIFY_LEFT);
			$printer -> text("Faktur     : ".$penjualan['faktur_penjualan']."\n");
			$printer -> text("Kasir      : ".$penjualan['nama_karyawan']."\n");

			if ($penjualan['jenis'] == 'member') {
				$printer -> text("Nama       : ".$penjualan['nama_pelanggan']."\n");
				$printer -> text("Alamat     : ".$penjualan['alamat']."\n");
			}else{
				if ($penjualan['nama_pengiriman'] != '' || $penjualan['alamat_pengiriman'] != '') {
					$printer -> text("Nama       : ".$penjualan['nama_pengiriman']."\n");
					$printer -> text("Alamat     : ".$penjualan['alamat_pengiriman']."\n");
				}
			}

			$printer -> text("----------------------------------------\n");
			$printer -> text("Barang     Diskon   Jml  Harga  SubTotal\n");
			$printer -> text("----------------------------------------\n");

    		// item barang
			$no = 1;
			$this->db->join('barang', 'id_barang');
			$barang =  $this->db->get_where('detail_penjualan', ['faktur_penjualan' => $penjualan['faktur_penjualan']])->result_array();
			foreach ($barang as $row) {
				$this->db->select($row['type_golongan'] . ' AS harga_jual');
				$harga_jual = $this->db->get_where('barang', ['id_barang' => $row['id_barang']])->row()->harga_jual;
				$harga = number_format($harga_jual);
				$total_harga = number_format($row['total_harga']);
				$printer -> text($row['nama_pendek']."\n");
				$printer -> setJustification(2);
				$printer -> text("{$row['diskon']}   {$row['jumlah']} X {$harga}   {$total_harga}\n");
				$printer -> setJustification();
			}

			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			$printer -> text("---------------------------------------\n");
			$printer -> setJustification(Printer::JUSTIFY_RIGHT);

			if ($penjualan['id_service'] != '') {
				$harga_jasa = sprintf('%-5.40s %-1.05s %13.40s','Harga Jasa','=', number_format($penjualan['harga_jasa']));
				$printer -> text("$harga_jasa\n");
			}

			$lineTotal = sprintf('%-5.40s %-1.05s %13.40s','Total Belanja','=', number_format($total_belanja + $penjualan['harga_jasa']));
			$printer -> text("$lineTotal\n");

			$lineDisc = sprintf('%-5.40s %-1.05s %13.40s','Diskon','=', $penjualan['diskon']);
			$printer -> text("$lineDisc\n");

			$linePotongan = sprintf('%-5.40s %-1.05s %13.40s','Potongan','=', number_format($penjualan['potongan']));
			$printer -> text("$linePotongan\n");
			
			$lineTotal = sprintf('%-5.40s %-1.05s %13.40s','Total Bayar','=', number_format($penjualan['total_bayar']));
			$printer -> text("$lineTotal\n");

			$cash = sprintf('%-5.40s %-1.05s %13.40s','Cash','=', number_format($total_bayar));
			$printer -> text("$cash\n");

			if ($total_bayar >= $penjualan['total_bayar']) {
				$lineKembalian = sprintf('%-5.40s %-1.05s %13.40s','Kembalian','=', number_format($total_bayar - $penjualan['total_bayar']));
				$printer -> text("$lineKembalian\n");
			}else{
				$lineKembalian = sprintf('%-5.40s %-1.05s %13.40s','Harus Dibayar','=', number_format($penjualan['total_bayar'] - $total_bayar));
				$printer -> text("$lineKembalian\n");
			}

			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			$printer -> text("---------------------------------------\n");
			$printer -> feed(1);
			$printer -> text("***TERIMA KASIH***\n");
			$printer -> text($pengaturan['keterangan_invoice']."\n");


			$printer -> cut();
			$printer -> close();

			if ($redirect == true) {
				redirect('penjualan','refresh');
			}


		} catch (Exception $e) {
			echo "Error: " . $e -> getMessage() . "\n";
		}
	}

	public function buka_laci()
	{
		$pengaturan = $this->db->get('pengaturan')->row_array();
		$connector = new WindowsPrintConnector($pengaturan['nama_printer']);
		$printer = new Printer($connector);
		$printer -> pulse();
		$printer -> close();
	}

	public function tambah_pelanggan()
	{
		$post = $this->input->post();

		$this->db->insert('pelanggan', [
			'id_pelanggan' => $post['id'],
			'nama_pelanggan' => $post['nama_pelanggan'],
			'alamat' => $post['alamat'],
			'telepon' => $post['telepon'],
			'jk' => $post['jk'],
			'jenis' => $post['jenis']
		]);

		$arr = [];
		$arr['id_pelanggan'] = $post['id'];
		$arr['nama_pelanggan'] = $post['nama_pelanggan'];

		echo json_encode($arr);
	}

	public function register()
	{
		$valid = $this->form_validation;
		$valid->set_rules('uang_awal', 'uang awal', 'required');

		if ($valid->run()) {
			$this->penjualan_model->set_register($this->input->post());
			redirect('penjualan','refresh');
		}

		$data['judul'] = "Register";

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/register', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function close_register()
	{
		$this->penjualan_model->close_register();

		$this->db->select('MAX(id_register) AS id');
		$this->db->where('id_petugas', $this->session->userdata('id_petugas'));
		$id = $this->db->get('register')->row_array()['id'];
		redirect('laporan/cetak_register/' . $id,'refresh');
	}

	public function pembayaran($id)
	{
		$data['judul'] = "Data Pembayaran";
		$data['pembayaran'] = $this->penjualan_model->get_pembayaran($id);
		$data['faktur_penjualan'] = $id;

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/pembayaran', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function tambah_pembayaran($id)
	{
		$data['judul'] = "Tambah Pembayaran";
		$data['pembayaran'] = $this->penjualan_model->get_pembayaran($id);
		$data['faktur_penjualan'] = $id;

		$this->form_validation->set_rules('nominal', 'nominal', 'required');

		if ($this->form_validation->run()) {
			$this->penjualan_model->tambah_pembayaran($this->input->post());
			$this->session->set_flashdata('success', 'Ditambah');
			redirect('penjualan/pembayaran/' . $id,'refresh');
		}

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/tambah_pembayaran', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function ubah_pembayaran($id)
	{
		$data['judul'] = "Ubah Pembayaran";
		$data['pembayaran'] = $this->penjualan_model->get_pembayaran($id, true);
		$data['faktur_penjualan'] = $this->penjualan_model->get_pembayaran($id, true)['faktur_penjualan'];

		$this->form_validation->set_rules('nominal', 'nominal', 'required');

		if ($this->form_validation->run()) {
			$this->penjualan_model->ubah_pembayaran($this->input->post('id_pembayaran'), $this->input->post());
			$this->session->set_flashdata('success', 'Diubah');
			redirect('penjualan/pembayaran/' . $data['faktur_penjualan'],'refresh');
		}

		$this->load->view('templates/header', $data, FALSE);
		$this->load->view('penjualan/ubah_pembayaran', $data, FALSE);
		$this->load->view('templates/footer', $data, FALSE);
	}

	public function hapus_pembayaran($id, $faktur_penjualan)
	{
		$this->penjualan_model->hapus_pembayaran($id, $faktur_penjualan);
		$this->session->set_flashdata('success', 'Dihapus');
		redirect('penjualan/pembayaran/' . $faktur_penjualan,'refresh');
	}

}

/* End of file penjualan.php */
/* Location: ./application/modules/penjualan/controllers/penjualan.php */ ?>