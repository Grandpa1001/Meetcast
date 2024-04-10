<?php

namespace Database\Seeders;

use App\Models\Chains;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChainsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $chains = ['Ethereum','Bitcoin','Polygon','Aptos','Blas'];
        foreach ($chains as $key=>$value) {
            Chains::create([
                'name'=>$value
            ]);
        }
    }
}
