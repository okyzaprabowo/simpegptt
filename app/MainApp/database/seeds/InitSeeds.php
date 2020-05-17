<?php
namespace App\MainApp\database\seeds;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitSeeds extends Seeder
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
        DB::table('agama')->truncate();
        $jabatan = [
            [
                'nama'=>'Islam',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Kristen Katholik',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Kristen Protestan',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Hindu',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Budha',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($jabatan as $value) {
            DB::table('agama')->insert($value);
        }   

        /**
         * Status Kawin
         */ 
        $now = Now();
        DB::table('status_kawin')->truncate();
        $statusKawin = [
            [
                'nama'=>'Kawin',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Belum Kawin',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Cerai',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nama'=>'Janda/Duda',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($statusKawin as $value) {
            DB::table('status_kawin')->insert($value);
        }
        
    }
}
