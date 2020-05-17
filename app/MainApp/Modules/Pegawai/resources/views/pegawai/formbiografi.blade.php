<div class="card-body pb-2">
    <h5>Akun</h5>
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Userame</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.user.username" :disabled="!isEditable || isSatker">
        </div>
    </div>
    @if($mode!='view')
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Password</label>
        <div class="col-sm-8">
            <input type="password" class="form-control" v-model="form.user.password" min="6">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Konfirmasi Password</label>
        <div class="col-sm-8">
            <input type="password" class="form-control" v-model="form.user.password_confirmation" min="6">
            <i>Kosongkan password jika tidak akan diubah</i>
        </div>
    </div>
                   
    <div class="form-group text-right">                 
        <a href="javascript:void(0)" @click="submitData" class="btn btn-success" v-if="isProfile">
            Simpan Data
        </a>
    </div>
    @endif

    <hr>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Kode Induk</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.kode" :disabled="!isEditable || isSatker">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">NIK (No KTP)</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.ktp" :disabled="!isEditable">
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">NPWP</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.npwp" :disabled="!isEditable">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Nama</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.nama" :disabled="!isEditable || isSatker">
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Gelar Depan</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.gelar_depan" :disabled="!isEditable">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Gelar Belakang</label>                            
        <div class="col-sm-8">
            <input type="text" class="form-control" v-model="form.gelar_belakang" :disabled="!isEditable">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">TTL</label>                            
        <div class="col-sm-8">                                
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Tempat" v-model="form.tempat_lahir" :disabled="!isEditable">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="ion ion-md-calendar"></i></span>
                </div>
                <input type="text" class="form-control datepicker-base" onchange="vueFormPegawai.form.tanggal_lahir = this.value" placeholder="Tanggal Lahir" v-model="form.tanggal_lahir" :disabled="!isEditable">
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Golongan Darah</label>                            
        <div class="col-sm-8">                                
            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="form.golongan_darah" :disabled="!isEditable">
                <option value="" selected>Pilih...</option>
                <option value="O−">O−</option>
                <option value="O+">O+</option>
                <option value="A-">A-</option>
                <option value="A+">A+</option>
                <option value="B-">B-</option>
                <option value="B+">B+</option>
                <option value="AB-">AB-</option>
                <option value="AB+">AB+</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Jenis Kelamin</label>                            
        <div class="col-sm-8">                                
            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="form.kelamin" :disabled="!isEditable">
                <option selected>Pilih...</option>
                <option value="0">Perempuan</option>
                <option value="1">Laki-laki</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Status Pernikahan</label>                            
        <div class="col-sm-8">                                
            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="form.status_kawin_id" :disabled="!isEditable">
                <option value="0" selected>Pilih...</option>
                <option value="1">Kawin</option>
                <option value="2">Belum Kawin</option>
                <option value="3">Cerai</option>
                <option value="4">Janda/Duda</option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Agama</label>                            
        <div class="col-sm-8">                            
            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="form.agama_id" :disabled="!isEditable">
                <option value="0" selected>Pilih...</option>
                <option value="1">Islam</option>
                <option value="2">Kristen Katolik</option>
                <option value="3">Kristen Protestan</option>
                <option value="4">Hindu</option>
                <option value="5">Budha</option>
            </select>
        </div>
    </div>

    
    <div class="form-group row">
        <label class="col-form-label col-sm-2 text-sm-right">Tipe Pegawai</label>                            
        <div class="col-sm-8">                                
            <select class="custom-select mr-sm-2 mb-2 mb-sm-0" v-model="form.tipe" :disabled="!isEditable">
                <option value="0" selected>Pilih...</option>
                <option value="1">OSS</option>
                <option value="2">P2K</option>
                <option value="3">P2K BLU</option>
            </select>
        </div>
    </div>

</div>