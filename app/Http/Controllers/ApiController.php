<?php

namespace App\Http\Controllers;

use App\Http\Resources\FixtureCollection;
use App\Http\Resources\FixtureResource;
use App\Models\Fixture;
use App\Models\Standing;
use App\Models\Team;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

class ApiController extends Controller
{

    public function resetLeague() {
        Artisan::call('migrate:fresh --seed');
        return true;
    }

    public function getStandings() {
        return Standing::with('team')
            ->orderBy('points', 'DESC')
            ->orderBy('gd', 'DESC')
            ->get();
    }

    public function getFixture() {
        $tempResults = Fixture::with('homeTeam', 'awayTeam')->get();
        $result = [];
        foreach ($tempResults as $item) {
            $result[$item['week']][] = $item;
        }
        return $result;
    }

    public function postUpdateMatch(Request $request) {
        if (gettype($request->home_team_goal) == 'integer' && gettype($request->away_team_goal) == 'integer') {
            if ($request->home_team_goal >= 0 && $request->home_team_goal <= 10 && $request->away_team_goal >= 0 && $request->away_team_goal <= 10) {
                $match = Fixture::find($request->id);
                $match->home_team_goal = $request->home_team_goal;
                $match->away_team_goal = $request->away_team_goal;
                $match->save();
            } else {
                return response()->json([
                    'title' => 'Wrong data range!',
                    'message' => 'Please enter number of goals between 0 and 10.'
                ], 400);
            }
        } else {
            return response()->json([
                'title' => 'Wrong data type!',
                'message' => 'Please enter data in number type.'
            ], 400);
        }
        $this->standingsCalc([$match->homeTeam->id, $match->awayTeam->id]);
        return $match;
    }

    public function weekMatchsCalc($results) {
        foreach ($results as $result) {
            if (!empty($result->home_team_goal) || !empty($result->away_team_goal)) {
                continue;
            }
            $matchResult = (object)$this->matchResultsCalc($result->homeTeam, $result->awayTeam);

            $result->home_team_goal = $matchResult->homeGoal;
            $result->away_team_goal = $matchResult->awayGoal;
            $result->save();

            $this->standingsCalc([$result->homeTeam->id, $result->awayTeam->id]);

        }

        return $results;
    }

    public function matchResultsCalc(Team $homeTeam, Team $awayTeam) {
        $homeGoal = 0;
        $awayGoal = 0;

        $homeAdvantage = 1.5806; // this rate taken from Home Advantage: Comparison between the Major European Football Leagues By Werlayne S. S. Leite

        $homeTeamPower = $homeTeam->power * $homeAdvantage;
        $awayTeamPower = $awayTeam->power;

        $probabilityRate = 4000;
        $homeTeamProbability = $homeTeamPower / $probabilityRate;
        $awayTeamProbability = $awayTeamPower / $probabilityRate;
        for ($i = 0; $i < 90; $i++) {
            if (mt_rand($homeTeamProbability, $probabilityRate) <= $homeTeamProbability * $probabilityRate) {
                $homeGoal++;
            }
            if (mt_rand($awayTeamProbability, $probabilityRate) <= $awayTeamProbability * $probabilityRate) {
                $awayGoal++;
            }
        }

        return [
            'homeGoal' => $homeGoal,
            'awayGoal' => $awayGoal,
        ];
    }

    public function postPlayLeague(Request $request) {
        foreach ($request->all() as $item) {
            if (gettype($item['power']) == 'integer') {
                if (($item['power'] < 0 || $item['power'] > 100)) {
                    return response()->json([
                        'title' => 'Wrong data range!',
                        'message' => 'Please enter data between 0 and 100.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'title' => 'Wrong data type!',
                    'message' => 'Please enter data as number.'
                ], 400);
            }
            $team = Team::find($item['id']);
            $team->power = $item['power'];
            $team->save();
        }
        return $this->weekMatchsCalc(Fixture::all());
    }

    public function postPlayWeek(Request $request) {
        foreach ($request->all() as $item) {
            if (gettype($item['power']) == 'integer') {
                if (($item['power'] < 0 || $item['power'] > 100)) {
                    return response()->json([
                        'title' => 'Wrong data range!',
                        'message' => 'Please enter data between 0 and 100.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'title' => 'Wrong data type!',
                    'message' => 'Please enter data as number.'
                ], 400);
            }
            $team = Team::find($item['id']);
            $team->power = $item['power'];
            $team->save();
        }
        $latestWeek = Fixture::whereNull('home_team_goal')->orderBy('week')->pluck('week')->first();

        $results = Fixture::where('week', $latestWeek)->get();
        return $this->weekMatchsCalc($results);
    }

    public function standingsCalc($teams) {
        foreach ($teams as $team) {
            $teamStanding = Team::find($team)->standing;
            $teamStanding->played = Fixture::where(function ($query) use ($team) {
                $query->where('home_team_id', $team)->orWhere('away_team_id', $team);
            })->whereNotNull('home_team_goal')->whereNotNull('away_team_goal')->count();
            $teamStanding->won = Fixture::where('home_team_id', $team)->whereColumn('home_team_goal', '>', 'away_team_goal')->count() + Fixture::where('away_team_id', $team)->whereColumn('away_team_goal', '>', 'home_team_goal')->count();
            $teamStanding->lost = Fixture::where('home_team_id', $team)->whereColumn('home_team_goal', '<', 'away_team_goal')->count() + Fixture::where('away_team_id', $team)->whereColumn('away_team_goal', '<', 'home_team_goal')->count();
            $teamStanding->drawn = $teamStanding->played - ($teamStanding->won + $teamStanding->lost);
            $teamStanding->gf = Fixture::where('home_team_id', $team)->sum('home_team_goal') + Fixture::where('away_team_id', $team)->sum('away_team_goal');
            $teamStanding->ga = Fixture::where('home_team_id', $team)->sum('away_team_goal') + Fixture::where('away_team_id', $team)->sum('home_team_goal');
            $teamStanding->gd = $teamStanding->gf - $teamStanding->ga;
            $teamStanding->points = ($teamStanding->won * 3) + ($teamStanding->drawn * 1);
            $teamStanding->save();
        }
    }

}
