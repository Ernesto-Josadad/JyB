<?php

/**
 * Controlador para gestionar publicaciones de un usuario.
 */

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Models\Comments;
use App\Models\Likes;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    private $post;
    private $comments;
    private $likes;

    /**
     * Constructor de la clase. Recibe instancias de los modelos Posts, Comments y Likes.
     */

    public function __construct(Posts $post, Comments $comments, Likes $likes)
    {
        $this->post = $post;
        $this->comments = $comments;
        $this->likes = $likes;
    }

    /**
     * Crea una nueva publicación.
     */

    public function createPost(ImageRequest $request)
    {
        try {
            DB::beginTransaction();

            $post = $this->post->createPost($request);

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Obtiene las publicaciones de un usuario autenticado.
     */

    public function getPosts()
    {
        return $this->post->getPost(Auth::id());
    }

    /**
     * Permite dar "me gusta" o "no me gusta" a una publicación.
     */

    public function likeOrDislike(Request $request)
    {
        try {

            $postId = $request->post_id;
            $userId = auth()->user()->id;

            if ($this->likes->where('post_id', $postId)->where('user_id', $userId)->exists()) {
                $this->likes->deleteLike($postId, $userId);

                return response()->json(['like' => false, 'likes' => $this->likes->where('post_id', $postId)->get()]);
            } else {
                $this->likes->like($postId, $userId);

                return response()->json(['like' => true, 'likes' => $this->likes->where('post_id', $postId)->get()]);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Agrega un comentario a una publicación.
     */

    public function comment(Request $request)
    {
        try {

            $comment = $this->comments->create($request->all());

            return $this->comments->with('user:id,name,nick_name,profile_photo_path')->where('id', $comment->id)->first();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
