<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // CreateDataUxuiSettingAppVariabelSeeder::class,
            // CreateDataUserDefaultSeeder::class,
            // CreateDataGroupUserAksesSeeder::class,
            // CreateDataJenisJadwalSeeder::class,

            CreateUserKaryawan::class,
        ]);
    }
}
