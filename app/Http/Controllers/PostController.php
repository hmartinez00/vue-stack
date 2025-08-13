<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Muestra la lista de posts.
     */
    public function index(Request $request)
    {
        $posts = Post::paginate(10); // Ajustamos la paginación

        return Inertia::render('Post/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo post.
     */
    public function create()
    {
        return Inertia::render('Post/Create');
    }

    /**
     * Almacena un nuevo post.
     */
    public function store(PostRequest $request): RedirectResponse
    {
        Post::create($request->validated());

        return Redirect::route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Muestra un post específico.
     */
    public function show(Post $post)
    {
        return Inertia::render('Post/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Muestra el formulario para editar un post.
     */
    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Actualiza un post.
     */
    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return Redirect::route('posts.index')
            ->with('success', 'Post updated successfully');
    }

    /**
     * Elimina un post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return Redirect::route('posts.index')
            ->with('success', 'Post deleted successfully');
    }
}
