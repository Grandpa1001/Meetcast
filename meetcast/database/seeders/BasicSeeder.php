<?php

namespace Database\Seeders;

use App\Models\Basic;
use App\Enums\BasicGroupEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        #NFT like
        Basic::create([
            'name'=>'I love nft',
            'group'=>BasicGroupEnum::nft,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
        Basic::create([
            'name'=>'I don\'t love nft',
            'group'=>BasicGroupEnum::nft,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

         #Community
         $values= ['Wassie','Milady','Remilio'];
         foreach ($values as $key => $value) {
            Basic::create([
                'name' => $value,
                'group' => BasicGroupEnum::community,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
         }

          #dao
          $values= ['Optimism','Ethereum','Polygon'];
          foreach ($values as $key => $value) {
             Basic::create([
                 'name' => $value,
                 'group' => BasicGroupEnum::dao,
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);
          }





    }
}
