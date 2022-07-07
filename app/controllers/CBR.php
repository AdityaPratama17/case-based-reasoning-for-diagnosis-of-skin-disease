<?php 

class CBR extends Controller{
    public function index(){
        $data['judul']='Diagnosis';
        $data['fitur'] = $this->model('CBR_model')->getAllBobot();
        // print_r($data['fitur']);
        // exit;

        $this->view('templates/header', $data);
        $this->view('cbr/index', $data);
        $this->view('templates/footer', $data);
    }

    public function cekPenyakit(){
        // AMBIL KASUS LAMA
        $case = $this->model('CBR_model')->getAllCase();
        $jumTrain = count($case);
        // AMBIL KASUS BARU
        // $newCase = $this->model('CBR_model')->getDataTest($id);
        $newCase = $_POST['newCase'];
        // echo '<hr><h1>KASUS BARU</h1><pre>'; print_r($newCase); echo '</pre>';

        // PERHITUNGAN INDEXING BAYES ------------------------------------------------------------------------
        $jumP_XCi=0;
        for ($i=1; $i <= 6; $i++) { 
            // PERHITUNGAN P(Ci)
            $jumKelas[$i] = count($this->model('CBR_model')->getJumlahKelas($i));
            $P_Ci[$i] = $jumKelas[$i]/$jumTrain;
            // PERHITUNGAN P(X)
            $P_XCi[$i]=$P_Ci[$i];
            foreach (array_keys($newCase) as $fitur) {
                if ($fitur!='id_cb' && $fitur!='class') {
                    $jumFitur = count($this->model('CBR_model')->getJumlahKelasPerFitur($i,$fitur,$newCase[$fitur]));
                    if ($jumFitur==0) $jumFitur=1;
                    $P_X = $jumFitur/$jumKelas[$i];
                    $P_XCi[$i] *= $P_X;
                }
            }
            // PERHITUNGAN JUMLAH P(X|Ci)
            $jumP_XCi += $P_XCi[$i];
        }
        
        // PERHITUNGAN P(Ci|X)
        $indexing = array_fill(1,3,array_fill_keys(array('nilai','kelas'),0));
        for ($i=1; $i <= 6; $i++) { 
            $P_CiX[$i] = $P_XCi[$i]/$jumP_XCi;
            // echo 'P(C'.$i.'|X) = '.$P_XCi[$i].'/'.$jumP_XCi.' = '.$P_CiX[$i].'<br>';
            
            // PROBABILITAS TERBESAR
            if ($P_CiX[$i]>$indexing[1]['nilai']) {
                $indexing[3]=$indexing[2];
                $indexing[2]=$indexing[1];
                $indexing[1]['nilai']=$P_CiX[$i];
                $indexing[1]['kelas']=$i;
            } elseif ($P_CiX[$i]>$indexing[2]['nilai']){
                $indexing[3]=$indexing[2];
                $indexing[2]['nilai']=$P_CiX[$i];
                $indexing[2]['kelas']=$i;
            } elseif ($P_CiX[$i]>$indexing[3]['nilai']) {
                $indexing[3]['nilai']=$P_CiX[$i];
                $indexing[3]['kelas']=$i;
            }
        }
        // echo '<hr><h1>INDEXING</h1><pre>'; print_r($indexing); echo '</pre>';


        // PERHITUNGAN SIMILARITAS ------------------------------------------------------------------------

        // PERHITUNGAN PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('CBR_model')->getAllBobot();
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }

        // INIT KASUS TERPILIH
        // $kasus_terpilih = array_fill(1,3,array_fill_keys(array('id','nilai','kelas'),0));

        // PEHITUNGAN SIMILARITAS
        for ($id_train=0; $id_train < $jumTrain; $id_train++) {
            // AMBIL KASUS LAMA
            $oldCase = $case[$id_train];
            
            // PERHITUNGAN SIMILARITAS BERDASARKAN KELAS YG TERPILIH
            for ($j=1; $j < 4; $j++) { 
                if ($oldCase['class'] == $indexing[$j]['kelas']) {                    
                    // SIMILARITAS LOKAL
                    $pembilang = 0;
                    foreach ($oldCase as $fitur => $oc) {
                        if ($fitur!='id_cb' && $fitur!='class') {
                            if ($fitur=='34') { // atribut numerik
                                $maxmin = $this->model('CBR_model')->getMaxMinData();
                                $sim_local = 1-(abs($oc-$newCase[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                            }else { // atribut nominal
                                if ($oc==$newCase[$fitur]) {
                                    $sim_local = 1;
                                }else {
                                    $sim_local = 0;
                                }
                            }
                            $bobot_id = $this->model('CBR_model')->getBobotByFitur($fitur);
                            // echo '<hr><h1>BOBOT</h1><pre>'; print_r($bobot_id); echo '</pre>';
                            $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                            $pembilang += $sim_local;
                        }
                    }
                    
                    // SIMILARITAS GLOBAL
                    $simMD['nilai'] = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                    $kasus_terpilih[$id_train]['nilai']=$simMD['nilai'];
                    $kasus_terpilih[$id_train]['kelas'] = $oldCase['class'];
                    $kasus_terpilih[$id_train]['id']=$oldCase['id_cb'];
                    // echo 'SimMD('.$oldCase['id_cb'].') = '.$simMD['nilai'].'     kelas = '.$simMD['kelas'].'<br>';                    
                }
            }
        }

        // SORTING HASIL SIMILARITAS
        array_multisort(array_column($kasus_terpilih, 'nilai'), SORT_DESC, $kasus_terpilih);
        // echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($kasus_terpilih); echo '</pre>';
        
        if ($kasus_terpilih[0]['nilai']>=80) {
            // echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($kasus_terpilih[0]); echo '</pre>';
            // echo 'reuse';
            $this->model('CBR_model')->retain($newCase,$kasus_terpilih[0]['kelas']);
        }else {
            // echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($kasus_terpilih[0]); echo '</pre>';
            // echo 'revise';
            $this->model('CBR_model')->tambahRevise($newCase,$kasus_terpilih[0]['kelas'],$kasus_terpilih[0]['nilai']);
        }

        $data['judul']='Hasil Diagnosis';
        $data['nama']= 'Aditya Pratama';
        $data['penyakit']=['Psoriasis','Seboreic Dermatitis','Lichen Planus','Pityriasis Rosea','Cronic Dermatitis','Pityriasis Rubra Pilaris'];
        $data['bobot']=['Tidak','Cukup Yakin','Yakin','Sangat Yakin','Tidak','Ya'];
        $data['fitur'] = $this->model('CBR_model')->getAllBobot();
        $data['newCase']=$newCase;
        $data['kasus_terpilih']=$kasus_terpilih;
        $data['indexing']=$indexing;

        // echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($data['newCase']); echo '</pre>';

        $this->view('templates/header', $data);
        $this->view('cbr/hasil', $data);
        $this->view('templates/footer', $data);
    }

    public function test(){
        // $arr = [
        //     ["nilai"=>35, "kelas"=>"a", "id"=>43],
        //     ["nilai"=>41, "kelas"=>"b", "id"=>44],
        //     ["nilai"=>31, "kelas"=>"b", "id"=>45],
        // ];
        // array_multisort(array_column($arr, 'nilai'), SORT_DESC, $arr);
        // echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($arr); echo '</pre>';
        
        // $kasus_terpilih = [];
        for ($i=0; $i < 3; $i++) { 
            $kasus_terpilih[$i]['nilai'] = $i;
            $kasus_terpilih[$i]['kelas'] = $i;
        }
        echo '<hr><h1>HASIL SIMILARITAS</h1><pre>'; print_r($kasus_terpilih); echo '</pre>';

    }

    public function akurasi(){
        // AMBIL KASUS LAMA
        set_time_limit(500);
        $case = $this->model('CBR_model')->getAllCase();
        // SPLIT DATA TRAIN & TEST
        shuffle($case);
        $jumCase = count($case);
        $persentase = 0.7;
        $jumTrain=round(($persentase*$jumCase),0)+1;
        $jumTest=$jumCase-$jumTrain;
        // echo 'jum train : '.$jumTrain;
        // echo '<br>jum test : '.$jumTest;exit;

        // PERHITUNGAN PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('CBR_model')->getAllBobot();
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }

        echo '<hr><h1>DENGAN INDEXING</h1>';
        $this->IndexingMD($case,$jumTest,$jumTrain,$jumCase,$penyebut);
        echo '<hr><h1>TANPA INDEXING</h1>';
        $this->MD($case,$jumTest,$jumTrain,$jumCase,$penyebut);
    }

    public function IndexingMD($case,$jumTest,$jumTrain,$jumCase,$penyebut){
        // At start of script
        $time_start = microtime(true);
        $akurasi=0;
        $jumEksekusi=0;

        for ($id_test=0; $id_test < $jumTest; $id_test++) {
            $newCase = $case[$id_test];
            // echo '<pre>'; print_r($newCase); echo '</pre>';

            // PERHITUNGAN INDEXING BAYES ------------------------------------------------------------------------
            $jumP_XCi=0;
            for ($i=1; $i <= 6; $i++) { 
                // PERHITUNGAN P(Ci)
                $jumKelas = 0;
                for ($id_train=$jumTest; $id_train < $jumCase; $id_train++) { 
                    if ($case[$id_train]['class']==$i) $jumKelas++;
                }
                $P_Ci = $jumKelas/$jumTrain;
                // PERHITUNGAN P(X)
                $P_XCi[$i]=$P_Ci;
                foreach (array_keys($newCase) as $fitur) {
                    if ($fitur!='id_cb' && $fitur!='class') {
                        $jumFitur=0;
                        for ($id_train=$jumTest; $id_train < $jumCase; $id_train++) { 
                            if ($case[$id_train][$fitur]==$newCase[$fitur] && $case[$id_train]['class']==$i) $jumFitur++;
                        }
                        if ($jumFitur==0) $jumFitur=1;
                        $P_X = $jumFitur/$jumKelas;
                        $P_XCi[$i] *= $P_X;
                    }
                }
                // PERHITUNGAN JUMLAH P(X|Ci)
                $jumP_XCi += $P_XCi[$i];
            }
            
            // PERHITUNGAN P(Ci|X)
            $indexing = array_fill(1,3,array_fill_keys(array('nilai','kelas'),0));
            for ($i=1; $i <= 6; $i++) { 
                $P_CiX[$i] = $P_XCi[$i]/$jumP_XCi;
                
                // PROBABILITAS TERBESAR
                if ($P_CiX[$i]>$indexing[1]['nilai']) {
                    $indexing[3]=$indexing[2];
                    $indexing[2]=$indexing[1];
                    $indexing[1]['nilai']=$P_CiX[$i];
                    $indexing[1]['kelas']=$i;
                } elseif ($P_CiX[$i]>$indexing[2]['nilai']){
                    $indexing[3]=$indexing[2];
                    $indexing[2]['nilai']=$P_CiX[$i];
                    $indexing[2]['kelas']=$i;
                } elseif ($P_CiX[$i]>$indexing[3]['nilai']) {
                    $indexing[3]['nilai']=$P_CiX[$i];
                    $indexing[3]['kelas']=$i;
                }
            }
            // echo '<pre>'; print_r($indexing); echo '</pre>';exit;

            // PERHITUNGAN SIMILARITAS ------------------------------------------------------------------------
            $max=0;
            for ($id_train=$jumTest; $id_train < $jumCase; $id_train++) { 
                // PERHITUNGAN SIMILARITAS BERDASARKAN KELAS YG TERPILIH
                $pembilang = 0;
                for ($j=1; $j < 4; $j++) { 
                    if ($case[$id_train]['class'] == $indexing[$j]['kelas']) {
                        foreach ($case[$id_train] as $fitur => $oldCase) {
                            if ($fitur!='id_cb' && $fitur!='class') {
                                if ($fitur=='34') { // atribut numerik
                                    $maxmin = $this->model('CBR_model')->getMaxMinData();
                                    $sim_local = 1-(abs($oldCase-$newCase[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                                }else { // atribut nominal
                                    if ($oldCase==$newCase[$fitur]) $sim_local = 1;
                                    else $sim_local = 0;
                                }
                                $bobot_id = $this->model('CBR_model')->getBobotByFitur($fitur);
                                $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                                $pembilang += $sim_local;
                            }
                        }
                        $sim_global = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                        if ($max < $sim_global) {
                            $max = $sim_global;
                            $simMD['nilai'] = $sim_global;
                            $simMD['id'] = $case[$id_train]['id_cb'];
                            $simMD['kelas'] = $case[$id_train]['class'];
                        }
                        $jumEksekusi++;
                    }
                }
            }
            if ($case[$id_test]['class']== $simMD['kelas']) {
                $akurasi++;
            }
        }
        echo "benar = $akurasi dari $jumTest<br>";
        $akurasi = round(($akurasi/$jumTest)*100,2);
        echo "akurasi = $akurasi <br>";
        echo "jumlah data diproses = $jumEksekusi <br>";
        // echo '<hr><h1>SIMILARITAS</h1><pre>'; print_r($simMD); echo '</pre>';

        // Anywhere else in the script
        $time_end = microtime(true);
        $execution_time = round(($time_end - $time_start));
        echo '<b>Total Execution Time:</b> '.$execution_time.' Sec<br>';
        $execution_time = round(($time_end - $time_start)/60,3);
        echo '<b>Total Execution Time:</b> '.$execution_time.' Mins<br>';
    }

    public function MD($case,$jumTest,$jumTrain,$jumCase,$penyebut){
        // At start of script
        $time_start = microtime(true);
        $akurasi=0;
        $jumEksekusi=0;

        for ($id_test=0; $id_test < $jumTest; $id_test++) {
            $newCase = $case[$id_test];

            // PERHITUNGAN SIMILARITAS ------------------------------------------------------------------------
            $simMD['nilai']=0;
            for ($id_train=$jumTest; $id_train < $jumCase; $id_train++) { 
                $pembilang = 0;
                // PERHITUNGAN SIMILARITAS BERDASARKAN KELAS YG TERPILIH
                foreach ($case[$id_train] as $fitur => $oldCase) {
                    if ($fitur!='id_cb' && $fitur!='class') {
                        if ($fitur=='34') { // atribut numerik
                            $maxmin = $this->model('CBR_model')->getMaxMinData();
                            $sim_local = 1-(abs($oldCase-$newCase[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                        }else { // atribut nominal
                            if ($oldCase==$newCase[$fitur]) $sim_local = 1;
                            else $sim_local = 0;
                        }
                        $bobot_id = $this->model('CBR_model')->getBobotByFitur($fitur);
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        $pembilang += $sim_local;
                    }
                }
                $sim_global = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                if ($simMD['nilai'] < $sim_global) {
                    $simMD['nilai'] = $sim_global;
                    $simMD['id'] = $case[$id_train]['id_cb'];
                    $simMD['kelas'] = $case[$id_train]['class'];
                }
                $jumEksekusi++;
                // echo '('.$case[$id_test]['id_cb'].') == ('.$case[$id_train]['id_cb'].')<br>';
                // echo "($id_test) == ($id_train)<br>";
            }
            // echo '<br>';
            if ($case[$id_test]['class']== $simMD['kelas']) {
                $akurasi++;
            }
        }
        echo "benar = $akurasi dari $jumTest<br>";
        $akurasi = round(($akurasi/$jumTest)*100,2);
        echo "akurasi = $akurasi <br>";
        echo "jumlah data diproses = $jumEksekusi <br>";
        // echo '<hr><h1>SI</h1><pre>'; print_r($simMD); echo '</pre>';

        // Anywhere else in the script
        $time_end = microtime(true);
        $execution_time = round(($time_end - $time_start));
        echo '<b>Total Execution Time:</b> '.$execution_time.' Sec<br>';
        $execution_time = round(($time_end - $time_start)/60,3);
        echo '<b>Total Execution Time:</b> '.$execution_time.' Mins<br>';

    }
}