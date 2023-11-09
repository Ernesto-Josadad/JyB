<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($nick_name)
    {
        return User::select('id', 'name', 'nick_name', 'profile_photo_path')->where('nick_name', 'like', '%' . $nick_name . '%')->get();
    }

    public function usersIFollow($nick_name)
    {
        return User::select('id', 'name', 'nick_name', 'profile_photo_path')
            ->whereHas('followers', function (Builder $query) {
                $query->where('follower_id', auth()->user()->id);
            })
            ->where('nick_name', 'like', '%' . $nick_name . '%')
            ->get();
    }
}
