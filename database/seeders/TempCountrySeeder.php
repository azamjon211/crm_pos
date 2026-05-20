<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TempCountrySeeder extends Seeder
{
    public function run(): void
    {
        // 1. GitHub dan JSON yuklab olish
        $this->command->info('GitHub dan JSON yuklab olinmoqda...');

        $url = 'https://github.com/dr5hn/countries-states-cities-database/blob/master/json/countries.json';

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ]
        ]);

        $json = file_get_contents($url, false, $context);

        if (!$json) {
            $this->command->error('JSON yuklab olinmadi!');
            return;
        }

        $countries = json_decode($json, true);
        $this->command->info(count($countries) . ' ta mamlakat topildi.');

        // 2. temp_countries ga insert
        $this->command->info('temp_countries ga yozilmoqda...');

        $rows = [];
        foreach ($countries as $c) {
            $rows[] = [
                'iso2'               => $c['iso2'],
                'iso3'               => $c['iso3']               ?? null,
                'numeric_code'       => $c['numeric_code']       ?? null,
                'phonecode'          => $c['phonecode']          ?? null,
                'capital'            => $c['capital']            ?? null,
                'native'             => $c['native']             ?? null,
                'nationality'        => $c['nationality']        ?? null,
                'tld'                => $c['tld']                ?? null,
                'population'         => $c['population']         ?? null,
                'gdp'                => $c['gdp']                ?? null,
                'area_sq_km'         => $c['area_sq_km']         ?? null,
                'region'             => $c['region']             ?? null,
                'region_id'          => $c['region_id']          ?? null,
                'subregion'          => $c['subregion']          ?? null,
                'subregion_id'       => $c['subregion_id']       ?? null,
                'postal_code_format' => $c['postal_code_format'] ?? null,
                'postal_code_regex'  => $c['postal_code_regex']  ?? null,
                'timezones'          => json_encode($c['timezones'] ?? []),
                'currency'           => json_encode([
                    'currency_code'   => $c['currency']          ?? null,
                    'currency_name'   => $c['currency_name']     ?? null,
                    'currency_symbol' => $c['currency_symbol']   ?? null,
                ]),
            ];
        }

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('temp_countries')->insert($chunk);
        }

        $this->command->info('temp_countries ga yozildi.');

        // 3. classifiers ni UPDATE
        $this->command->info('classifiers yangilanmoqda...');

        DB::statement("
            UPDATE classifiers c
            SET
                iso3               = t.iso3,
                numeric_code       = t.numeric_code,
                phonecode          = t.phonecode,
                capital            = t.capital,
                native             = t.native,
                nationality        = t.nationality,
                tld                = t.tld,
                population         = t.population,
                gdp                = t.gdp,
                area_sq_km         = t.area_sq_km,
                region             = t.region,
                region_id          = t.region_id,
                subregion          = t.subregion,
                subregion_id       = t.subregion_id,
                postal_code_format = t.postal_code_format,
                postal_code_regex  = t.postal_code_regex,
                timezones          = t.timezones,
                currency           = t.currency
            FROM temp_countries t
            WHERE c.code = t.iso2
              AND c.classifier_type = 'country'
        ");

        $this->command->info('classifiers yangilandi.');

        // 4. temp_countries ni o'chirish
        Schema::dropIfExists('temp_countries');

        $this->command->info('temp_countries o\'chirildi. Hammasi tayyor!');
    }
}
