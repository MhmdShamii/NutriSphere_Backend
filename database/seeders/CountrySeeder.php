<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries_data = file_get_contents(database_path('data/countries.json'));
        $countries = json_decode($countries_data, true);

        DB::table('countries')->truncate();
        $data = collect($countries)->map(function ($country) {
            return [
                'name'       => $country['name'],
                'code'       => $country['alpha-3'],
                'phone_code' => $country['phone_code'],
            ];
        })->toArray();

        Country::insert($data);
    }
}
