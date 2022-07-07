
<div class="container contain shadow p-4">
    <!-- TABEL BIODATA -->
    <div class="row mt-3">
        <div class="col-md-5">
            <table class="table table-borderless table-sm">
                <!-- <tr>
                    <th scope="col">Nama</th>
                    <td>:</td>
                    <td scope="col"><?= $data['nama'];?></td>
                </tr> -->
                <tr>
                    <td scope="col"> <h5>Jenis Penyakit</h5> </td>
                    <td>:</td>
                    <td scope="col"><h5 class="font-weight-bold"><?= $data['penyakit'][$data['kasus_terpilih'][0]['kelas']-1];?></h5></td>
                </tr>
                <tr>
                    <td scope="col"><h5>Nilai Similaritas</h5> </td>
                    <td>:</td>
                    <td scope="col"><h5 class="font-weight-bold"><?= $data['kasus_terpilih'][0]['nilai'];?>%</h5></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- TABEL HASIL CBR -->
    <hr><h5 class="text-center font-weight-bold">HASIL PROSES CBR</h5><hr>
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Hasil Indexing (Bayesian Model)</div>
                <div class="card-body font-weight-normal">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Penyakit</th>
                            <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php foreach ($data['indexing'] as $index) { ?>
                                <tr>
                                    <th scope="row"><?= $i++;?></th>
                                    <td><?= $data['penyakit'][$index['kelas']-1];?></td>
                                    <td><?= $index['nilai'];?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Hasil Similaritas (Minkowski Distance)</div>
                <div class="card-body font-weight-normal">
                <div class="table-wrapper-scroll-y my-custom-scrollbar" style="height: 155px;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Id Kasus</th>
                            <th scope="col">Penyakit</th>
                            <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php foreach ($data['kasus_terpilih'] as $kasus) { ?>
                                <tr>
                                    <th scope="row"><?= $i++;?></th>
                                    <td><?= $kasus['id'];?></td>
                                    <td><?= $data['penyakit'][$kasus['kelas']-1];?></td>
                                    <td><?= $kasus['nilai'];?>%</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>    
    </div>
    <br>
    <!-- TABEL FITUR -->
    <hr><h5 class="text-center font-weight-bold">FEATURE INPUT</h5><hr>
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Clinical Features </div>
                <div class="card-body font-weight-normal">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Feature</th>
                            <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php foreach ($data['fitur'] as $fitur) { ?>
                                <?php if ($fitur['id_bobot']<=10) { ?>
                                    <tr>
                                        <th scope="row"><?= $i++;?></th>
                                        <td><?= $fitur['fitur'];?></td>
                                        <td><?= $data['bobot'][$data['newCase'][$fitur['id_bobot']]];?></td>
                                    </tr>
                                <?php } ?> 
                                <?php if ($fitur['id_bobot']==11) { ?>
                                    <tr>
                                        <th scope="row"><?= $i++;?></th>
                                        <td><?= $fitur['fitur'];?></td>
                                        <td><?= $data['bobot'][$data['newCase'][$fitur['id_bobot']]+4];?></td>
                                    </tr>
                                <?php } ?> 
                                <?php if ($fitur['id_bobot']==34) { ?>
                                    <tr>
                                        <th scope="row"><?= $i++;?></th>
                                        <td><?= $fitur['fitur'];?></td>
                                        <td><?= $data['newCase'][$fitur['id_bobot']];?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Histopathological Features</div>
                <div class="card-body font-weight-normal">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar" style="height: 459px;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Feature</th>
                                <th scope="col">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;?>
                                <?php foreach ($data['fitur'] as $fitur) { 
                                    if ($fitur['id_bobot']>=12 && $fitur['id_bobot']<=33) { ?>
                                    <tr>
                                        <th scope="row"><?= $i++;?></th>
                                        <td><?= $fitur['fitur'];?></td>
                                        <td><?= $data['bobot'][$data['newCase'][$fitur['id_bobot']]];?></td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<br><br><br>
</div>

<br><br><br><br>