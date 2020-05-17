<div id="vuePegawaiFormKeluarga">
    <div class="card-body pb-2">
        <div class="mb-2 text-right">
            <a href="javascript:void(0)" @click="keluargaShowForm(true,0)" class="btn btn-success btn-sm" v-if="isEditable">
                <span class="ion ion-md-add"></span>&nbsp; Tambah Keluarga
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="min-width: 1rem;">Relasi</th>
                        <th style="min-width: 5rem;">Nama</th>
                        <th style="min-width: 5rem;">Jenis Kelamin</th>
                        <th style="">TTL</th> 
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i,k) in form.keluarga">
                        <td>@{{i+1}}</td>                        
                        <td>@{{item.relasi}}</td>             
                        <td>@{{item.nama}}</td>
                        <td>
                            <span v-text="item.kelamin==1 ? 'Laki-laki' : 'Perempuan'"></span>
                        </td>
                        <td>
                            @{{item.tempat_lahir}}, @{{item.tanggal_lahir}}
                        </td>
                        <td>                            
                            @if(\UserAuth::hasAccess('Pegawai.master','u'))
                            <div @click="keluargaShowForm(false,i)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            @endif
                            @if(\UserAuth::hasAccess('Pegawai.master','d'))
                            <div @click="keluargaDeleteItem(i)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                                <i class="ion ion-md-close"></i>
                            </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <i class="float-right m-3"><b>*</b> untuk menyimpan hasil penambahan atau editan data "Keluarga", jangan lupa klik "<b>Simpan Data</b>"</i>
        </div>
    </div>

    <!-- modal form Jabatan -->
    <div class="modal fade" id="formPegawaiKeluargaModal" tabindex="-1" role="dialog" aria-labelledby="formPegawaiKeluargaModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="formPegawaiKeluargaModalTitle" v-text="formTitle">Keluarga Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Relasi</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formKeluarga.relasi" max="100" placeholder="Relasi">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Nama</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formKeluarga.nama" placeholder="Nama">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Jenis Kelamin</label>                            
                        <div class="col-sm-9">                                
                            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="formKeluarga.kelamin" :disabled="!isEditable">
                                <option selected>Pilih...</option>
                                <option value="0">Perempuan</option>
                                <option value="1">Laki-laki</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">TTL</label>                            
                        <div class="col-sm-9">                                
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tempat Lahir" v-model="formKeluarga.tempat_lahir" :disabled="!isEditable">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ion ion-md-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control datepicker-keluarga" id="keluarga-tanggal-lahir" onchange="vueFormPegawai.formKeluarga.tanggal_lahir = this.value" placeholder="Tanggal Lahir" v-model="formKeluarga.tanggal_lahir" :disabled="!isEditable">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="keluargaFormSubmitted">Save</button>
                </div>
            </div>
        </div>
    </div>

</div>