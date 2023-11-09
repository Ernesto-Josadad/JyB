<?php

/**
 * Código para el controlador OfflineController.
 * Este controlador actualiza el estado de un usuario y dispara un evento OfflineEvent.
 */

namespace App\Http\Controllers;

use App\Events\OfflineEvent;
use App\Models\User;
use Illuminate\Http\Request;

class OfflineController extends Controller
{
    /**
     * Actualiza el estado de un usuario y dispara un evento OfflineEvent.
     *
     * @param int $id El ID del usuario a actualizar.
     * @return void
     */

    public function __invoke($id)
    {
        User::where('id', $id)->update(['status' => 0]);

        event(new OfflineEvent(User::find($id)));
    }
}
