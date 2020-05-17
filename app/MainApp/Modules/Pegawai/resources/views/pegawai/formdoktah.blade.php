<div class="card-body pb-2">
    <div class="mb-2 text-right">
        <a href="javascript:void(0)" @click="doktahShowForm(true,0)" class="btn btn-success btn-sm" v-if="isEditable">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Dokumen
        </a>
    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-striped table-bordered mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="min-width: 1rem;">Nama File</th>
                    <th style="min-width: 5rem;">Nama</th> 
                    <th style="min-width: 5rem;">Keterangan</th> 
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item,i,k) in form.doktah">
                    <td>@{{i+1}}</td>                        
                    <td>
                        <a target="_blank" :href="'{{Storage::url('/')}}' + item.filepath" v-if="!(item.filepath instanceof Object)">
                            @{{item.filename}}
                        </a>
                        <template v-else>
                            @{{item.filename}}
                        </template>
                    </td>
                    <td>@{{item.nama}}</td>
                    <td>@{{item.keterangan}}</td>
                    <td>                            
                        @if(\UserAuth::hasAccess('Pegawai.master','u'))
                        <div @click="doktahShowForm(false,i)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                            <i class="ion ion-md-create"></i>
                        </div> 
                        @endif
                        @if(\UserAuth::hasAccess('Pegawai.master','d'))
                        <div @click="doktahDeleteItem(i)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                            <i class="ion ion-md-close"></i>
                        </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <i class="float-right m-3"><b>*</b> untuk menyimpan hasil penambahan atau editan data "Doktah", jangan lupa klik "<b>Simpan Data</b>"</i>
    </div>

    
    <!-- modal form Jabatan -->
    <div class="modal fade" id="formPegawaiDoktahModal" tabindex="-1" role="dialog" aria-labelledby="formPegawaiDoktahModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="formPegawaiDoktahModalTitle" v-text="formTitle">Doktah Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">File</label>
                        <div class="col-sm-9">
                            <template v-if="formDoktah.filename">
                                <input type="text" class="form-control" max="199" v-model="formDoktah.filename" readonly="true">
                                <label class="btn btn-outline-primary btn-sm">
                                    Change
                                    <input type="file" @change="handleDoktahUpload" accept="*" class="inputFileDoktah user-edit-fileinput">
                                </label>
                            </template>                            
                            <input v-else @change="handleDoktahUpload" accept="*" type="file" class="inputFileDoktah form-control pb-2 h-100" placeholder="File">
                        </div>                        
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Nama</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" max="199" v-model="formDoktah.nama">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Keterangan</label>                            
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" v-model="formDoktah.keterangan"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="doktahFormSubmitted">Save</button>
                </div>
            </div>
        </div>
    </div>
    

</div>