<?php

namespace App\Services;

use App\Models\Country;
use App\Models\User;
use PhpParser\Node\Expr\BinaryOp\Equal;

class CountryService
{

    public function getCountryByCode(string $code): ?Country
    {
        return Country::findByCode($code)->first();
    }

    public function getUsersForCountry(string $code): array
    {
        $country = $this->getCountryByCode($code);

        if (!$country) {
            abort(404, 'Country not found');
        }

        return $this->getUsersByCountryId($country->id);
    }

    // ====== Helper Functions ======

    private function getUsersByCountryId(int $countryId): array
    {
        return User::where('country_id', $countryId)->with('country')->paginate(20);
    }
}
