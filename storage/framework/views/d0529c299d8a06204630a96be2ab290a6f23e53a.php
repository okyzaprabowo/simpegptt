<div class="card-body pb-2">
    <div class="mb-2 text-right">
        <a href="javascript:void(0)" @click="pendidikanShowForm(true,0)" class="btn btn-success btn-sm" v-if="isEditable">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Pendidikan
        </a>
    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-striped table-bordered mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="min-width: 1rem;">Tipe Pendidikan</th>
                    <th style="min-width: 1rem;">Tingkat Pendidikan<br><i class="font-weight-normal">(Khusus <b>Formal</b>)</i></th>
                    <th style="min-width: 5rem;">Institusi Pendidikan</th> 
                    <th style="min-width: 5rem;">Program Studi</th> 
                    <th style="">Tanggal Masuk</th> 
                    <th style="">Tanggal Lulus</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item,i,k) in form.pendidikan">
                    <td>{{i+1}}</td>                        
                    <td><span v-text="item.is_formal==1?'Formal':'Non formal'"></span></td>           
                    <td><span v-text="item.is_formal==1?item.tingkat:'-'"></span></td>
                    <td>{{item.nama_sekolah}}</td>
                    <td>{{item.program_studi}}</td>
                    <td>{{item.tanggal_masuk}}</td>
                    <td>{{item.tanggal_lulus}}</td>
                    <td>                            
                        <?php if(\UserAuth::hasAccess('Pegawai.master','u')): ?>
                        <div @click="pendidikanShowForm(false,i)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                            <i class="ion ion-md-create"></i>
                        </div> 
                        <?php endif; ?>
                        <?php if(\UserAuth::hasAccess('Pegawai.master','d')): ?>
                        <div @click="pendidikanDeleteItem(i)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                            <i class="ion ion-md-close"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <i class="float-right m-3"><b>*</b> untuk menyimpan hasil penambahan atau editan data "Pendidikan", jangan lupa klik "<b>Simpan Data</b>"</i>
    </div>

    
    <!-- modal form Jabatan -->
    <div class="modal fade" id="formPegawaiPendidikanModal" tabindex="-1" role="dialog" aria-labelledby="formPegawaiPendidikanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="formPegawaiPendidikanModalTitle" v-text="formTitle">Pendidikan Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Tipe</label>                             
                        <div class="col-sm-9">                        
                            <select class="form-control" style="width: 100%" onchange="vueFormPegawai.formPendidikan.is_formal = this.value" data-allow-clear="true">
                                <option value="1">Formal</option>
                                <option value="0">Non formal</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Nama Institusi</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formPendidikan.nama_sekolah" max="100" placeholder="Nama Institusi">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Program Studi</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formPendidikan.program_studi">
                        </div>
                    </div>
                    <div class="form-group row" v-if="formPendidikan.is_formal == 1">
                        <label class="col-form-label col-sm-3 text-sm-right">Tingkat</label>                            
                        <div class="col-sm-9">
                            <input type="text" class="form-control" v-model="formPendidikan.tingkat">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Tanggal Masuk</label>                            
                        <div class="col-sm-9">                                
                        <input type="text" class="form-control datepicker-pendidikan" id="pendidikan-tanggal-masuk" onchange="vueFormPegawai.formPendidikan.tanggal_masuk = this.value" placeholder="Tanggal Masuk" v-model="formPendidikan.tanggal_masuk" :disabled="!isEditable">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 text-sm-right">Tanggal Lulus</label>                            
                        <div class="col-sm-9">                                
                        <input type="text" class="form-control datepicker-pendidikan" id="pendidikan-tanggal-lulus" onchange="vueFormPegawai.formPendidikan.tanggal_lulus = this.value" placeholder="Tanggal Lulus" v-model="formPendidikan.tanggal_lulus" :disabled="!isEditable">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="pendidikanFormSubmitted">Save</button>
                </div>
            </div>
        </div>
    </div>
    

</div><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/Modules/Pegawai/resources/views/pegawai/formpendidikan.blade.php ENDPATH**/ ?>