<?php

namespace Database\Seeders;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Seeder;

class FixtureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Fixture::truncate();
        $teams = Team::all()->shuffle()->toArray();
        $awayTeams = array_splice($teams, (count($teams) / 2));
        $homeTeams = $teams;
        for ($week = 1; $week < count($homeTeams) + count($awayTeams); $week++) {
            for ($j = 0; $j < count($homeTeams); $j++) {
                $fixture[$week][] = [
                    'home_team_id' => $homeTeams[$j]['id'],
                    'away_team_id' => $awayTeams[$j]['id'],
                ];
            }
            if (count($homeTeams) + count($awayTeams) - 1 > 2) {
                array_unshift($awayTeams, array_splice($homeTeams, 1, 1)[0]);
                array_push($homeTeams, array_pop($awayTeams));
            }
        }

        for ($i = 1; $i <= count($fixture); $i++) {
            foreach ($fixture[$i] as $match) {
                shuffle($match);
                Fixture::create([
                    'week' => $i,
                    'home_team_id' => $match[0],
                    'away_team_id' => $match[1],
                ]);

                Fixture::create([
                    'week' => count($fixture) + $i,
                    'home_team_id' => $match[1],
                    'away_team_id' => $match[0],
                ]);
            }
        }
    }
}
