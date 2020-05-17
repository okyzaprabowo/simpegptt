<?php

namespace App\MainApp\Modules\Laporan\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Kepegawaian;
use App\Base\BaseController;
use Facades\App\MainApp\Repositories\Absensi;
use Facades\App\MainApp\Repositories\Master;
use Carbon\CarbonPeriod;

class KehadiranHarianController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Laporan.kehadiranharian')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }        

        $this->response = 'laporan.kehadiran_harian';
        
        $this->output['data']['bulanList'] = [
            '',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $this->output['data']['tanggal_start'] = now()->subMonth()->format('Y-m-d');
        $this->output['data']['tanggal_end'] = now()->format('Y-m-d');
        
        if($request->input('tanggal_start',false)){
            $this->output['data']['tanggal_start'] = $request->input('tanggal_start');
        }
        
        if($request->input('tanggal_end',false)){
            $this->output['data']['tanggal_end'] = $request->input('tanggal_end');
        }
        // Absensi::addTime('18:00:00','15:12:00');
        //--------------------------
        $orderBy = false;
        $filter =  ['q'=>$request->input('q', null)];//untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null),
                'tanggal_start' => $this->output['data']['tanggal_start'],
                'tanggal_end' => $this->output['data']['tanggal_end']
            ];
        $this->output['data']['q'] = $filter['q'];
        
        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);

        
        if(UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            
            $instansiId = UserAuth::user('profile')['instansi_id'];

        }else if(UserAuth::getActiveUserRoleCode()=='admin_satker' || UserAuth::getActiveUserRoleCode()=='pimpinan2'){
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[1] = eselon 2, jika tidak ada berarti eselon 2 adalah instansi yg dipilih
                if(isset($instansiId[1])){
                    $instansiId = $instansiId[1];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }else if(UserAuth::getActiveUserRoleCode()=='pimpinan3') {
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[2] == eselon 3, jika tidak ada berarti eselon 3 adalah instansi yg dipilih
                if(isset($instansiId[2])){
                    $instansiId = $instansiId[2];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }

        if(isset($instansiId)){
            $filter[] = [['instansi_id', $instansiId],['OR instansi_induk_path', 'LIKE', '%;'.$instansiId.';%']];
        }

        // $this->output['data']['pegawai'] = Kepegawaian::listPegawai(
        //     $filter, $limit['offset'], $limit['limit'],$orderBy
        // );

        $filter['tanggal_start'] = $this->output['data']['tanggal_start'];
        $filter['tanggal_end'] = $this->output['data']['tanggal_end'];

        $this->output['data']['pegawai'] = Kepegawaian::listPegawaiAbsensi(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );
        
        $pegawaiIds = [];
        foreach($this->output['data']['pegawai']['data'] as $peg) {
            $pegawaiIds[] = $peg['id'];
        }
        
        $this->output['data']['absensi'] = Absensi::rekapAbsensi(
            $pegawaiIds,$this->output['data']['tanggal_start'], $this->output['data']['tanggal_end']
        );
                
        $jenisIjin = Master::listJenisIjin();
        $this->output['data']['jenisIjin'] = [];
        $this->output['data']['jenisIjinKategori'] = [0=>['jenisIjin'=>[],'kategori'=>[]]];
        foreach($jenisIjin['data'] as $val) {
            $this->output['data']['jenisIjin'][$val['id']] = $val;
            
            if(!isset($this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]))
                $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']] = [
                    'jenisIjin'=>[],
                    'kategori'=>$val['kategori']
                ];
            $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]['jenisIjin'][$val['id']] = $val;
        }
        
        $this->output['data']['weekMap'] = [
            0 => 'M',
            1 => 'S',
            2 => 'S',
            3 => 'R',
            4 => 'K',
            5 => 'J',
            6 => 'S',
        ];
        $this->output['data']['periode'] = CarbonPeriod::create($this->output['data']['tanggal_start'], $this->output['data']['tanggal_end']);

        //generate pagination
        $this->output['viewdata']['pagination'] = Kepegawaian::getPaginationPegawai(route('laporan.kehadiran_harian', $paginationParams));
        
        return $this->done();
    }

    public function index2(Request $request)
    {
        $this->response = 'laporan.kehadiran_harian';

        $orderBy = false;
        $filter = //untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);

        $this->output['data'] = Kepegawaian::listPegawai(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Kepegawaian::getPaginationPegawai(route('laporan.kehadiran_harian', $paginationParams));
        $period = CarbonPeriod::create('2018-06-14', '2018-06-20');
        return $this->done();
    }
    
    /**
     * @param Request $request
     *      status    
     *      is_pegawai
     */
    public function readList(Request $request) {
        
        $filter = [
            'q'=>$request->input('q', null)
        ];

        //jika menyertakan status
        if($request->input('status', null))
            $filter[] = ['status', $request->input('status')];

        if($request->input('is_pegawai', null))
            $filter['profile'] = [['is_pegawai', $request->input('is_pegawai')]];

        if(UserAuth::isLogin()){
            $filter[] = ['id','!=',UserAuth::user('id')];
            $filter[] = ['level','>=',UserAuth::user('level')];
        }            
        
        $this->output['data']['offset'] = $request->input('offset', 0);
        $this->output['data']['limit'] = $request->input('limit', 0);

        $this->output['data'] = UserRepo::listUser(
            $filter, $this->output['data']['offset'], $this->output['data']['limit']
        );

        return $this->done();
    }
    
    public function readOne(Request $request)
    {
        $id = $request->route('id');
        $this->output['data'] = UserRepo::getUser($id);
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['id']);
        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 

        return $this->done();
    }

    /**
     * 
     */
    public function create(Request $request)
    {
        $userData = $request->all();//$request->only(['name', 'email', 'password']);

        $validator = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:5|max:255'
        ];
        if(isset($userData['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        $validator = \Validator::make($userData, $validator);

        
        if(isset($userData['profile']['jabatan_id'])){
            $jabatan = Pegawai::getJabatan($userData['profile']['jabatan_id']);
            if($jabatan){
                $userData['role_code'] = $jabatan['role_code'];
            }
        }

        if ($validator->fails()) {       
            $this->setError(__('validation.inputerror'),$validator->messages());
            return $this->done();
        }

        if($request->file('profile.ttd',null)){
            $userData['profile']['ttd'] = $request->file('profile.ttd')->store('images/ttd');       
        }else{
            $userData['profile']['ttd'] = '';
        }

        //jika berhasil
        if (UserRepo::register($userData,false)) {            
            $this->setAlert('Data Inserted Successfully');
        }else{
            //jika sudah upload ttd maka delete
            if($userData['profile']['ttd']!=''){
                \Illuminate\Support\Facades\Storage::delete($userData['profile']['ttd']);
            }
            $this->setError(UserRepo::error());
        }        
        
        return $this->done();
    }


    public function update(Request $request)
    {
        $id = $request->route('id');

        $input = $request->all();

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }

        if(isset($input['profile']['jabatan_id'])){
            $jabatan = Pegawai::getJabatan($input['profile']['jabatan_id']);
            if($jabatan){
                $input['role_code'] = $jabatan['role_code'];
            }
        }

        if(isset($input['profile']['is_pegawai']) && $input['profile']['is_pegawai'] == 0){
            $input['profile']['jabatan_id'] = 0;
            $input['profile']['divisi_id'] = 0;
        }
        if($request->file('profile.ttd',null)){
            $input['profile']['ttd'] = $request->file('profile.ttd')->store('images/ttd'); 
            
        }else{
            if(array_key_exists('ttd',$input['profile']))unset($input['profile']['ttd']);            
        }
        $oldData = UserRepo::getUser($id);

        if(UserRepo::updateUser($id, $input)) {
            
            //jika ttd diubah dan sebelumnya ada ttd, makah hapus ttd sebelumnya
            if(isset($input['profile']['ttd']) && $oldData['profile']['ttd']!='' && $input['profile']['ttd']){
                \Illuminate\Support\Facades\Storage::delete($oldData['profile']['ttd']);
            }
            $this->setAlert('Data Updated successfully','success');
        }else{
            //jika melakukan update ttd, maka hapus
            if($input['profile']['ttd']!=''){
                \Illuminate\Support\Facades\Storage::delete($input['profile']['ttd']);
            }
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        } 

        return $this->done();
    }
    
    /**
     * update password
     * 
     * @param Request $request
     *      password
     *      password_confirmatin
     */
    public function updatePassword(Request $request)
    {
        $id = $request->route('id');
        $userData = $request->all();

        $validator = \Validator::make($userData, [
            'password' => 'required|min:8|max:255',
            'password_confirmation' => 'required|min:8|max:255|same:password'
        ]);

        if ($validator->fails()) {
            $this->setError('Input Error :',$validator->messages(),400,true);
            return $this->done();
        }

        $change = UserRepo::updateUser($id, $userData);
        if (!$change) {
            return $this->done();
        }

        $this->setAlert('Password Updated successfully','success');
        return $this->done();
    }

    public function suspend(Request $request)
    {
        $id = $request->route('id');

        UserRepo::suspendUser($id);
        return $this->done();
    }

    public function delete(Request $request)
    {
        $id = $request->route('id');
           
        if(UserAuth::isLogin() && $id != UserAuth::user('id')){
            $filter[] = ['id', $id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = UserRepo::listUser($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();;
            }
        }   

        if(!UserRepo::deleteUser($id)){
            $this->setError('Error : '.UserRepo::error());
        }
        return $this->done();
    }

    public function divisiList(Request $request) {
        
        $this->output['data'] = Pegawai::listDivisi();

        return $this->done();
    }
    public function jabatanList(Request $request) {
        
        $this->output['data'] = Pegawai::listJabatan();
        
        return $this->done();
    }
}
