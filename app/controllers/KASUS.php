<?php 

class KASUS extends Controller{
    public function index(){
        $data['judul']='Basis Kasus';
        $data['kasus'] = $this->model('CBR_model')->getAllCase();
        $data['penyakit']=['Psoriasis','Seboreic Dermatitis','Lichen Planus','Pityriasis Rosea','Cronic Dermatitis','Pityriasis Rubra Pilaris'];
        $data['bobot']=['Tidak','Cukup Yakin','Yakin','Sangat Yakin'];
        $data['fitur'] = $this->model('CBR_model')->getAllBobot();
        // echo '<hr><h1>KASUS BARU</h1><pre>'; print_r($data['kasus']); echo '</pre>';
        // exit;

        $this->view('kasus/index', $data);
    }

}