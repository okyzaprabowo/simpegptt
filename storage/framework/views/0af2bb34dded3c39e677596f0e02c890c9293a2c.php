<div id="vuePegawaiFormAlamat">
    <div class="card-body pb-2">
        <div class="mb-2 text-right">
            <a href="javascript:void(0)" @click="alamatShowForm(true,0)" class="btn btn-success btn-sm" v-if="isEditable">
                <span class="ion ion-md-add"></span>&nbsp; Tambah Alamat
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="min-width: 1rem;">Tipe</th>
                        <th style="min-width: 5rem;">Alamat</th>
                        <th style="">Provinsi</th> 
                        <th style="">Kota</th> 
                        <th style="">Kecamatan</th> 
                        <th style="">Kelurahan/desa</th> 
                        <th style="">Kontak</th> 
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i,k) in form.alamat">
                        <td>{{i+1}}</td>
                        <td>
                            <span v-text="item.tipe_alamat==1 ? 'Alamat Sekarang' : (item.tipe_alamat==2 ? 'Alamat ketika direkrut' : 'Alamat darurat' )"></span>
                        </td>
                        <td>
                            {{item.alamat}}
                            <span v-html="item.kodepos!='' ? ('<br>Kode pos : ' + item.kodepos) : ''"></span>
                        </td>
                        <td>{{item.provinsi}}</td>
                        <td>{{item.kota}}</td>
                        <td>{{item.kecamatan}}</td>
                        <td>{{item.kelurahan}}</td>
                        <td>
                            <div v-if="item.telepon"><b>telepon :</b> {{item.telepon}}</div>
                            <div v-if="item.ponsel"><b>ponsel :</b> {{item.ponsel}}</div>
                            <div v-if="item.email"><b>email :</b> {{item.email}}</div>
                        </td>
                        <td>                            
                            <?php if(\UserAuth::hasAccess('Pegawai.master','u')): ?>
                            <div @click="alamatShowForm(false,i)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            <?php endif; ?>
                            <?php if(\UserAuth::hasAccess('Pegawai.master','d')): ?>
                            <div @click="alamatDeleteItem(i)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                                <i class="ion ion-md-close"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <i class="float-right m-3"><b>*</b> untuk menyimpan hasil penambahan atau editan data "Alamat", jangan lupa klik "<b>Simpan Data</b>"</i>
        </div>
    </div>

    <!-- modal form Jabatan -->
    <div class="modal fade" id="formPegawaiAlamatModal" tabindex="-1" role="dialog" aria-labelledby="formPegawaiAlamatModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="formPegawaiAlamatModalTitle" v-text="formTitle">Alamat Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Tipe</label>                             
                        <div class="col-sm-9">                        
                            <select class="form-control" style="width: 100%" onchange="vueFormPegawai.formAlamat.tipe_alamat = this.value" data-allow-clear="true">
                                <option value="1">Alamat Sekarang</option>
                                <option value="2">Alamat Ketika direkrut</option>
                                <option value="3">Alamat Emergency</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="formAlamat.tipe_alamat == 3">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3 text-sm-right">Nama</label>                            
                            <div class="col-sm-9">
                                <input type="text" class="form-control" v-model="formAlamat.emer_nama" max="100" placeholder="nama">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3 text-sm-right">Pekerjaan</label>                            
                            <div class="col-sm-9">
                                <input type="text" class="form-control" v-model="formAlamat.emer_pekerjaan" max="100" placeholder="pekerjaan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3 text-sm-right">Relasi</label>                            
                            <div class="col-sm-9">
                                <input type="text" class="form-control" v-model="formAlamat.emer_relasi" max="100" placeholder="relasi">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Alamat</label>                            
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" v-model="formAlamat.alamat"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Kodepos</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.kodepos" max="100" placeholder="Kodepos">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Provinsi</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.provinsi" max="100" placeholder="Provinsi">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Kota</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.kota" max="100" placeholder="Kota">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Kecamatan</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.kecamatan" max="100" placeholder="Kecamatan">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Desa/Kelurahan</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.kelurahan" max="100" placeholder="Desa/Kelurahan">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Telepon</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.telepon" max="100" placeholder="telepon">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Ponsel</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.ponsel" max="100" placeholder="ponsel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Email</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formAlamat.email" max="100" placeholder="Email">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="alamatFormSubmitted">Save</button>
                </div>
            </div>
        </div>
    </div>

</div><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/Modules/Pegawai/resources/views/pegawai/formalamat.blade.php ENDPATH**/ ?>