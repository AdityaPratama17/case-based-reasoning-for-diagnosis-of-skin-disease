<?php 

class AKURASI extends Controller{
    public function index(){
        $data['judul']='Akurasi';
        $data['fitur'] = $this->model('CBR_model')->getAllBobot();
        $data['flag']=FALSE;
        
        $this->view('templates/header', $data);
        $this->view('akurasi/index', $data);
        $this->view('templates/footer', $data);
    }

    public function akurasi(){
        // AMBIL KASUS LAMA
        set_time_limit(500);
        $case = $this->model('CBR_model')->getAllCase();
        // SPLIT DATA TRAIN & TEST
        shuffle($case);
        $jumCase = count($case);
        $persentase = $_POST['split'];
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

        // echo '<hr><h1>DENGAN INDEXING</h1>';
        $data['a'] = $this->IndexingMD($case,$jumTest,$jumTrain,$jumCase,$penyebut);
        // echo '<hr><h1>TANPA INDEXING</h1>';
        $data['b'] = $this->MD($case,$jumTest,$jumTrain,$jumCase,$penyebut);
        
        $data['flag']=true;
        $data['judul']='Akurasi';
        $data['jumCase']=$jumCase;
        $data['jumTrain']=$jumTrain;
        $data['jumTest']=$jumTest;

        $this->view('templates/header', $data);
        $this->view('akurasi/index', $data);
        $this->view('templates/footer', $data);
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
            // if ($simMD['nilai']<80) {
            //     echo $simMD['nilai'];
            // }
        }
        // echo "benar = $akurasi dari $jumTest<br>";
        // $akurasi = round(($akurasi/$jumTest)*100,2);
        // echo "akurasi = $akurasi <br>";
        // echo "jumlah data diproses = $jumEksekusi <br>";
        // // echo '<hr><h1>SIMILARITAS</h1><pre>'; print_r($simMD); echo '</pre>';

        // // Anywhere else in the script
        $time_end = microtime(true);
        // $execution_time = round(($time_end - $time_start));
        // echo '<b>Total Execution Time:</b> '.$execution_time.' Sec<br>';
        // $execution_time = round(($time_end - $time_start)/60,3);
        // echo '<b>Total Execution Time:</b> '.$execution_time.' Mins<br>';

        $execution_time = round(($time_end - $time_start));
        $data['menit'] = floor($execution_time/60);
        $data['detik'] = $execution_time % 60;
        $data['jumBenar']=$akurasi;
        $data['akurasi']=round(($akurasi/$jumTest)*100,2);
        return $data;
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
        // echo "benar = $akurasi dari $jumTest<br>";
        // $akurasi = round(($akurasi/$jumTest)*100,2);
        // echo "akurasi = $akurasi <br>";
        // echo "jumlah data diproses = $jumEksekusi <br>";
        // echo '<hr><h1>SI</h1><pre>'; print_r($simMD); echo '</pre>';

        // Anywhere else in the script
        $time_end = microtime(true);
        // $execution_time = round(($time_end - $time_start));
        // echo '<b>Total Execution Time:</b> '.$execution_time.' Sec<br>';
        // $execution_time = round(($time_end - $time_start)/60,3);
        // echo '<b>Total Execution Time:</b> '.$execution_time.' Mins<br>';

        $execution_time = round(($time_end - $time_start));
        $data['menit'] = floor($execution_time/60);
        $data['detik'] = $execution_time % 60;
        $data['jumBenar']=$akurasi;
        $data['akurasi']=round(($akurasi/$jumTest)*100,2);
        return $data;

    }
}