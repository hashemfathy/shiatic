<?php

namespace App\Http\Middleware;

use Auth;

use App\Models\Post;
use Closure;

class CheckPost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $post = Post::find($request->post->id);
        // !auth()->check() && (auth()->user() == $post->user
        // \Log::info(auth()->user()->id);
        if ($post->published == false) {
            if (auth()->check() && (auth()->user()->id == $post->user_id)) {
                return $next($request);
            }
            abort(403, 'forbidden action.');
        }
        return $next($request);
    }
}
