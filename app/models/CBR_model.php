<?php

class CBR_model{
    private $db;

    public function __construct(){
		$this->db = new Database;
	}

    public function getAllCase(){
		$this->db->query('SELECT * FROM case_base');
		return $this->db->resultSet();
	}

    public function getDataTest($id){
		$this->db->query('SELECT * FROM case_base WHERE id_cb=:id');
		$this->db->bind('id',$id);
		return $this->db->single();
	}

    public function getJumlahKelas($kelas){
		$this->db->query('SELECT * FROM case_base WHERE class=:kelas');
        $this->db->bind('kelas',$kelas);
		return $this->db->resultSet();
	}

    public function getJumlahKelasPerFitur($kelas,$fitur,$nilai){
		$this->db->query('SELECT * FROM case_base WHERE class=:kelas AND `'.$fitur.'`=:nilai');
        $this->db->bind('kelas',$kelas);
        $this->db->bind('nilai',$nilai);
		return $this->db->resultSet();
	}

    public function getAllBobot(){
		$this->db->query('SELECT * FROM bobot');
		return $this->db->resultSet();
	}

    public function getMaxMinData(){
		$this->db->query('SELECT MAX(`34`) AS maxx, MIN(`34`) AS minn FROM case_base');
        return $this->db->single();
	}

    public function getBobotByFitur($fitur){
		$this->db->query('SELECT * FROM bobot WHERE id_bobot=:fitur');
        $this->db->bind('fitur',$fitur);
        return $this->db->single();
	}

    public function retain($data,$kelas){
		$query = "INSERT INTO case_base VALUES ('',".$data[1].",".$data[2].",".$data[3].",".$data[4].",".$data[5].",".$data[6].",".$data[7].",".$data[8].",".$data[9].",".$data[10].",".$data[11].",".$data[12].",".$data[13].",".$data[14].",".$data[15].",".$data[16].",".$data[17].",".$data[18].",".$data[19].",".$data[20].",".$data[21].",".$data[22].",".$data[23].",".$data[24].",".$data[25].",".$data[26].",".$data[27].",".$data[28].",".$data[29].",".$data[30].",".$data[31].",".$data[32].",".$data[33].",".$data[34].",".$kelas.")";

		$this->db->query($query);
		$this->db->execute();
		return $this->db->rowCount();
	}

    public function tambahRevise($data,$kelas,$sim){
		$query = "INSERT INTO revise VALUES ('',".$data[1].",".$data[2].",".$data[3].",".$data[4].",".$data[5].",".$data[6].",".$data[7].",".$data[8].",".$data[9].",".$data[10].",".$data[11].",".$data[12].",".$data[13].",".$data[14].",".$data[15].",".$data[16].",".$data[17].",".$data[18].",".$data[19].",".$data[20].",".$data[21].",".$data[22].",".$data[23].",".$data[24].",".$data[25].",".$data[26].",".$data[27].",".$data[28].",".$data[29].",".$data[30].",".$data[31].",".$data[32].",".$data[33].",".$data[34].",".$kelas.",".$sim.")";

		$this->db->query($query);
		$this->db->execute();
		// return $this->db->rowCount();
	}

	public function getAllKasusRevise(){
		$this->db->query('SELECT * FROM revise');
		return $this->db->resultSet();
	}

	public function getKasusReviseById($id){
		$this->db->query('SELECT * FROM revise WHERE id_cb=:id');
		// $this->db->query('SELECT * FROM revise r INNER JOIN penyakit p ON r.`35`=p.`id_penyakit` WHERE r.id_cb=:id');
		$this->db->bind('id',$id);
		return $this->db->single();
	}

	public function hapusKasus($id){
		$query = "DELETE FROM revise WHERE id_cb = :id";

		$this->db->query($query);
		$this->db->bind('id',$id);
		$this->db->execute();
		// return $this->db->rowCount();
	}

	public function WriteCase($case){
		$query = "INSERT INTO cb_kfcv VALUES ('',".$case[1].",".$case[2].",".$case[3].",".$case[4].",".$case[5].",".$case[6].",".$case[7].",".$case[8].",".$case[9].",".$case[10].",".$case[11].",".$case[12].",".$case[13].",".$case[14].",".$case[15].",".$case[16].",".$case[17].",".$case[18].",".$case[19].",".$case[20].",".$case[21].",".$case[22].",".$case[23].",".$case[24].",".$case[25].",".$case[26].",".$case[27].",".$case[28].",".$case[29].",".$case[30].",".$case[31].",".$case[32].",".$case[33].",".$case[34].",".$case['class'].")";

		$this->db->query($query);
		$this->db->execute();
		// return $this->db->rowCount();
	}

	public function getAllCaseKFCV(){
		$this->db->query('SELECT * FROM cb_kfcv');
		return $this->db->resultSet();
	}

	public function WriteCaseSplit($case){
		$query = "INSERT INTO split_cb VALUES ('',".$case[1].",".$case[2].",".$case[3].",".$case[4].",".$case[5].",".$case[6].",".$case[7].",".$case[8].",".$case[9].",".$case[10].",".$case[11].",".$case[12].",".$case[13].",".$case[14].",".$case[15].",".$case[16].",".$case[17].",".$case[18].",".$case[19].",".$case[20].",".$case[21].",".$case[22].",".$case[23].",".$case[24].",".$case[25].",".$case[26].",".$case[27].",".$case[28].",".$case[29].",".$case[30].",".$case[31].",".$case[32].",".$case[33].",".$case[34].",".$case[35].")";

		$this->db->query($query);
		$this->db->execute();
		// return $this->db->rowCount();
	}

	public function getAllCaseSplit(){
		$this->db->query('SELECT * FROM split_cb');
		return $this->db->resultSet();
	}
}