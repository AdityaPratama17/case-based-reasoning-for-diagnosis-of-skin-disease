<?php

class COBA_model{
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
    
    public function getAllDataTrain(){
		$this->db->query('SELECT * FROM case_base');
		return $this->db->resultSet();
	}

	public function getAllDataTest(){
		$this->db->query('SELECT * FROM test_30');
		return $this->db->resultSet();
	}

    public function getJumlahKelasPerFitur($kelas,$fitur,$nilai){
		$this->db->query('SELECT * FROM case_base WHERE class=:kelas AND `'.$fitur.'`=:nilai');
        $this->db->bind('kelas',$kelas);
        $this->db->bind('nilai',$nilai);
		return $this->db->resultSet();
	}

    public function getDataTrain($id){
		$this->db->query('SELECT * FROM case_base WHERE id_cb=:id');
		$this->db->bind('id',$id);
		return $this->db->single();
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

    // public function getAllAturan(){
	// 	$this->db->query('SELECT * FROM rules r
    //     INNER JOIN kerusakan USING(id_kerusakan)
    //     INNER JOIN gejala USING (id_gejala)
    //     ORDER BY r.id_rule, r.id_kerusakan ASC');
        
	// 	return $this->db->resultSet();
	// }

    // public function getAllRiwayat($id){
	// 	$this->db->query('SELECT * FROM riwayat WHERE id_user=:id');
    //     $this->db->bind('id',$id);
	// 	return $this->db->resultSet();
	// }

    // public function tambahAturan($data){
	// 	$query = "INSERT INTO rules VALUES ('',:gejala,:kerusakan)";

	// 	$this->db->query($query);
	// 	$this->db->bind('gejala', $data['gejala']);
	// 	$this->db->bind('kerusakan', $data['kerusakan']);
	// 	$this->db->execute();

	// 	return $this->db->rowCount();
	// }

    // public function getAturanById($id){
	// 	$this->db->query('SELECT * FROM rules WHERE id_rule=:id');
	// 	$this->db->bind('id',$id);
	// 	return $this->db->single();
	// }

    // public function ubahDataAturan($data){
	// 	$query = "UPDATE rules SET id_gejala = :gejala, id_kerusakan = :kerusakan WHERE id_rule = :id";

	// 	$this->db->query($query);
	// 	$this->db->bind('gejala', $data['gejala']);
	// 	$this->db->bind('kerusakan', $data['kerusakan']);
	// 	$this->db->bind('id', $data['id']);
	// 	$this->db->execute();

	// 	return $this->db->rowCount();
	// }

    // public function hapusAturan($id){
	// 	$query = "DELETE FROM rules WHERE id_rule = :id";

	// 	$this->db->query($query);
	// 	$this->db->bind('id', $id);
	// 	$this->db->execute();
		
	// 	return $this->db->rowCount();
	// }
}