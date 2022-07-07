<?php 

class COBA extends Controller{
	public function index($id){
        for ($id_test=1; $id_test < 2; $id_test++) { 
            
        // DATA TESTING
        echo '<h3>DATA TESTING</h3>';
        $test = $this->model('COBA_model')->getDataTest($id);
        // $test = $this->model('COBA_model')->getDataTest($id_test);
        echo '<pre>'; print_r($test); echo '</pre>';
        
        // AMBIL SEMUA DATA TRAINING
        $jumTrain = count($this->model('COBA_model')->getAllDataTrain());
        echo 'jumlah data trainning : '.$jumTrain;
        
        // PERHITUNGAN P(Ci)
        echo '<hr><h3>PERHITUNGAN P(Ci)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $jumKelas[$i] = count($this->model('COBA_model')->getJumlahKelas($i));
            $P_Ci[$i] = $jumKelas[$i]/$jumTrain;
            echo 'P(C'.$i.') = '.$jumKelas[$i].'/'.$jumTrain.' = '.$P_Ci[$i].'<br>';
        }
        
        // PERHITUNGAN P(X)
        echo '<hr><h3>PERHITUNGAN P(X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            echo '<br> Kelas '.$i.'<br>';
            $P_XCi[$i]=$P_Ci[$i];
            foreach (array_keys($test) as $idx) {
                if ($idx!='id_cb' && $idx!='class') {
                    $jumFitur = count($this->model('COBA_model')->getJumlahKelasPerFitur($i,$idx,$test[$idx]));
                    if ($jumFitur==0) {
                        $jumFitur=1;
                    }
                    $px = $jumFitur/$jumKelas[$i];
                    echo $idx.' = '.$jumFitur.'/'.$jumKelas[$i].' = '.$px.'<br>';
                    $P_XCi[$i] *= $px;
                }
            }
        }
        
        // PERHITUNGAN P(X|Ci)
        echo '<hr><h3>PERHITUNGAN P(X|Ci)</h3>';
        $jumP_XCi=0;
        for ($i=1; $i <= 6; $i++) { 
            echo 'P(X|C'.$i.') = '.$P_XCi[$i].'<br>';
            $jumP_XCi += $P_XCi[$i];
        }
        
        // PERHITUNGAN P(Ci|X)
        echo '<hr><h3>PERHITUNGAN P(Ci|X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $P_CiX[$i] = $P_XCi[$i]/$jumP_XCi;
            echo 'P(C'.$i.'|X) = '.$P_XCi[$i].'/'.$jumP_XCi.' = '.$P_CiX[$i].'<br>';
        }
        
        // TAMPILKAN PROBABILITAS TERBESAR
        echo '<hr><h3>KELAS TERPILIH</h3>';
        arsort($P_CiX);
        
        $indexing = array();
        $i=1;
        foreach($P_CiX as $x => $x_value) {
            $indexing[$i]=$x;
            $i++;
            if ($i==4) break;
        }
        echo '<pre>'; print_r($indexing); echo '</pre>';
        
        
        // AMBIL BOBOT & PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('COBA_model')->getAllBobot();
        // echo '<pre>'; print_r($bobot); echo '</pre>';
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }
        // echo 'penyebut = '.$penyebut.'<br>';

        for ($i=1; $i <4; $i++) { 
            $kasus_terpilih[$i]['id']=0;
            $kasus_terpilih[$i]['nilai']=0;
            $kasus_terpilih[$i]['kelas']=0;
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';

        echo '<hr><h3>PERHITUNGAN SIMILARITAS</h3>';

        // PERHITUNGAN SIMILARITAS
        for ($i=1; $i <= $jumTrain; $i++) { 
            // AMBIL KASUS LAMA
            // echo '<hr><h3>DATA TRAINING</h3>';
            $train = $this->model('COBA_model')->getDataTrain($i);
            // echo '<pre>'; print_r($train); echo '</pre>';

            if (in_array($train['class'], $indexing)) {
                
                // SIMILARITAS LOKAL
                $simMD[$i]['kelas'] = $train['class'];
                $pembilang[$i] = 0;
                foreach ($train as $key => $t) {
                    if ($key!='id_cb' && $key!='class') {
                        if ($key=='34') {
                            // ambil max min data
                            $maxmin = $this->model('COBA_model')->getMaxMinData();
                            // echo $key.' = '.$maxmin['maxx'].' - '.$maxmin['minn'].'<br>';
                            $sim_local = 1-(abs($t-$test[$key])/($maxmin['maxx']-$maxmin['minn']));
                        }else {
                            if ($t==$test[$key]) {
                                $sim_local = 1;
                            }else {
                                $sim_local = 0;
                            }
                        }
                        // AMBIL BOBOT
                        $bobot_id = $this->model('COBA_model')->getBobotByFitur($key);
                        
                        // echo $key.' = '.$sim_local.' * '.$bobot_id['nilai'].'<br>';
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        // echo $key.' = '.$sim_local.'<br>';
                        
                        $pembilang[$i] += $sim_local;
                    }
                }
                // echo 'pembilang = '.$pembilang[$i].'<br>';
                $simMD[$i]['nilai'] = round((pow(($pembilang[$i]/$penyebut), 1/3)* 100), 2);
                echo 'SimMD('.$i.') = '.$simMD[$i]['nilai'].'     kelas = '.$simMD[$i]['kelas'].'<br>';
                
                // ambil 3 kasus dengan sim terbesar
                if ($simMD[$i]['nilai']>$kasus_terpilih[1]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    $kasus_terpilih[2]=$kasus_terpilih[1];
                    // $kasus_terpilih[1]=$simMD[$i];
                    $kasus_terpilih[1]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[1]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[1]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[2]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    // $kasus_terpilih[2]=$simMD[$i];
                    $kasus_terpilih[2]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[2]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[2]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[3]['nilai']) {
                    // $kasus_terpilih[3]=$simMD[$i];
                    $kasus_terpilih[3]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[3]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[3]['id']=$train['id_cb'];
                }
                // echo '<br>case 1 = '.$kasus_terpilih[1]['nilai'];
                // echo '<br>case 2 = '.$kasus_terpilih[2]['nilai'];
                // echo '<br>case 3 = '.$kasus_terpilih[3]['nilai'].'<br>';
            }
        }
        echo '<hr><h3>KASUS TERPILIH</h3><pre>'; print_r($kasus_terpilih); echo '</pre>';

        // if ($test['class']==$kasus_terpilih[1]['kelas']) {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - benar';
        // }else {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - salah';
        // }
        }
        // foreach ($kasus_terpilih as $key => $kt) {
        //     echo $key.' = '.$kt['nilai'].' = '.$kt['kelas'].'<br>';
        // }



        // SIMILARITAS GLOBAL


	}

    public function laporan($id){
        for ($id_test=1; $id_test < 2; $id_test++) { 
            
        // DATA TESTING
        echo '<h3>DATA TESTING</h3>';
        $test = $this->model('COBA_model')->getDataTest($id);
        // $test = $this->model('COBA_model')->getDataTest($id_test);
        echo '<pre>'; print_r($test); echo '</pre>';
        
        // AMBIL SEMUA DATA TRAINING
        $jumTrain = count($this->model('COBA_model')->getAllDataTrain());
        echo 'jumlah data trainning : '.$jumTrain;
        
        // PERHITUNGAN P(Ci)
        echo '<hr><h3>PERHITUNGAN P(Ci)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $jumKelas[$i] = count($this->model('COBA_model')->getJumlahKelas($i));
            $P_Ci[$i] = $jumKelas[$i]/$jumTrain;
            echo 'P(C'.$i.') = '.$jumKelas[$i].'/'.$jumTrain.' = '.$P_Ci[$i].'<br>';
        }
        
        // PERHITUNGAN P(X)
        echo '<hr><h3>PERHITUNGAN P(X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            echo '<br> Kelas '.$i.'<br>';
            $P_XCi[$i]=$P_Ci[$i];
            foreach (array_keys($test) as $idx) {
                if ($idx!='id_cb' && $idx!='class') {
                    $jumFitur = count($this->model('COBA_model')->getJumlahKelasPerFitur($i,$idx,$test[$idx]));
                    if ($jumFitur==0) {
                        $jumFitur=1;
                    }
                    $px = $jumFitur/$jumKelas[$i];
                    echo $idx.' = '.$jumFitur.'/'.$jumKelas[$i].' = '.$px.'<br>';
                    $P_XCi[$i] *= $px;
                }
            }
        }
        
        // PERHITUNGAN P(X|Ci)
        echo '<hr><h3>PERHITUNGAN P(X|Ci)</h3>';
        $jumP_XCi=0;
        for ($i=1; $i <= 6; $i++) { 
            echo 'P(X|C'.$i.') = '.$P_XCi[$i].'<br>';
            $jumP_XCi += $P_XCi[$i];
        }
        
        // PERHITUNGAN P(Ci|X)
        echo '<hr><h3>PERHITUNGAN P(Ci|X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $P_CiX[$i] = $P_XCi[$i]/$jumP_XCi;
            echo 'P(C'.$i.'|X) = '.$P_XCi[$i].'/'.$jumP_XCi.' = '.$P_CiX[$i].'<br>';
        }
        
        // TAMPILKAN PROBABILITAS TERBESAR
        echo '<hr><h3>KELAS TERPILIH</h3>';
        arsort($P_CiX);
        
        $indexing = array();
        $i=1;
        foreach($P_CiX as $x => $x_value) {
            $indexing[$i]=$x;
            $i++;
            if ($i==4) break;
        }
        echo '<pre>'; print_r($indexing); echo '</pre>';
        
        
        // AMBIL BOBOT & PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('COBA_model')->getAllBobot();
        // echo '<pre>'; print_r($bobot); echo '</pre>';
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }
        // echo 'penyebut = '.$penyebut.'<br>';

        for ($i=1; $i <4; $i++) { 
            $kasus_terpilih[$i]['id']=0;
            $kasus_terpilih[$i]['nilai']=0;
            $kasus_terpilih[$i]['kelas']=0;
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';

        echo '<hr><h3>PERHITUNGAN SIMILARITAS</h3>';

        // PERHITUNGAN SIMILARITAS
        for ($i=6; $i <= 6; $i++) { 
            // AMBIL KASUS LAMA
            // echo '<hr><h3>DATA TRAINING</h3>';
            $train = $this->model('COBA_model')->getDataTrain($i);
            // echo '<pre>'; print_r($train); echo '</pre>';

            if (in_array($train['class'], $indexing)) {
                
                // SIMILARITAS LOKAL
                $simMD[$i]['kelas'] = $train['class'];
                $pembilang[$i] = 0;
                foreach ($train as $key => $t) {
                    if ($key!='id_cb' && $key!='class') {
                        if ($key=='34') {
                            // ambil max min data
                            $maxmin = $this->model('COBA_model')->getMaxMinData();
                            // echo $key.' = '.$maxmin['maxx'].' - '.$maxmin['minn'].'<br>';
                            $sim_local = 1-(abs($t-$test[$key])/($maxmin['maxx']-$maxmin['minn']));
                        }else {
                            if ($t==$test[$key]) {
                                $sim_local = 1;
                            }else {
                                $sim_local = 0;
                            }
                        }
                        // AMBIL BOBOT
                        $bobot_id = $this->model('COBA_model')->getBobotByFitur($key);
                        
                        echo $key.' = '.$sim_local.' * '.$bobot_id['nilai'].'<br>';
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        echo $key.' = '.$sim_local.'<br>';
                        
                        $pembilang[$i] += $sim_local;
                    }
                }
                // echo 'pembilang = '.$pembilang[$i].'<br>';
                $simMD[$i]['nilai'] = round((pow(($pembilang[$i]/$penyebut), 1/3)* 100), 2);
                echo 'SimMD('.$i.') = '.$simMD[$i]['nilai'].'     kelas = '.$simMD[$i]['kelas'].'<br>';
                
                // ambil 3 kasus dengan sim terbesar
                if ($simMD[$i]['nilai']>$kasus_terpilih[1]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    $kasus_terpilih[2]=$kasus_terpilih[1];
                    // $kasus_terpilih[1]=$simMD[$i];
                    $kasus_terpilih[1]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[1]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[1]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[2]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    // $kasus_terpilih[2]=$simMD[$i];
                    $kasus_terpilih[2]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[2]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[2]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[3]['nilai']) {
                    // $kasus_terpilih[3]=$simMD[$i];
                    $kasus_terpilih[3]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[3]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[3]['id']=$train['id_cb'];
                }
                // echo '<br>case 1 = '.$kasus_terpilih[1]['nilai'];
                // echo '<br>case 2 = '.$kasus_terpilih[2]['nilai'];
                // echo '<br>case 3 = '.$kasus_terpilih[3]['nilai'].'<br>';
            }
        }
        echo '<hr><h3>KASUS TERPILIH</h3><pre>'; print_r($kasus_terpilih); echo '</pre>';

        // if ($test['class']==$kasus_terpilih[1]['kelas']) {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - benar';
        // }else {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - salah';
        // }
        }
        // foreach ($kasus_terpilih as $key => $kt) {
        //     echo $key.' = '.$kt['nilai'].' = '.$kt['kelas'].'<br>';
        // }



        // SIMILARITAS GLOBAL


	}


	public function step($id){
        for ($id_test=1; $id_test < 2; $id_test++) { 
            
        // DATA TESTING
        echo '<h3>DATA TESTING</h3>';
        $test = $this->model('COBA_model')->getDataTest($id);
        // $test = $this->model('COBA_model')->getDataTest($id_test);
        echo '<pre>'; print_r($test); echo '</pre>';
        
        // AMBIL SEMUA DATA TRAINING
        $jumTrain = count($this->model('COBA_model')->getAllDataTrain());
        echo 'jumlah data trainning : '.$jumTrain;
        
        // PERHITUNGAN P(Ci)
        echo '<hr><h3>PERHITUNGAN P(Ci)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $jumKelas[$i] = count($this->model('COBA_model')->getJumlahKelas($i));
            $P_Ci[$i] = $jumKelas[$i]/$jumTrain;
            echo 'P(C'.$i.') = '.$jumKelas[$i].'/'.$jumTrain.' = '.$P_Ci[$i].'<br>';
        }
        
        // PERHITUNGAN P(X)
        echo '<hr><h3>PERHITUNGAN P(X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            echo '<br> Kelas '.$i.'<br>';
            $P_XCi[$i]=$P_Ci[$i];
            foreach (array_keys($test) as $idx) {
                if ($idx!='id_cb' && $idx!='class') {
                    $jumFitur = count($this->model('COBA_model')->getJumlahKelasPerFitur($i,$idx,$test[$idx]));
                    if ($jumFitur==0) {
                        $jumFitur=1;
                    }
                    $px = $jumFitur/$jumKelas[$i];
                    echo $idx.' = '.$jumFitur.'/'.$jumKelas[$i].' = '.$px.'<br>';
                    $P_XCi[$i] *= $px;
                }
            }
        }
        
        // PERHITUNGAN P(X|Ci)
        echo '<hr><h3>PERHITUNGAN P(X|Ci)</h3>';
        $jumP_XCi=0;
        for ($i=1; $i <= 6; $i++) { 
            echo 'P(X|C'.$i.') = '.$P_XCi[$i].'<br>';
            $jumP_XCi += $P_XCi[$i];
        }
        
        // PERHITUNGAN P(Ci|X)
        echo '<hr><h3>PERHITUNGAN P(Ci|X)</h3>';
        for ($i=1; $i <= 6; $i++) { 
            $P_CiX[$i] = $P_XCi[$i]/$jumP_XCi;
            echo 'P(C'.$i.'|X) = '.$P_XCi[$i].'/'.$jumP_XCi.' = '.$P_CiX[$i].'<br>';
        }
        
        // TAMPILKAN PROBABILITAS TERBESAR
        echo '<hr><h3>KELAS TERPILIH</h3>';
        arsort($P_CiX);
        
        $indexing = array();
        $i=1;
        foreach($P_CiX as $x => $x_value) {
            $indexing[$i]=$x;
            $i++;
            if ($i==4) break;
        }
        echo '<pre>'; print_r($indexing); echo '</pre>';
        
        
        // AMBIL BOBOT & PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('COBA_model')->getAllBobot();
        // echo '<pre>'; print_r($bobot); echo '</pre>';
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }
        // echo 'penyebut = '.$penyebut.'<br>';

        for ($i=1; $i <4; $i++) { 
            $kasus_terpilih[$i]['id']=0;
            $kasus_terpilih[$i]['nilai']=0;
            $kasus_terpilih[$i]['kelas']=0;
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';

        // PERHITUNGAN SIMILARITAS
        for ($i=1; $i <= 1; $i++) { 
            // AMBIL KASUS LAMA
            // echo '<hr><h3>DATA TRAINING</h3>';
            $train = $this->model('COBA_model')->getDataTrain(6);
            echo '<hr><h3>DATA TRAINING</h3><pre>'; print_r($train); echo '</pre>';
            echo '<hr><h3>PERHITUNGAN SIMILARITAS LOKAL</h3>';

            if (in_array($train['class'], $indexing)) {
                
                // SIMILARITAS LOKAL
                $simMD[$i]['kelas'] = $train['class'];
                $pembilang[$i] = 0;
                foreach ($train as $key => $t) {
                    if ($key!='id_cb' && $key!='class') {
                        if ($key=='34') {
                            // ambil max min data
                            $maxmin = $this->model('COBA_model')->getMaxMinData();
                            // echo $key.' = '.$maxmin['maxx'].' - '.$maxmin['minn'].'<br>';
                            $sim_local = 1-(abs($t-$test[$key])/($maxmin['maxx']-$maxmin['minn']));
                        }else {
                            if ($t==$test[$key]) {
                                $sim_local = 1;
                            }else {
                                $sim_local = 0;
                            }
                        }
                        // AMBIL BOBOT
                        $bobot_id = $this->model('COBA_model')->getBobotByFitur($key);
                        
                        echo $key.' = '.$sim_local.' * '.$bobot_id['nilai'].'<br>';
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        // echo $key.' = '.$sim_local.'<br>';
                        
                        $pembilang[$i] += $sim_local;
                    }
                }
                // echo 'pembilang = '.$pembilang[$i].'<br>';
                echo '<hr><h3>HASIL SIMILARITAS</h3>SimMD('.$i.') = '.$sim_local.' / '.$penyebut.' = ';
                $simMD[$i]['nilai'] = round((pow(($pembilang[$i]/$penyebut), 1/3)* 100), 2);
                echo $simMD[$i]['nilai'].'%<br>';
                
                // ambil 3 kasus dengan sim terbesar
                if ($simMD[$i]['nilai']>$kasus_terpilih[1]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    $kasus_terpilih[2]=$kasus_terpilih[1];
                    // $kasus_terpilih[1]=$simMD[$i];
                    $kasus_terpilih[1]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[1]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[1]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[2]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    // $kasus_terpilih[2]=$simMD[$i];
                    $kasus_terpilih[2]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[2]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[2]['id']=$train['id_cb'];
                }elseif ($simMD[$i]['nilai']>$kasus_terpilih[3]['nilai']) {
                    // $kasus_terpilih[3]=$simMD[$i];
                    $kasus_terpilih[3]['nilai']=$simMD[$i]['nilai'];
                    $kasus_terpilih[3]['kelas']=$simMD[$i]['kelas'];
                    $kasus_terpilih[3]['id']=$train['id_cb'];
                }
                // echo '<br>case 1 = '.$kasus_terpilih[1]['nilai'];
                // echo '<br>case 2 = '.$kasus_terpilih[2]['nilai'];
                // echo '<br>case 3 = '.$kasus_terpilih[3]['nilai'].'<br>';
            }
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';
        // if ($test['class']==$kasus_terpilih[1]['kelas']) {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - benar';
        // }else {
        //     echo ' kb='.$test['class'].' - kl='.$kasus_terpilih[1]['kelas'].' - salah';
        // }
        }
        // foreach ($kasus_terpilih as $key => $kt) {
        //     echo $key.' = '.$kt['nilai'].' = '.$kt['kelas'].'<br>';
        // }



        // SIMILARITAS GLOBAL


	}



    public function akurasi(){
        set_time_limit(500);
        // AMBIL JUMLAH DATA TRAINING DAN DATA TESTING
        $jumTrain = count($this->model('COBA_model')->getAllDataTrain());
        $jumTest = count($this->model('COBA_model')->getAllDataTest());
        // AMBIL BOBOT
        $bobot = $this->model('COBA_model')->getAllBobot();
        $akurasi = 0;
        $total = 100;
        // -------------------------------------------------------------------------------

        for ($id_test=1; $id_test <= $jumTest; $id_test++) { 
        // for ($id_test=5; $id_test < 6; $id_test++) { 
        // for ($id_test=1; $id_test < $total+1; $id_test++) { 
            echo '<br>'.$id_test;
            // DATA TESTING
            $test = $this->model('COBA_model')->getDataTest($id_test);
            
            // PERHITUNGAN INDEXING BAYES
            $jumP_XCi=0;
            for ($i=1; $i < 6; $i++) { 
                // PERHITUNGAN P(Ci)
                $jumKelas[$i] = count($this->model('COBA_model')->getJumlahKelas($i));
                $P_Ci[$i] = $jumKelas[$i]/$jumTrain;
                // PERHITUNGAN P(X)
                $P_XCi[$i]=$P_Ci[$i];
                foreach (array_keys($test) as $fitur) {
                    if ($fitur!='id_cb' && $fitur!='result') {
                        $jumFitur = count($this->model('COBA_model')->getJumlahKelasPerFitur($i,$fitur,$test[$fitur]));
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
            for ($i=1; $i < 6; $i++) { 
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
            
            // PERHITUNGAN PENYEBUT PADA MINKOWSKI
            $penyebut = 0;
            foreach ($bobot as $key => $b) {
                $penyebut += pow($b['nilai'],3);
            }
            
            // INIT KASUS TERPILIH
            $kasus_terpilih = array_fill(1,3,array_fill_keys(array('id','nilai','kelas'),0));
            
            // PERHITUNGAN SIMILARITAS
            for ($id_train=1; $id_train < $jumTrain; $id_train++) {
                // AMBIL KASUS LAMA
                $train = $this->model('COBA_model')->getDataTrain($id_train);
                
                // PERHITUNGAN SIMILARITAS BERDASARKAN KELAS YG TERPILIH
                for ($j=1; $j < 4; $j++) { 
                    if ($train['result'] == $indexing[$j]['kelas']) {
                        $simMD[$id_train]['kelas'] = $train['result'];
                        $pembilang = 0;
                        
                        // SIMILARITAS LOKAL
                        foreach ($train as $fitur => $t) {
                            if ($fitur!='id_cb' && $fitur!='result') {
                                if ($fitur=='age' || $fitur=='absences') { // atribut numerik
                                    $maxmin = $this->model('COBA_model')->getMaxMinData($fitur);
                                    $sim_local = 1-(abs($t-$test[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                                }else { // atribut nominal
                                    if ($t==$test[$fitur]) {
                                        $sim_local = 1;
                                    }else {
                                        $sim_local = 0;
                                    }
                                }
                                $bobot_id = $this->model('COBA_model')->getBobotByFitur($fitur);
                                $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                                $pembilang += $sim_local;
                            }
                        }
                        
                        // SIMILARITAS GLOBAL
                        $simMD[$id_train]['nilai'] = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                        
                        // AMBIL 3 KASUS DENGAN SIM TERBESAR
                        if ($simMD[$id_train]['nilai']>$kasus_terpilih[1]['nilai']) {
                            $kasus_terpilih[3]=$kasus_terpilih[2];
                            $kasus_terpilih[2]=$kasus_terpilih[1];
                            $kasus_terpilih[1]['nilai']=$simMD[$id_train]['nilai'];
                            $kasus_terpilih[1]['kelas']=$simMD[$id_train]['kelas'];
                            $kasus_terpilih[1]['id']=$train['id_cb'];
                        }elseif ($simMD[$id_train]['nilai']>$kasus_terpilih[2]['nilai']) {
                            $kasus_terpilih[3]=$kasus_terpilih[2];
                            $kasus_terpilih[2]['nilai']=$simMD[$id_train]['nilai'];
                            $kasus_terpilih[2]['kelas']=$simMD[$id_train]['kelas'];
                            $kasus_terpilih[2]['id']=$train['id_cb'];
                        }elseif ($simMD[$id_train]['nilai']>$kasus_terpilih[3]['nilai']) {
                            $kasus_terpilih[3]['nilai']=$simMD[$id_train]['nilai'];
                            $kasus_terpilih[3]['kelas']=$simMD[$id_train]['kelas'];
                            $kasus_terpilih[3]['id']=$train['id_cb'];
                        }
                        
                    }
                }
            }
            // AKURASI
            if ($test['result']==$kasus_terpilih[1]['kelas']) {
                $akurasi++;
                echo ' kb='.$test['result'].' - kl='.$kasus_terpilih[1]['kelas'].' - benar';
            }else {
                echo ' kb='.$test['result'].' - kl='.$kasus_terpilih[1]['kelas'].' - salah';
            }
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';
        echo '<br> benar = '.$akurasi.' total = '.$jumTest;
        $akurasi = round(($akurasi/$jumTest)*100, 2);
        // echo '<br> benar = '.$akurasi.' total = '.$total;
        // $akurasi = round(($akurasi/$total)*100, 2);
        echo '<br> hasil = '.$akurasi;
	}



    public function noindex(){
        set_time_limit(500);
        // AMBIL JUMLAH DATA TRAINING DAN DATA TESTING
        $jumTrain = count($this->model('COBA_model')->getAllDataTrain());
        $jumTest = count($this->model('COBA_model')->getAllDataTest());
        // AMBIL BOBOT
        $bobot = $this->model('COBA_model')->getAllBobot();
        $akurasi = 0;
        $total = 100;
        // -------------------------------------------------------------------------------

        for ($id_test=1; $id_test <= $jumTest; $id_test++) { 
        // for ($id_test=5; $id_test < 6; $id_test++) { 
        // for ($id_test=1; $id_test < $total+1; $id_test++) { 
            echo '<br>'.$id_test;
            // DATA TESTING
            $test = $this->model('COBA_model')->getDataTest($id_test);
            
            // PERHITUNGAN PENYEBUT PADA MINKOWSKI
            $penyebut = 0;
            foreach ($bobot as $key => $b) {
                $penyebut += pow($b['nilai'],3);
            }
            
            // INIT KASUS TERPILIH
            $kasus_terpilih = array_fill(1,3,array_fill_keys(array('id','nilai','kelas'),0));
            
            // PERHITUNGAN SIMILARITAS
            for ($id_train=1; $id_train < $jumTrain; $id_train++) {
                // AMBIL KASUS LAMA
                $train = $this->model('COBA_model')->getDataTrain($id_train);
                $simMD[$id_train]['kelas'] = $train['result'];
                $pembilang = 0;
                
                // SIMILARITAS LOKAL
                foreach ($train as $fitur => $t) {
                    if ($fitur!='id_cb' && $fitur!='result') {
                        if ($fitur=='age' || $fitur=='absences') { // atribut numerik
                            $maxmin = $this->model('COBA_model')->getMaxMinData($fitur);
                            $sim_local = 1-(abs($t-$test[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                        }else { // atribut nominal
                            if ($t==$test[$fitur]) {
                                $sim_local = 1;
                            }else {
                                $sim_local = 0;
                            }
                        }
                        $bobot_id = $this->model('COBA_model')->getBobotByFitur($fitur);
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        $pembilang += $sim_local;
                    }
                }
                
                // SIMILARITAS GLOBAL
                $simMD[$id_train]['nilai'] = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                
                // AMBIL 3 KASUS DENGAN SIM TERBESAR
                if ($simMD[$id_train]['nilai']>$kasus_terpilih[1]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    $kasus_terpilih[2]=$kasus_terpilih[1];
                    $kasus_terpilih[1]['nilai']=$simMD[$id_train]['nilai'];
                    $kasus_terpilih[1]['kelas']=$simMD[$id_train]['kelas'];
                    $kasus_terpilih[1]['id']=$train['id_cb'];
                }elseif ($simMD[$id_train]['nilai']>$kasus_terpilih[2]['nilai']) {
                    $kasus_terpilih[3]=$kasus_terpilih[2];
                    $kasus_terpilih[2]['nilai']=$simMD[$id_train]['nilai'];
                    $kasus_terpilih[2]['kelas']=$simMD[$id_train]['kelas'];
                    $kasus_terpilih[2]['id']=$train['id_cb'];
                }elseif ($simMD[$id_train]['nilai']>$kasus_terpilih[3]['nilai']) {
                    $kasus_terpilih[3]['nilai']=$simMD[$id_train]['nilai'];
                    $kasus_terpilih[3]['kelas']=$simMD[$id_train]['kelas'];
                    $kasus_terpilih[3]['id']=$train['id_cb'];
                }
            }
            // AKURASI
            if ($test['result']==$kasus_terpilih[1]['kelas']) {
                $akurasi++;
                echo ' kb='.$test['result'].' - kl='.$kasus_terpilih[1]['kelas'].' - benar';
            }else {
                echo ' kb='.$test['result'].' - kl='.$kasus_terpilih[1]['kelas'].' - salah';
            }
        }
        // echo '<pre>'; print_r($kasus_terpilih); echo '</pre>';
        echo '<br> benar = '.$akurasi.' total = '.$jumTest;
        $akurasi = round(($akurasi/$jumTest)*100, 2);
        // echo '<br> benar = '.$akurasi.' total = '.$total;
        // $akurasi = round(($akurasi/$total)*100, 2);
        echo '<br> hasil = '.$akurasi;
	}



    public function minkowski(){
        // At start of script
        $time_start = microtime(true); 

        
        set_time_limit(500);
        $case = $this->model('COBA_model')->getAllCase();
        shuffle($case);
        $jumCase = count($case);
        $jumTrain=round((0.7*$jumCase),0)+1;
        $jumTest=$jumCase-$jumTrain;
        // echo "$jumTrain + $jumTest = $jumCase";
        $akurasi=0;
        $sama = 0;

        // PERHITUNGAN PENYEBUT PADA MINKOWSKI
        $bobot = $this->model('COBA_model')->getAllBobot();
        $penyebut = 0;
        foreach ($bobot as $key => $b) {
            $penyebut += pow($b['nilai'],3);
        }

        // SIMILARITAS
        // for ($id_test=0; $id_test < 1; $id_test++) { 
        for ($id_test=0; $id_test < $jumTest; $id_test++) { 
            $case_test = $case[$id_test];
            $simMD['nilai'] = 0;
            // for ($id_train=$jumTest; $id_train < $jumTest+1; $id_train++) { 
            for ($id_train=$jumTest; $id_train < $jumTrain; $id_train++) { 
                $pembilang = 0;
                foreach ($case[$id_train] as $fitur => $case_train) {
                    if ($fitur!='id_cb' && $fitur!='result') {
                        if ($fitur=='age' || $fitur=='absences') { // atribut numerik
                            $maxmin = $this->model('COBA_model')->getMaxMinData($fitur);
                            $sim_local = 1-(abs($case_train-$case_test[$fitur])/($maxmin['maxx']-$maxmin['minn']));
                        }else { // atribut nominal
                            if ($case_train==$case_test[$fitur]) $sim_local = 1;
                            else $sim_local = 0;
                        }
                        $bobot_id = $this->model('COBA_model')->getBobotByFitur($fitur);
                        $sim_local = pow(($sim_local*$bobot_id['nilai']), 3);
                        $pembilang += $sim_local;
                    }
                }
                $sim_global = round((pow(($pembilang/$penyebut), 1/3)* 100), 2);
                if ($simMD['nilai'] < $sim_global) {
                    $simMD['nilai'] = $sim_global;
                    $simMD['id'] = $case[$id_train]['id_cb'];
                    $simMD['kelas'] = $case[$id_train]['result'];
                }
            }
            echo "<br>($id_test) kelas = ".$case[$id_test]['result']."($id_train) kelas = ".$case[$id_train]['result'].' akurasi = '.$simMD['nilai'];
            if ($case[$id_test]['result']== $simMD['kelas']) {
                $akurasi++;
            }
        }
        // echo "<br>benar = $akurasi pembilang = $pembilang, penyebut = $penyebut, total =".$simMD['nilai'];
        echo "<hr><br> akurasi = $akurasi";
        $akurasi = round(($akurasi/194)*100,2);
        echo "<br> akurasi = $akurasi, sama = $sama";

        // Anywhere else in the script
        $time_end = microtime(true);
        $execution_time = round(($time_end - $time_start)/60,3);
        echo '<br><b>Total Execution Time:</b> '.$execution_time.' Mins';
        $execution_time = round(($time_end - $time_start),3);
        echo '<br><b>Total Execution Time:</b> '.$execution_time.' Sec';
        // echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);


    }

}