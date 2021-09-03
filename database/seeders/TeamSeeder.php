<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = [
            [
                'name' => 'Manchester United',
                'logo' => 'img/manu.png',
            ],
            [
                'name' => 'Liverpool FC',
                'logo' => 'img/liv.png',
            ],
            [
                'name' => 'Arsenal FC',
                'logo' => 'img/ars.png',
            ],
            [
                'name' => 'Everton FC',
                'logo' => 'img/evr.png',
            ],
        ];

        foreach ($teams as $team) {
            $club = Team::create($team);
            $club->standing()->create();
        }
    }
}
