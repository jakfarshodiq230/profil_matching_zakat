<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Admin extends CI_Controller {

	public function __construct(){

		parent::__construct();
		/*-- Check Session  --*/
		is_login();
		is_level();


		/*-- untuk mengatasi error confirm form resubmission  --*/
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0',false);
		header('Pragma: no-cache');
		$this->load->model('admin_model');
		$this->load->library('Pdf');
		$this->load->library('hijriyah');
	}

	public function index(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['ttlCalonPegawai'] = $this->admin_model->getCountCalonPegawai();
		$data['ttlAspekPenilaian'] = $this->admin_model->getCountAspekPenilaian();
		$data['ttlFaktorPenilaian'] = $this->admin_model->getCountFaktorPenilaian();
		$data['ttlKandidat'] = $this->admin_model->getCountKandidat();

		$data['title'] = "Profile Matching";
		$data['parent'] = "PM";
		$data['page'] = "Beranda";
		$this->template->load('admin/layout/template','admin/modul_beranda/index',$data);

	}

	public function dataAdministrator(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['administrator'] = $this->admin_model->getAdministrator();
		$data['title'] = "Profile Matching";
		$data['parent'] = "Data";
		$data['page'] = "Administrator";
		$this->template->load('admin/layout/template','admin/modul_administrator/administrator',$data);
	}

	public function dataAdministratorAdd(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$this->form_validation->set_rules('nama_administrator','Nama Administrator','required');
		$this->form_validation->set_rules('username','Username','required|trim|is_unique[administrator.nama_administrator]', [
			'is_unique' => 'This Username Alredy Taken!'
		]);
		$this->form_validation->set_rules('password','Password','required|trim|min_length[5]',[
			'min_length' => 'Password to short, min 5 Character!'
		]);

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Administrator";
			$data['page'] = "Add Administrator";
			$this->template->load('admin/layout/template','admin/modul_administrator/administratorAdd',$data);

		}else{

			$data = [

				'nama_administrator' => $this->db->escape_str(ucfirst($this->input->post('nama_administrator')),true),
				'username' => $this->db->escape_str(htmlspecialchars($this->input->post('username')),true),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'level' => $this->db->escape_str($this->input->post('level'),true),
				'aktif' => $this->db->escape_str($this->input->post('aktif'),true)
			];

			$this->db->insert('administrator',$data);
			$this->session->set_flashdata('success','Data Administrator "'.$this->input->post('nama_administrator').'" Berhasil Di Tambahkan');
			redirect('admin/dataAdministrator');

		}
	}

	public function dataAdministratorEdit($id_administrator = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$oneadministrator = $this->db->get_where('administrator',['id_administrator' => $this->encrypt->decode($id_administrator)])->row();

		/*-- Encrypt URL NIM --*/
		if (count($this->uri->segment_array()) > 3) {
			$this->session->set_flashdata('message','URL yang anda masukkan salah');
			redirect('data/dataAdministrator');
		}
		if (!isset($id_administrator)) {
			$this->session->set_flashdata('message','Data yang Anda Inginkan Tidak Mempunyai ID');
			redirect('data/dataAdministrator');
		}
		if (is_numeric($id_administrator)) {
			$this->session->set_flashdata('message','Url Hanya Bisa Diakses Setelah Terenkripsi');
			redirect('data/dataAdministrator');
		} 

//id_administrator masih salah
		if(!$oneadministrator->id_administrator == $this->encrypt->decode($id_administrator)){
			$this->session->set_flashdata('message','NIK Yang Diminta Tidak Sama');
			redirect('data/dataAdministrator');
		}



		$data['oneadministrator'] = $this->admin_model->getOneAdministrator($this->encrypt->decode($id_administrator));

		$this->form_validation->set_rules('nama_administrator','Nama Administrator','required');
		$this->form_validation->set_rules('username','Username','required|trim');
		$this->form_validation->set_rules('password','Password','trim|min_length[5]',[
			'min_length' => 'Password to short, min 5 Character!'
		]);


		$data['oldpassword'] = $oneadministrator->password;

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Administrator";
			$data['page'] = "Edit Administrator";
			$this->template->load('admin/layout/template','admin/modul_administrator/administratorEdit',$data);

		}else{

			if(password_verify($this->input->post('password'),$oneadministrator->password)) {

				$this->session->set_flashdata('message','Password yang anda masukkan sama dengan password yang anda gunakan saat ini!');
				$data['title'] = "Profile Matching";
				$data['parent'] = "Administrator";
				$data['page'] = "Edit Administrator";
				$this->template->load('admin/layout/template','admin/modul_administrator/administratorEdit',$data);

			}else{

				$data = [

					'nama_administrator' => $this->db->escape_str(ucfirst($this->input->post('nama_administrator')),true),
					'username' => $this->db->escape_str(htmlspecialchars($this->input->post('username')),true),
					'level' => $this->db->escape_str($this->input->post('level'),true),
					'aktif' => $this->db->escape_str($this->input->post('aktif'),true)
				];

				if(!empty($this->input->post('password'))) {

					$data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

				}else{

                    // We don't save an empty password
					unset($data['password']);
				}

				$this->db->where('id_administrator', $this->input->post('zz'));
				$this->db->update('administrator',$data);
				$this->session->set_flashdata('success','Data Administrator "'.$this->input->post('nama_administrator').'" Berhasil Di Update');
				redirect('admin/dataAdministrator');

			}

		}

	}

	public function dataAdministratorDelete($id_administrator){

		$this->db->delete('administrator',['id_administrator' => $this->encrypt->decode($id_administrator)]);
		$this->session->set_flashdata('success','Data Administrator Yang Anda Pilih Berhasil Di Hapus');
		redirect('admin/dataAdministrator');

	}


	public function calonPenerima(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['pegawai'] = $this->admin_model->getPegawai();

		$data['title'] = "Profile Matching";
		$data['parent'] = "Data";
		$data['page'] = "Calon Penerima Zakat";
		$this->template->load('admin/layout/template','admin/modul_pegawai/pegawai',$data);

	}


	public function calonPenerimaAdd(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$this->form_validation->set_rules('nik_pegawai','NIK','required|trim|is_natural|is_unique[calon_penerima.nik]|exact_length[6]',[
			'is_natural' => 'ID Pegawai Hanya Berisi Angka!',
			'is_unique' => 'ID Pegawai Yang Anda Masukkan Telah Dipakai!',
			'exact_length' => 'ID Pegawai Harus 6 Angka'
		]);
		$this->form_validation->set_rules('nama_penerima','Nama Pegawai','required');
		// $this->form_validation->set_rules('tempat_lahir','Tempat Lahir','required');
		// $this->form_validation->set_rules('tanggal_lahir','Tanggal Lahir','required');
		$this->form_validation->set_rules('alamat','Alamat','required');

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Calon Pegawai";
			$data['page'] = "Add Calon Pegawai";
			$this->template->load('admin/layout/template','admin/modul_pegawai/pegawaiAdd',$data);

		}else{

			$data = [

				'nik' => $this->input->post('nik_pegawai'),
				'nama_penerima' => $this->db->escape_str(ucfirst($this->input->post('nama_penerima')),true),
				'tempat_lahir' => $this->db->escape_str(ucfirst($this->input->post('tempat_lahir')),true),
				'tanggal_lahir' => $this->db->escape_str($this->input->post('tanggal_lahir'),true),
				'jenis_kelamin' => $this->db->escape_str($this->input->post('jenis_kelamin'),true),
				'pendidikan' => $this->db->escape_str(strtoupper($this->input->post('pendidikan')),true),
				'alamat' => $this->db->escape_str($this->input->post('alamat'),true)

			];

			$this->db->insert('calon_penerima',$data);
			$this->session->set_flashdata('success','Data Pegawai "'.$this->input->post('nama_penerima').'" Berhasil Di Tambahkan');
			redirect('admin/calonPenerima');


		}
	}

	public function calonPenerimaEdit($nik = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		/*-- Encrypt URL NIM --*/
		if (count($this->uri->segment_array()) > 3) {
			$this->session->set_flashdata('message','URL yang anda masukkan salah');
			redirect('data/calonPenerima');
		}
		if (!isset($nik)) {
			$this->session->set_flashdata('message','Data yang Anda Inginkan Tidak Mempunyai ID');
			redirect('data/calonPenerima');
		}
		if (is_numeric($nik)) {
			$this->session->set_flashdata('message','Url Hanya Bisa Diakses Setelah Terenkripsi');
			redirect('data/calonPenerima');
		} 

		//id_administrator masih salah
		// if(!$oneadministrator->id_administrator == $this->encrypt->decode($id_administrator)){
		// 	$this->session->set_flashdata('message','NIK Yang Diminta Tidak Sama');
		// 	redirect('data/dataAdministrator');
		// }

		$data['onepegawai'] = $this->admin_model->getOnePegawai($this->encrypt->decode($nik));

		$this->form_validation->set_rules('nik_pegawai','NIK','required|trim|is_natural|exact_length[6]',[
			'is_natural' => 'ID Pegawai Hanya Berisi Angka!',
			'exact_length' => 'ID Pegawai Harus 6 Angka'
		]);
		$this->form_validation->set_rules('nama_penerima','Nama Pegawai','required');
		// $this->form_validation->set_rules('tempat_lahir','Tempat Lahir','required');
		// $this->form_validation->set_rules('tanggal_lahir','Tanggal Lahir','required');
		$this->form_validation->set_rules('alamat','Alamat','required');

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Calon Penerima Zakat";
			$data['page'] = "Edit Calon Penerima Zakat";
			$this->template->load('admin/layout/template','admin/modul_pegawai/pegawaiEdit',$data);

		}else{

			$data = [

				'nik' => $this->db->escape_str($this->input->post('nik_pegawai'),true),
				'nama_penerima' => $this->db->escape_str(ucfirst($this->input->post('nama_penerima')),true),
				'tempat_lahir' => $this->db->escape_str(ucfirst($this->input->post('tempat_lahir')),true),
				'tanggal_lahir' => $this->db->escape_str($this->input->post('tanggal_lahir'),true),
				'jenis_kelamin' => $this->db->escape_str($this->input->post('jenis_kelamin'),true),
				'pendidikan' => $this->db->escape_str(strtoupper($this->input->post('pendidikan')),true),
				'alamat' => $this->db->escape_str($this->input->post('alamat'),true)

			];

			$this->db->where('nik', $this->input->post('zz'));
			$this->db->update('calon_penerima',$data);
			$this->session->set_flashdata('success','Data Calon Penerima Zakat "'.$this->input->post('nama_penerima').'" Berhasil Di Update');
			redirect('admin/calonPenerima');

		}

	}

	public function calonPenerimaDelete($nik){
		$query = "SELECT * FROM kandidat where nik = '".$this->encrypt->decode($nik)."'";
		$kandidat = $this->db->query($query)->row();
		$this->db->delete('kandidat',['nik' => $this->encrypt->decode($nik)]);
		$this->db->delete('detail_calon',['id_kandidat' => $kandidat->id_kandidat]);
		$this->db->delete('calon_penerima',['nik' => $this->encrypt->decode($nik)]);
		$this->session->set_flashdata('success','Data Pegawai Yang Anda Pilih Berhasil Di Hapus');
		redirect('admin/calonPenerima');

	}

	public function aspek(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['aspek'] = $this->admin_model->getAspek();

		$data['title'] = "Profile Matching";
		$data['parent'] = "Data";
		$data['page'] = "Aspek Penilaian";
		$this->template->load('admin/layout/template','admin/modul_aspek/aspek',$data);
	}

	public function aspekAdd(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$this->form_validation->set_rules('nama_aspek','Nama Aspek','required');
		$this->form_validation->set_rules('bobot','Bobot','required|is_natural',[
			'is_natural' => 'Bobot Hanya Berisi Angka!'
		]);
		$this->form_validation->set_rules('bcf','Bobot Core Factor','required|trim|is_natural|less_than[100]',[
			'is_natural' => 'Bobot Core Factor Hanya Berisi Angka!',
			'less_than' => 'Maksimal Angka 100'
		]);
		$this->form_validation->set_rules('bsf','Bobot Secondary Factor','required|trim|is_natural|less_than[100]',[
			'is_natural' => 'Bobot Secondary Factor Hanya Berisi Angka!',
			'less_than' => 'Maksimal Angka 100'
		]);

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Aspek Penilaian";
			$data['page'] = "Add Aspek Penilaian";
			$this->template->load('admin/layout/template','admin/modul_aspek/aspekAdd',$data);

		}else{

			$total = array($this->input->post('bcf'),$this->input->post('bsf'));
			if(array_sum($total) <> 100){
				$this->session->set_flashdata('message','Jumlah Bobot Core Factor dan Bobot Secondary Factor harus 100');
				$data['title'] = "Profile Matching";
				$data['parent'] = "Data";
				$data['page'] = "Add Aspek Penilaian";
				$this->template->load('admin/layout/template','admin/modul_aspek/aspekAdd',$data);

			}else{

				$data = [

					'kode_aspek' => $this->db->escape_str($this->input->post('kode_aspek'),true),
					'nama_aspek' =>$this->db->escape_str($this->input->post('nama_aspek'),true),
					'bobot' => $this->db->escape_str($this->input->post('bobot'), true),
					'bobot_cf' => $this->db->escape_str($this->input->post('bcf'),true),
					'bobot_sf' => $this->db->escape_str($this->input->post('bsf'),true)
				];

				$this->db->insert('aspek',$data);
				$this->session->set_flashdata('success','Aspek Penilaian "'.$this->input->post('nama_aspek').'" Berhasil Di Tambahkan');
				redirect('admin/aspek');
			}

		}

	}

	public function aspekEdit($kode_aspek = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['oneaspek'] = $this->admin_model->getOneAspek($this->encrypt->decode($kode_aspek));

		/*-- Encrypt URL NIM --*/
		if (count($this->uri->segment_array()) > 3) {
			$this->session->set_flashdata('message','URL yang anda masukkan salah');
			redirect('admin/aspek');
		}
		if (!isset($kode_aspek)) {
			$this->session->set_flashdata('message','Data yang Anda Inginkan Tidak Mempunyai Kode');
			redirect('admin/aspek');
		}
		if (is_numeric($kode_aspek)) {
			$this->session->set_flashdata('message','Url Hanya Bisa Diakses Setelah Terenkripsi');
			redirect('admin/aspek');
		} 

		//id_administrator masih salah
		// if(!$oneadministrator->id_administrator == $this->encrypt->decode($id_administrator)){
		// 	$this->session->set_flashdata('message','NIK Yang Diminta Tidak Sama');
		// 	redirect('data/dataAdministrator');
		// }

		$this->form_validation->set_rules('nama_aspek','Nama Aspek','required');
		$this->form_validation->set_rules('bobot','Bobot','required|is_natural',[
			'is_natural' => 'Bobot Hanya Berisi Angka!'
		]);
		$this->form_validation->set_rules('bcf','Bobot Core Factor','required|trim|is_natural|less_than[100]',[
			'is_natural' => 'Bobot Core Factor Hanya Berisi Angka!',
			'less_than' => 'Maksimal Angka 100'
		]);
		$this->form_validation->set_rules('bsf','Bobot Secondary Factor','required|trim|is_natural|less_than[100]',[
			'is_natural' => 'Bobot Secondary Factor Hanya Berisi Angka!',
			'less_than' => 'Maksimal Angka 100'
		]);

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Aspek Penilaian";
			$data['page'] = "Edit Aspek Penilaian";
			$this->template->load('admin/layout/template','admin/modul_aspek/aspekEdit',$data);

		}else{

			$total = array($this->input->post('bcf'),$this->input->post('bsf'));
			if(array_sum($total) <> 100){
				$this->session->set_flashdata('message','Jumlah Bobot Core Factor dan Bobot Secondary Factor harus 100');
				$data['title'] = "Profile Matching";
				$data['parent'] = "Data";
				$data['page'] = "Edit Aspek Penilaian";
				$this->template->load('admin/layout/template','admin/modul_aspek/aspekEdit',$data);

			}else{

				$data = [

					'kode_aspek' => $this->db->escape_str($this->input->post('kode_aspek'),true),
					'nama_aspek' =>$this->db->escape_str($this->input->post('nama_aspek'),true),
					'bobot' => $this->db->escape_str($this->input->post('bobot'), true),
					'bobot_cf' => $this->db->escape_str($this->input->post('bcf'),true),
					'bobot_sf' => $this->db->escape_str($this->input->post('bsf'),true)

				];

				$this->db->where('kode_aspek', $this->input->post('zz'));
				$this->db->update('aspek',$data);
				$this->session->set_flashdata('success','Aspek Penilaian "'.$this->input->post('nama_aspek').'" Berhasil Di Update');
				redirect('admin/aspek');
			}
		}
	}

	public function aspekDelete($kode_aspek){

		$this->db->delete('aspek',['kode_aspek' => $this->encrypt->decode($kode_aspek)]);
		$this->session->set_flashdata('success','Aspek Penilaian Yang Anda Pilih Berhasil Di Hapus');
		redirect('admin/aspek');

	}


	public function faktor(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['faktor'] = $this->admin_model->getFaktor();

		$data['title'] = "Profile Matching";
		$data['parent'] = "Data";
		$data['page'] = "Faktor Penilaian";
		$this->template->load('admin/layout/template','admin/modul_faktor/faktor',$data);

	}


	public function faktorAdd(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['aspek'] = $this->admin_model->getAspek();

		$this->form_validation->set_rules('kode_aspek','Kode Aspek','required');
		$this->form_validation->set_rules('nama_faktor','Nama Faktor Penilaian','required');
		$this->form_validation->set_rules('jenis_faktor','Jenis Faktor Penilaian','required');
		$this->form_validation->set_rules('nilai_target','Nilai Target','required');

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Faktor Penilaian";
			$data['page'] = "Add Faktor Penilaian";
			$this->template->load('admin/layout/template','admin/modul_faktor/faktorAdd',$data);

		}else{

			$data = [

				'kode_faktor' => $this->db->escape_str($this->input->post('kode_faktor'),true),
				'kode_aspek' =>$this->db->escape_str($this->input->post('kode_aspek'),true),
				'nama_faktor' => $this->db->escape_str(ucfirst($this->input->post('nama_faktor')),true),
				'jenis_faktor' => $this->db->escape_str($this->input->post('jenis_faktor'),true),
				'nilai_target' => $this->db->escape_str($this->input->post('nilai_target'),true)

			];

			$this->db->insert('faktor',$data);
			$this->session->set_flashdata('success','Faktor Penilaian "'.$this->input->post('nama_faktor').'" Berhasil Di Tambahkan');
			redirect('admin/faktor');

		}
	}

	public function faktorEdit($kode_faktor = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['aspek'] = $this->admin_model->getAspek();
		$data['onefaktor'] = $this->admin_model->getOneFaktor($this->encrypt->decode($kode_faktor));

		/*-- Encrypt URL NIM --*/
		if (count($this->uri->segment_array()) > 3) {
			$this->session->set_flashdata('message','URL yang anda masukkan salah');
			redirect('admin/faktor');
		}
		if (!isset($kode_faktor)) {
			$this->session->set_flashdata('message','Data yang Anda Inginkan Tidak Mempunyai Kode');
			redirect('admin/faktor');
		}
		if (is_numeric($kode_faktor)) {
			$this->session->set_flashdata('message','Url Hanya Bisa Diakses Setelah Terenkripsi');
			redirect('admin/faktor');
		} 

		//id_administrator masih salah
		// if(!$oneadministrator->id_administrator == $this->encrypt->decode($id_administrator)){
		// 	$this->session->set_flashdata('message','NIK Yang Diminta Tidak Sama');
		// 	redirect('data/dataAdministrator');
		// }

		$this->form_validation->set_rules('kode_aspek','Kode Aspek','required');
		$this->form_validation->set_rules('nama_faktor','Nama Faktor Penilaian','required');
		$this->form_validation->set_rules('jenis_faktor','Jenis Faktor Penilaian','required');
		$this->form_validation->set_rules('nilai_target','Nilai Target','required|trim|is_natural',[
			'is_natural' => 'Nilai Target Hanya Berisi Angka!',
		]);

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Faktor Penilaian";
			$data['page'] = "Edit Faktor Penilaian";
			$this->template->load('admin/layout/template','admin/modul_faktor/faktorEdit',$data);

		}else{

			$data = [

				'kode_faktor' => $this->db->escape_str($this->input->post('kode_faktor'),true),
				'kode_aspek' =>$this->db->escape_str($this->input->post('kode_aspek'),true),
				'nama_faktor' => $this->db->escape_str(ucfirst($this->input->post('nama_faktor')),true),
				'jenis_faktor' => $this->db->escape_str($this->input->post('jenis_faktor'),true),
				'nilai_target' => $this->db->escape_str($this->input->post('nilai_target'),true)

			];

			$this->db->where('kode_faktor', $this->input->post('zz'));
			$this->db->update('faktor',$data);
			$this->session->set_flashdata('success','Faktor Penilaian "'.$this->input->post('nama_faktor').'" Berhasil Di Update');
			redirect('admin/faktor');

		}

	}

	public function faktorDelete($kode_faktor){

		$this->db->delete('faktor',['kode_faktor' => $this->encrypt->decode($kode_faktor)]);
		$this->db->delete('detail_calon', ['kode_faktor' => $this->encrypt->decode($kode_faktor)]);
		$this->session->set_flashdata('success','Faktor Penilaian Yang Anda Pilih Berhasil Di Hapus');
		redirect('admin/faktor');

	}


	public function kandidat(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['aspek'] = $this->admin_model->getAspek();
		$data['faktor'] = $this->admin_model->getFaktor();
		$data['kandidat'] = $this->admin_model->getKandidat();
		$data['detail'] = $this->admin_model->getDetailKandidat();

		$data['administrator'] = $this->admin_model->getAdministrator();
		$data['title'] = "Profile Matching";
		$data['parent'] = "Kandidat";
		$data['page'] = "Kandidat Calon Penerima Zakat";
		$this->template->load('admin/layout/template','admin/modul_kandidat/kandidat',$data);

	}


	public function kandidatAdd(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['pegawai'] = $this->admin_model->getPegawai();
		$data['faktor'] = $this->admin_model->getFaktor();
		$data['skala'] = array(1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik') ;

		$faktor = $this->admin_model->getFaktor();

		$this->form_validation->set_rules('nik','Nama Pegawai','required');

		$data['pegawai'] = $this->admin_model->getPegawai();


		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Kandidat";
			$data['page'] = "Add Kandidat Calon Penerima Zakat";
			$this->template->load('admin/layout/template','admin/modul_kandidat/kandidatAdd',$data);

		}else{
			$kode = $this->admin_model->Codekandidat();
			$this->db->insert('kandidat',[
				'nik' => $this->input->post('nik'),
				'id_kandidat'=>$kode,
			]);
			// $id_kandidat = $this->db->insert_id();
			foreach ($this->input->post('faktor') as $key => $value) {
				$data = [
					'id_detail' =>  $this->admin_model->Codedetail(),
					'id_kandidat' => $kode,
					'kode_faktor' => $key,
					'nilai_faktor' => $value

				];

				$this->db->insert('detail_calon', $data);
			}

			$this->session->set_flashdata('success','Kandidat Calon "'.$this->input->post('nik').'" Berhasil Di Tambahkan');
			redirect('admin/kandidat');

		}
	}

	public function kandidatEdit($id_kandidat = null){

		/*-- Encrypt URL NIM --*/
		if (count($this->uri->segment_array()) > 3) {
			$this->session->set_flashdata('message','URL yang anda masukkan salah');
			redirect('admin/kandidat');
		}
		if (!isset($id_kandidat)) {
			$this->session->set_flashdata('message','Data yang Anda Inginkan Tidak Mempunyai ID');
			redirect('admin/kandidat');
		}
		if (is_numeric($id_kandidat)) {
			$this->session->set_flashdata('message','Url Hanya Bisa Diakses Setelah Terenkripsi');
			redirect('admin/kandidat');
		} 


		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['onekandidat'] = $this->admin_model->getOneKandidat($this->encrypt->decode($id_kandidat));
		$data['pegawai'] = $this->admin_model->getPegawai();
		$data['faktor'] = $this->admin_model->getFaktor();
		$data['skala'] = array(1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik') ;

		$this->form_validation->set_rules('nik','Nama Pegawai','required');

		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "Kandidat";
			$data['page'] = "Edit Kandidat  Calon Penerima Zakat";
			$this->template->load('admin/layout/template','admin/modul_kandidat/kandidatEdit',$data);

		}else{


			$this->db->where('id_kandidat', $id_kandidat);
			$sql = $this->db->update('kandidat',['nik' => $this->input->post('nik')]);


			foreach ($this->input->post('faktor') as $key => $value) {


				$this->db->where('kode_faktor', $key);
				$this->db->where('id_kandidat', $this->input->post('zz'));
				$this->db->update('detail_calon',['nilai_faktor' => $value]);
			}

			$this->session->set_flashdata('success','Kandidat Calon "'.$this->input->post('nik').'" Berhasil Di Update');
			redirect('admin/kandidat');

		}

	}


	public function kandidatDelete($id_kandidat){

		$this->db->delete('kandidat',['id_kandidat' => $this->encrypt->decode($id_kandidat)]);
		$this->db->delete('detail_calon',['id_kandidat' => $this->encrypt->decode($id_kandidat)]);
		$this->session->set_flashdata('success','Data Administrator Yang Anda Pilih Berhasil Di Hapus');
		redirect('admin/kandidat');

	}

	public function manual(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['aspek'] = $this->admin_model->getAspek();
		$data['faktor'] = $this->admin_model->getFaktor();
		$data['kandidat'] = $this->admin_model->getKandidat();

		$data['pembobotan'] = array(0 => 5, 1 => 4.5, -1 => 4, 2 => 3.5, -2 => 3, 3 => 2.5, -3 => 2, 4 => 1.5, -4 => 1);

		$data['administrator'] = $this->admin_model->getAdministrator();
		$data['title'] = "Profile Matching";
		$data['parent'] = "Manual";
		$data['page'] = "Manual Pehitungan";
		$data['kandidathasil'] = $this->admin_model->getKandidatHasil();
		$this->template->load('admin/layout/template','admin/modul_manual/manual',$data);

	}

	public function updateNilai($id_kandidat){

		$this->db->where('id_kandidat', $id_kandidat);
		$this->db->update('kandidat',['nilai_akhir' => $this->input->post('nilai_akhir')]);
		$this->session->set_flashdata('success','Nilai Akhir Berhasil Di Update');
		redirect('admin/manual');

	}


	public function hasil(){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();
		$data['pembobotan'] = array(0 => 5, 1 => 4.5, -1 => 4, 2 => 3.5, -2 => 3, 3 => 2.5, -3 => 2, 4 => 1.5, -4 => 1);


		$data['kandidathasil'] = $this->admin_model->getKandidatHasil();
		$data['allmasuk'] = $this->admin_model->getAllDiTerima();
		$data['title'] = "Profile Matching";
		$data['parent'] = "PM";
		$data['page'] = "Hasil Perhitungan";
		$this->template->load('admin/layout/template','admin/modul_hasil/hasil',$data);

	}

	public function pekerjaTerima($nik = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

		$data['kandidathasil'] = $this->admin_model->getKandidatHasil();
		$data['allmasuk'] = $this->admin_model->getAllDiTerima();

		$this->form_validation->set_rules('zz','NIK Kandidat','required');


		if($this->form_validation->run() == false){

			$data['title'] = "Profile Matching";
			$data['parent'] = "PM";
			$data['page'] = "Hasil Perhitungan";
			$this->template->load('admin/layout/template','admin/modul_hasil/hasil',$data);

		}else{

			$nikkk = $this->input->post('zz');
			$this->db->query("
				INSERT INTO penerima(nik,nama_penerima,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat,pendidikan)
				SELECT nik,nama_penerima,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat,pendidikan 
				FROM calon_penerima WHERE nik = '$nikkk' 
				");

			// $id_pekerja =  $this->admin_model->CodePenerima();

			$query = "SELECT * FROM detail_calon JOIN kandidat ON detail_calon.id_kandidat = kandidat.id_kandidat WHERE kandidat.nik = '$nikkk' ";

			$kode = array();
			$nilai = array();

			foreach ($this->db->query($query)->result() as $key) {
				$kode[] = $key->kode_faktor;
				$nilai[] = $key->nilai_faktor;
			}

			// $faktor = array(
			// 	'kode_faktor' => $kode,
			// 	//'nilai_faktor' => $nilai
			// );

			$json_format_kode = json_encode($kode);
			$json_format_nilai = json_encode($nilai);
			//var_dump($json_format);
			// die();
			$nilai_akhir = $this->db->query($query)->row()->nilai_akhir;
			$data = [

				'tgl_diterima' => date('Y-m-d'),
				'faktor' => $json_format_kode,
				'nilai_faktor' => $json_format_nilai,
				'nilai_akhir' => $nilai_akhir,
				'kandidat_terima' => 1
			];
			// var_dump($data);
			$kandidat_id = $this->db->query($query)->row()->id_kandidat;
			// var_dump($kandidat_id);
			// die();
			$this->db->where('nik', $nikkk);
			$this->db->update('penerima', $data);

			$this->db->delete('detail_calon',['id_kandidat' => $kandidat_id]);
			$this->db->delete('kandidat',['id_kandidat' => $kandidat_id]);

			$this->session->set_flashdata('success','Pegawai Yang Anda Pilih Telah Diterima Dengan ID '.$id_pekerja.' ok');
			redirect('admin/hasil');
		}

	}

	public function zakatTerimaDetail($id_pekerja = null){

		$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();
		
		$data['onepekerja'] = $this->admin_model->getOneDiTerima($id_pekerja);
		$data['faktor'] = $this->admin_model->getFaktor();
		$data['skala'] = array(1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik') ;
		$data['title'] = "Profile Matching";
		$data['parent'] = "Pekerja";
		$data['page'] = "Detail Penerima Zakat";
		$this->template->load('admin/layout/template','admin/modul_hasil/hasilDetail',$data);
	}

	// public function pekerjakembalikan(){

	// 	$data['user'] = $this->db->get_where('administrator',['username' => $this->session->userdata('username')])->row();

	// 	$data['kandidathasil'] = $this->admin_model->getKandidatHasil();

	// 	$data['title'] = "Profile Matching";
	// 	$data['parent'] = "PM";
	// 	$data['page'] = "Hasil Perhitungan";
	// 	$this->template->load('admin/layout/template','admin/modul_hasil/hasil',$data);

	// }

	public function pekerjadelete($id_penerima = null){

		$this->db->delete('penerima',['id_penerima' => $this->encrypt->decode( $id_penerima)]);
		$this->session->set_flashdata('success','Data penerima Yang anda pilih berhasil dihapus');
		redirect('admin/hasil');

	}

	// eksport pdf
	
	public function exportPDF(){
		error_reporting(0); // AGAR ERROR MASALAH VERSI PHP TIDAK MUNCUL
        $pdf = new FPDF('L', 'mm','Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
		//Image( file name , x position , y position , width [optional] , height [optional] )
		$pdf->Image(base_url('assets/lazismu.png'),240,2,25);
        $pdf->Cell(0,7,'DAFTAR PENERIMA ZAKAT',0,1,'C');
        $pdf->Cell(10,8,'',0,1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,'No',1,0,'C');
		$pdf->Cell(50,6,'NIK',1,0,'C');
        $pdf->Cell(60,6,'Nama Penerima',1,0,'C');
        $pdf->Cell(40,6,'Jenis Kelamin',1,0,'C');
		$pdf->Cell(40,6,'Nilai Akhir',1,0,'C');
		$pdf->Cell(20,6,'Rangking',1,0,'C');
        $pdf->Cell(40,6,'Keterangan',1,1,'C');
        $pdf->SetFont('Arial','',10);
        $pegawai = $this->admin_model->getAllDiTerima();
        $no=0;
		$jk='';
		$ktr='';
        foreach ($pegawai as $data){
			if($data->jenis_kelamin == 'L'){
				$jk = "Laki-laki";
			}else{
				$jk = "Perempuan";
			}

			if($data->kandidat_terima == '1'){
				$ktr = "Diterima";
			}else{
				$ktr = "Ditolak";
			}
            $no++;
            $pdf->Cell(10,6,$no,1,0, 'C');
            $pdf->Cell(50,6,$data->nik,1,0);
            $pdf->Cell(60,6,$data->alamat,1,0);
			$pdf->Cell(40,6,$jk,1,0);
			$pdf->Cell(40,6,$data->nilai_akhir,1,0,'C');
			$pdf->Cell(20,6,$data->my_rank,1,0,'C');
            $pdf->Cell(40,6,$ktr,1,1,'C');
        }
		date_default_timezone_set('Asia/Jakarta');// change according timezone
		$currentTime = date( 'd F Y');
		$date=date("Y-m-d");
		$datehijriah= $this->hijriyah->konvhijriah($date);
		//$datehijriah=konvhijriah($currentTime);
		$pdf->Cell(10,7,'',0,1);
		$pdf->SetFont('Arial','U');
		$pdf->Cell(0,7,'Pekanbaru, '.$currentTime.' M',0,1,'R');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,1,'Pekanbaru, '.$datehijriah,0,1,'R');
		$pdf->Cell(10,7,'',0,1);
		$pdf->Cell(10,7,'',0,1);
		$pdf->Cell(0,7,'Lazismu Riau',0,1,'R');
        $pdf->Output('PenerimaZakat.pdf','D');
		//$pdf->Output();
	}

}