
<div class="container contain shadow p-4" style="width: 800px;">
    <div class="card">
        <div class="card-header text-center">REVISE</div>
        <div class="card-body font-weight-normal">
            <!-- FLASHER -->
            <div>
                <?php Flasher::flash(); ?>
            </div>
            
            <table class="table table-sm table-hover text-center">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Prediksi Penyakit</th>
                    <th scope="col">Similarity Value</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1;?>
                    <?php foreach ($data['kasus'] as $kasus) { ?>
                        <tr>
                            <th scope="row"><?= $i++;?></th>
                            <td><?= $data['penyakit'][$kasus['35']-1];?></td>
                            <td><?= $kasus['sim'];?>%</td>
                            <td><button type="button" class="btn bg-primary btn-sm text-white rounded border-0 btn-block tombolRevise" data-toggle="modal" data-target="#formModal" data-id='<?= $kasus['id_cb']; ?>'>revise</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<br><br><br><br>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Revisi Kasus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASEURL; ?>/revise/revisiKasus" method='post'>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
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
                                                <td><span id="f<?= $fitur['id_bobot'];?>"></span></td>
                                            </tr>
                                        <?php } ?> 
                                        <?php if ($fitur['id_bobot']==11) { ?>
                                            <tr>
                                                <th scope="row"><?= $i++;?></th>
                                                <td><?= $fitur['fitur'];?></td>
                                                <td><span id="f<?= $fitur['id_bobot'];?>"></span></td>
                                            </tr>
                                        <?php } ?> 
                                        <?php if ($fitur['id_bobot']==34) { ?>
                                            <tr>
                                                <th scope="row"><?= $i++;?></th>
                                                <td><?= $fitur['fitur'];?></td>
                                                <td><span id="f<?= $fitur['id_bobot'];?>"></span></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card mt-4">
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
                                                <td><span id="f<?= $fitur['id_bobot'];?>"></span></td>
                                            </tr>
                                        <?php }} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><hr>
                    <div class="form-group row mt-4">
                        <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Penyakit</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="f35" name="class">
                                <option value="1">Psoriasis</option>
                                <option value="2">Seboreic Dermatitis</option>
                                <option value="3">Lichen Planus</option>
                                <option value="4">Pityriasis Rosea</option>
                                <option value="5">Cronic Dermatitis</option>
                                <option value="6">Pityriasis Rubra Pilaris</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary btn-block">REVISE</button>
                        </div>
                    </div>
                </div>
		    </form>
		</div>
	</div>
</div>