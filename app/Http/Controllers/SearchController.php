<?php

/**
 * Controlador de búsqueda que permite buscar usuarios por su apodo y también obtener los usuarios seguidos por el usuario autenticado.
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * Busca usuarios por su apodo.
     *
     * @param  string  $nick_name  Apodo a buscar
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function search($nick_name)
    {
        return User::select('id', 'name', 'nick_name', 'profile_photo_path')->where('nick_name', 'like', '%' . $nick_name . '%')->get();
    }

    /**
     * Obtiene los usuarios seguidos por el usuario autenticado que coinciden con el apodo proporcionado.
     *
     * @param  string  $nick_name  Apodo a buscar
     * @return \Illuminate\Database\Eloquent\Collection
     */

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
