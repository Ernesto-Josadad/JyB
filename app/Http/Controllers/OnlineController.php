<?php

/**
 * Actualiza el estado de un usuario a "en línea" y dispara un evento en línea.
 *
 * @param int $id El ID del usuario.
 * @return void
 */

namespace App\Http\Controllers;

use App\Events\OnlineEvent;
use App\Models\User;
use Illuminate\Http\Request;

class OnlineController extends Controller
{
    public function __invoke($id)
    {
        User::where('id', $id)->update(['status' => 1]);

        event(new OnlineEvent(User::find($id)));
    }
}
