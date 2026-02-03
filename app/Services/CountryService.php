<?php

namespace App\Services;

use App\Models\Country;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryService
{

    public function getCountryByCode(string $code): ?Country
    {
        return Country::findByCode($code)->first();
    }

    public function getUsersForCountry(string $code): LengthAwarePaginator
    {
        $country = $this->getCountryByCode($code);

        if (!$country) {
            abort(404, 'Country not found');
        }

        return $this->getUsersByCountryId($country->id);
    }

    // ====== Helper Functions ======

    private function getUsersByCountryId(int $countryId): LengthAwarePaginator
    {
        return User::where('country_id', $countryId)->with('country')->paginate(1);
    }
}
