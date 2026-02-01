<?php

namespace App\Services;

use App\Models\Country;


class CountryService
{

    public function getCountries(): array
    {
        return Country::all()->toArray();
    }

    public function getCountryByCode(string $code): ?Country
    {
        return Country::findByCode($code)->first();
    }
}
