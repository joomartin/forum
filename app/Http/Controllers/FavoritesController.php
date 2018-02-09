<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    /**
     * FavoritesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        $reply->favorite();

        if (request()->expectsJson()) {
            return response(['status' => 'Favorite created'], 200);
        }

        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
