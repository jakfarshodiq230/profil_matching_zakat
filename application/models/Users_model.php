<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

	public function getProfile($username){
		$query = "SELECT * FROM administrator WHERE username = '$username'";
		return $this->db->query($query)->row();
	}

	public function getPegawai(){

		$query = "SELECT * FROM calon_penerima ORDER BY nik ASC";
		return $this->db->query($query)->result();
	}

	public function getOnePegawai($nik){

		$query = "SELECT * FROM calon_penerima WHERE nik LIKE '$nik' ";
		return $this->db->query($query)->row();

	}

	public function getAspek(){

		$query = "SELECT * FROM aspek ORDER BY kode_aspek ASC";
		return $this->db->query($query)->result();
	}

	public function getOneAspek($kode_aspek){

		$query = "SELECT * FROM aspek WHERE kode_aspek LIKE '$kode_aspek' ";
		return $this->db->query($query)->row();

	}

	public function getFaktor(){

		$query = "SELECT * FROM faktor ORDER BY kode_faktor ASC";
		return $this->db->query($query)->result();
	}

	public function getOneFaktor($kode_faktor){

		$query = "SELECT * FROM faktor WHERE kode_faktor LIKE '$kode_faktor' ";
		return $this->db->query($query)->row();

	}

	public function getKandidat(){

		$query = "SELECT a.*, b.nama_pegawai FROM kandidat as a JOIN calon_penerima as b ON a.nik = b.nik ORDER BY a.id_kandidat ASC";
		return $this->db->query($query)->result();
	}

	public function getOneKandidat($id_kandidat){

		$query = "SELECT a.*, b.nama_pegawai FROM kandidat as a JOIN calon_penerima as b ON a.nik = b.nik WHERE a.id_kandidat = '$id_kandidat'";
		return $this->db->query($query)->row();
	}

	public function getDetailkandidat(){
		$query = "SELECT * FROM detail_calon ORDER BY kode_faktor ASC";
		return $this->db->query($query)->result();
	}

	public function getAllHasil(){

		$query = "SELECT s.*, k.nilai_akhir FROM calon_penerima as s JOIN kandidat as k ON k.nik = s.nik";
		return $this->db->query($query)->result();
	}

	public function getAllDiTerima(){
		$query = "SELECT * FROM penerima";
		return $this->db->query($query)->result();
	}
	public function getOneDiTerima($id){
		$query = "SELECT * FROM penerima WHERE id_pekerja = '$id'";
		return $this->db->query($query)->row();
	}


}