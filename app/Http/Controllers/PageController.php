<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Possibledate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class PageController extends Controller
{
    public function dashboard(Request $request)
    {
        $games = Game::query()
            ->where(function($query) {
                $query->whereNull('date')
                    ->orWhere('date', '>', now());
            })
            ->orderBy('date', 'desc')
            ->get();
        return Inertia::render('Dashboard', compact('games'));
    }

    public function newGame(Request $request)
    {
        if ($request->isMethod('POST')) {
            $game_data = $request->all();
            $game_data['user_id'] = $request->user()->id;
            Game::create($game_data);
        }
        return Inertia::render('NewGame');
    }

    public function editGame(Request $request, Game $game)
    {
        if ($request->isMethod('POST')) {
            $game_data = $request->all();
            $game->update($game_data);
        }
        return Inertia::render('EditGame', compact('game'));
    }

    public function signup(Request $request, Game $game)
    {
        $game->players()->attach($request->user()->id);
        return redirect()->to('/game/'.$game->id);
    }

    public function signout(Request $request, Game $game)
    {
        $game->players()->detach($request->user()->id);
        return redirect()->to('/game/'.$game->id);
    }

    public function addDate(Request $request, Game $game)
    {
        $date = $request->get('date');
        Possibledate::create([
            'date' => $date,
            'game_id' => $game->id,
        ]);
        return redirect()->to('/game/'.$game->id);
    }

    public function toggleDate(Request $request, Possibledate $date)
    {
        $user_ids = $date->users->map(function($user) {
           return $user->id;
        });
        $current_user = $request->user()->id;
        if ($user_ids->contains($current_user)) {
            $date->users()->detach($current_user);
        } else {
            $date->users()->attach($current_user);
        }
        return redirect()->to('/game/'.$date->game->id);
    }

    public function deleteGame(Request $request, Game $game)
    {
        $game->delete();
        return redirect()->to('/dashboard');
    }
}
