
<div class="container contain shadow p-4">
    <!-- <h4 class="text-center">MASUKAN GEJALA</h4> -->
    <form action="<?= BASEURL;?>/cbr/cekPenyakit" method="post">
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
                                            <td>
                                            <select id="inputState" class="form-control" name="newCase[<?= $fitur['id_bobot'];?>]">
                                                <option value='0'>Tidak</option>
                                                <option value='1'>Cukup Yakin</option>
                                                <option value='2'>Yakin</option>
                                                <option value='3'>Sangat Yakin</option>
                                            </select>
                                            </td>
                                        </tr>
                                    <?php } ?> 
                                    <?php if ($fitur['id_bobot']==11) { ?>
                                        <tr>
                                            <th scope="row"><?= $i++;?></th>
                                            <td><?= $fitur['fitur'];?></td>
                                            <td>
                                            <select id="inputState" class="form-control" name="newCase[<?= $fitur['id_bobot'];?>]">
                                                <option value='0'>Tidak</option>
                                                <option value='1'>Ya</option>
                                            </select>
                                            </td>
                                        </tr>
                                    <?php } ?> 
                                    <?php if ($fitur['id_bobot']==34) { ?>
                                        <tr>
                                            <th scope="row"><?= $i++;?></th>
                                            <td><?= $fitur['fitur'];?></td>
                                            <td>
                                            <input type="number" class="form-control" name="newCase[<?= $fitur['id_bobot'];?>]" required>
                                            </td>
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
                        <div class="table-wrapper-scroll-y my-custom-scrollbar">
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
                                            <td>
                                            <select id="inputState" class="form-control" name="newCase[<?= $fitur['id_bobot'];?>]">
                                                <option value='0'>Tidak</option>
                                                <option value='1'>Cukup Yakin</option>
                                                <option value='2'>Yakin</option>
                                                <option value='3'>Sangat Yakin</option>
                                            </select>
                                            </td>
                                        </tr>
                                    <?php }} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-md btn-block mt-4" name="submit"><b>Cek Penyakit</b></button>
    </form>
    <br>
</div>

<br><br><br><br>