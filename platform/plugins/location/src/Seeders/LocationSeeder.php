<?php

namespace Botble\Location\Seeders;

use Botble\Base\Models\MetaBox;
use Botble\Language\Models\LanguageMeta;
use Botble\Location\Models\City;
use Botble\Location\Models\CityTranslation;
use Botble\Location\Models\Country;
use Botble\Location\Models\CountryTranslation;
use Botble\Location\Models\State;
use Botble\Location\Models\StateTranslation;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            LanguageMeta::whereIn('reference_type', [City::class, State::class, Country::class])->delete();
        }

        City::truncate();
        State::truncate();
        Country::truncate();
        CityTranslation::truncate();
        StateTranslation::truncate();
        CountryTranslation::truncate();
        MetaBox::whereIn('reference_type', [City::class, State::class, Country::class])->delete();

        $countries = [
            'us' => [
                'id'          => 1,
                'name'        => 'United States of America',
                'nationality' => 'Americans',
                'is_default'  => 1,
                'status'      => 'published',
                'order'       => 0,
            ],
            'ca' => [
                'id'          => 2,
                'name'        => 'Canada',
                'nationality' => 'Canada',
                'is_default'  => 0,
                'status'      => 'published',
                'order'       => 1,
            ],
            'vn' => [
                'id'          => 3,
                'name'        => 'Việt Nam',
                'nationality' => 'Vietnam',
                'is_default'  => 0,
                'status'      => 'published',
                'order'       => 2,
            ],
        ];

        foreach ($countries as $countryCode => $countryItem) {
            $country = Country::create($countryItem);

            $states = file_get_contents(__DIR__ . '/../../database/files/' . $countryCode . '/states.json');
            $states = json_decode($states, true);
            foreach ($states as $state) {
                $state['country_id'] = $country->id;

                State::create($state);
            }

            $cities = file_get_contents(__DIR__ . '/../../database/files/' . $countryCode . '/cities.json');
            $cities = json_decode($cities, true);
            foreach ($cities as $item) {

                $state = State::where('name', $item['name'])->first();
                if (!$state) {
                    continue;
                }

                foreach ($item['cities'] as $cityName) {
                    $city = [
                        'name'       => $cityName,
                        'state_id'   => $state->id,
                        'country_id' => $country->id,
                    ];

                    City::create($city);
                }
            }
        }
    }
}
