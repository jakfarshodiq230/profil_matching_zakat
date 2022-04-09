<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {


	public function getCountCalonPegawai(){

		$query = "SELECT COUNT(nik) as calon_penerima FROM calon_penerima";
		return $this->db->query($query)->row()->calon_penerima;

	}

	public function getCountAspekPenilaian(){

		$query = "SELECT COUNT(kode_aspek) as aspek FROM aspek";
		return $this->db->query($query)->row()->aspek;

	}

	public function getCountFaktorPenilaian(){

		$query = "SELECT COUNT(kode_faktor) as faktor FROM faktor";
		return $this->db->query($query)->row()->faktor;

	}

	public function getCountKandidat(){

		$query = "SELECT COUNT(id_kandidat) as kandidat FROM kandidat";
		return $this->db->query($query)->row()->kandidat;

	}


	public function getAdministrator(){

		$query = "SELECT * FROM administrator ORDER BY id_administrator ASC";
		return $this->db->query($query)->result();
	}

	public function getOneAdministrator($id_administrator){

		$query = "SELECT * FROM administrator WHERE id_administrator LIKE '$id_administrator' ";
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

		$query = "SELECT a.*, b.nama_penerima FROM kandidat as a JOIN calon_penerima as b ON a.nik = b.nik ORDER BY a.id_kandidat ASC";
		return $this->db->query($query)->result();
	}

	public function getOneKandidat($id_kandidat){

		$query = "SELECT a.*, b.nama_penerima FROM kandidat as a JOIN calon_penerima as b ON a.nik = b.nik WHERE a.id_kandidat = '$id_kandidat'";
		return $this->db->query($query)->row();
	}

	public function getDetailkandidat(){
		$query = "SELECT * FROM detail_calon ORDER BY kode_faktor ASC";
		return $this->db->query($query)->result();
	}

	public function getOneDetailkandidat($id_kandidat){
		$query = "SELECT * FROM detail_calon WHERE id_kandidat = '$id_kandidat'";
		return $this->db->query($query)->row();
	}

	public function getKandidatHAsil(){

		$query = "SELECT s.*, k.id_kandidat, k.nilai_akhir FROM calon_penerima as s JOIN kandidat as k ON k.nik = s.nik";
		return $this->db->query($query)->result();
	}

	public function getAllDataOneKandidat($nik){
		$query = "SELECT * FROM kandidat as k  JOIN detail_calon as dk ON dk.id_kandidat = k.id_kandidat WHERE k.nik = '$nik'";
		return $this->db->query($query)->result();
	}

	public function getAllDiTerima(){
		$query = "SELECT *, RANK() OVER ( ORDER BY nilai_akhir DESC ) my_rank FROM penerima;";
		return $this->db->query($query)->result();
	}

	public function getOneDiTerima($id){
		$query = "SELECT * FROM penerima WHERE id_penerima = '$id'";
		return $this->db->query($query)->row();
	}

	public function Codekandidat(){
		$this->db->select('MAX(kandidat.id_kandidat) as kode_kandidat', FALSE);
		$this->db->order_by('kode_kandidat','DESC');    
		$this->db->limit(1);    
		$query = $this->db->get('kandidat');
			if($query->num_rows() <> 0){      
				 $data = $query->row();
				 $kode = intval($data->kode_kandidat) + 1; 
			}
			else{      
				 $kode = 1;  
			}
		$kodetampil = $kode;   
		return $kodetampil;  
	}

	public function Codedetail(){
		$this->db->select('MAX(detail_calon.id_detail) as kode_detail', FALSE);
		$this->db->order_by('kode_detail','DESC');    
		$this->db->limit(1);    
		$query = $this->db->get('detail_calon');
			if($query->num_rows() <> 0){      
				 $data = $query->row();
				 $kode = intval($data->kode_detail) + 1; 
			}
			else{      
				 $kode = 1;  
			}
		$kodetampil = $kode;   
		return $kodetampil;  
	}

	public function CodePenerima(){
		$this->db->select('MAX(penerima.id_penerima) as kode_penerima', FALSE);
		$this->db->order_by('kode_penerima','DESC');    
		$this->db->limit(1);    
		$query = $this->db->get('penerima');
			if($query->num_rows() <> 0){      
				 $data = $query->row();
				 $kode = intval($data->kode_kandidat) + 1; 
			}
			else{      
				 $kode = 1;  
			}
		$kode_penerima = $kode;   
		return $kode_penerima;  
	}

}