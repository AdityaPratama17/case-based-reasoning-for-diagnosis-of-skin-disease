<?php 

class REVISE extends Controller{
    public function index(){
        $data['judul']='Revise';
        $data['kasus'] = $this->model('CBR_model')->getAllKasusRevise();
        $data['penyakit']=['Psoriasis','Seboreic Dermatitis','Lichen Planus','Pityriasis Rosea','Cronic Dermatitis','Pityriasis Rubra Pilaris'];
        $data['fitur'] = $this->model('CBR_model')->getAllBobot();

        // echo '<hr><h1>KASUS BARU</h1><pre>'; print_r($data['kasus']); echo '</pre>';

        $this->view('templates/header', $data);
        $this->view('revise/index', $data);
        $this->view('templates/footer', $data);
    }

    public function getKasus(){
        $data = $this->model('CBR_model')->getKasusReviseById($_POST['id']);
        $bobot = ['Tidak','Cukup Yakin','Yakin','Sangat Yakin','Tidak','Ya'];

        foreach ($data as $key => $value) {
            if ($key!='id_cb' && $key!='sim') {
                if ($key==34) {
                    $data['f'.$key] = $value;
                }elseif ($key==35) {
                    $data['f'.$key] = $value;
                }
                elseif ($key!=11) {
                    $data['f'.$key] = $bobot[$value];
                }else {
                    $data['f'.$key] = $bobot[$value+4];
                }
            }
        }

        // print_r($data); exit;
		echo json_encode($data);
	}

    public function revisiKasus(){
        // echo $_POST['id'];
        // echo $_POST['class']; exit;
        $data = $this->model('CBR_model')->getKasusReviseById($_POST['id']);
		if ($this->model('CBR_model')->retain($data,$_POST['class']) > 0) {
            $this->model('CBR_model')->hapusKasus($_POST['id']);
			Flasher::setFlash('Kasus','berhasil','direvisi','success');
			header('Location:'.BASEURL.'/revise');
			exit;
		}else {
			Flasher::setFlash('Kasus','gagal','direvisi','danger');
			header('Location:'.BASEURL.'/revise');
			exit;
		}
	}


}