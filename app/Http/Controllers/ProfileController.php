<?php

/**
 * ProfileController.php
 *
 * Este archivo contiene el controlador para las operaciones relacionadas con los perfiles de usuario.
 * El controlador incluye métodos para mostrar el perfil de un usuario, seguir a un usuario, dejar de seguir a un usuario,
 * comprobar si un usuario está siendo seguido y marcar las notificaciones como leídas.
 *
 * El controlador utiliza los modelos User, Followers, Posts y NotifyFollow para realizar las operaciones.
 * También utiliza la clase Request de Laravel para manejar las solicitudes HTTP y la clase Inertia para renderizar las vistas.
 */

namespace App\Http\Controllers;

use App\Models\Followers;
use App\Models\Posts;
use App\Models\User;
use App\Notifications\NotifyFollow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    private $user;
    private $followers;

    /**
     * Crea una nueva instancia del controlador.
     *
     * @param User $user
     * @param Followers $followers
     */

    public function __construct(User $user, Followers $followers)
    {
        $this->user = $user;
        $this->followers = $followers;

        /**
         * Crea una nueva instancia del controlador.
         *
         * @param User $user
         * @param Followers $followers
         */
    }

    public function index($nick_name)
    {
        $user = $this->user->where('nick_name', $nick_name)->first();

        $followers = $user->followers()->count();
        $followed = $this->followers->where('follower_id', $user->id)->count();
        $postsCount = $user->posts()->count();
        $posts = Posts::getPost($user->id, true);

        return Inertia::render('UserProfile/index', [
            'userProfile' => $user,
            'followers' => $followers,
            'followed' => $followed,
            'postsCount' => $postsCount,
            'posts' => $posts,
        ]);
    }

    /**
     * Sigue a un usuario.
     *
     * @param Request $request
     * @return mixed
     */

    public function followUser(Request $request)
    {
        $user = User::find($request->user_id);

        $user->notify(new NotifyFollow(auth()->user()));

        return $this->followers->follow($request->user_id);
    }

    /**
     * Deja de seguir a un usuario.
     *
     * @param Request $request
     * @return mixed
     */

    public function unFollow(Request $request)
    {
        $follow = $this->followers->where('user_id', $request->user_id)->where('follower_id', auth()->user()->id)->first();

        return $follow->delete();
    }

    /**
     * Comprueba si un usuario está siendo seguido.
     *
     * @param int $user_id
     * @return array
     */

    public function existsFollow($user_id)
    {
        return $this->followers->where('user_id', $user_id)->where('follower_id', auth()->user()->id)->exists()
            ? ['exists' => true] : ['exists' => false];
    }

    /**
     * Marca las notificaciones como leídas.
     *
     * @return mixed
     */

    public function markAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return $user->notifications;
    }
}
