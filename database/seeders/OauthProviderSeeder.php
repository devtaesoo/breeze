<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $items = [[
            'id' => 1,
            'name' => '카카오톡',
            'slug' => 'kakao'
        ],[
            'id' => 2,
            'name' => '네이버',
            'slug' => 'naver'
        ],[
            'id' => 3,
            'name' => '페이스북',
            'slug' => 'facebook'
        ],[
            'id' => 4,
            'name' => '구글',
            'slug' => 'google'
        ],[
            'id' => 5,
            'name' => '애플',
            'slug' => 'apple'
        ]];

        foreach($items as $item){
            \App\Models\OAuthProvider::updateOrInsert(['id' => $item['id']], $item);
        }
    }
}
