<?php
namespace App\MainApp\database\seeds;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAndRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {       
        /**
         * set jabatan
         */ 
        $now = Now();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('user_roles')->truncate();
        
        DB::table('roles')->insert([
            [
                'role_code' => 'superadmin',
                'name' => 'Super Admin',
                'level' => 1,
                'rule' => file_get_contents(app_path('MainApp/config/acl/superadmin.json')),
                'created_at' => $now,
                'updated_at' => $now
            ], 
            [
                'role_code' => 'admin',
                'name' => 'Administrator',
                'level' => 2,
                'rule' => file_get_contents(app_path('MainApp/config/acl/admin.json')),
                'created_at' => $now,
                'updated_at' => $now
            ], 
            [
                'role_code' => 'staf_admin',
                'name' => 'Staf Admin',
                'level' => 3,
                'rule' => file_get_contents(app_path('MainApp/config/acl/staf_admin.json')),
                'created_at' => $now,
                'updated_at' => $now
            ], 
            [
                'role_code' => 'admin_satker',
                'name' => 'Admin Satker',
                'level' => 3,
                'rule' => file_get_contents(app_path('MainApp/config/acl/admin_satker.json')),
                'created_at' => $now,
                'updated_at' => $now
            ], 
            [
                'role_code' => 'pejabat_approval',
                'name' => 'Pejabat Approval ',
                'level' => 4,
                'rule' => file_get_contents(app_path('MainApp/config/acl/pejabat_approval.json')),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'role_code' => 'pimpinan',
                'name' => 'Pimpinan Eselon 1',
                'level' => 5,
                'rule' => file_get_contents(app_path('MainApp/config/acl/pimpinan.json')),
                'created_at' => $now,
                'updated_at' => $now
            ], 
            [
                'role_code' => 'pegawai_ptt',
                'name' => 'Pegawai PTT',
                'level' => 6,
                'rule' => file_get_contents(app_path('MainApp/config/acl/pegawai_ptt.json')),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'role_code' => 'pimpinan2',
                'name' => 'Pimpinan Eselon 2',
                'level' => 5,
                'rule' => file_get_contents(app_path('MainApp/config/acl/pimpinan2.json')),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'role_code' => 'pimpinan3',
                'name' => 'Pimpinan Eselon 3',
                'level' => 5,
                'rule' => file_get_contents(app_path('MainApp/config/acl/pimpinan3.json')),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        $users = [
            [
                'users' => [
                    'user_idcode' => '2019010110000001',
                    'name' => 'Super Admin',
                    'username' => 'superadmin',
                    'email' => 'superadmin@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';superadmin;',
                    'level' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'user_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 1,
                    'role_id' => 1,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'user_idcode' => '2019010110000002',
                    'name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';admin;',
                    'level' => 2,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'user_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 2,
                    'role_id' => 2,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'user_idcode' => '2019010110000003',
                    'name' => 'Staf Admin',
                    'username' => 'staf_admin',
                    'email' => 'staf_admin@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';staf_admin;',
                    'level' => 3,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'user_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 3,
                    'role_id' => 3,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'user_idcode' => '2019010110000004',
                    'name' => 'Pejabat Approval',
                    'username' => 'pejabat_approval',
                    'email' => 'pejabat_approval@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';pejabat_approval;',
                    'level' => 4,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'user_id' => 4,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 4,
                    'role_id' => 4,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'user_idcode' => '2019010110000005',
                    'name' => 'Pimpinan',
                    'username' => 'pimpinan',
                    'email' => 'pimpinan@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';pimpinan;',
                    'level' => 5,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'user_id' => 5,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 5,
                    'role_id' => 5,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]
        ];

        foreach ($users as $key => $value) {
            DB::table('users')->insert($value['users']);
            DB::table('user_profiles')->insert($value['user_profiles']);
            DB::table('user_roles')->insert($value['user_roles']);
        }

        $pegawaiData = DB::table('pegawai')->get();

        foreach ($pegawaiData as $key => $value) {
            $userId = DB::table('users')->insertGetId([
                'user_idcode' => $value->kode,
                'name' => $value->nama,
                'username' => $value->kode,
                'email' => '',
                'password' => Hash::make($value->kode),
                'role' => ';pegawai_ptt;',
                'level' => 6,
                'created_at' => $now,
                'updated_at' => $now
            ]);
            DB::table('user_profiles')->insert([
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now
            ]);
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => 6,
                'has_auth_grant' => 1,
                'is_main_role' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            DB::table('pegawai')
              ->where('id', $value->id)
              ->update(['user_id' => $userId]);
        }
        
    }
}
