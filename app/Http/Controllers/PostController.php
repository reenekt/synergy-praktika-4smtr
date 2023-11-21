<?php

namespace App\Http\Controllers;

use App\Actions\Posts\CreatePostAction;
use App\Actions\Posts\DeletePostAction;
use App\Actions\Posts\GrantPrivatePostAccessAction;
use App\Actions\Posts\PatchPostAction;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\GrantPrivatePostAccessRequest;
use App\Http\Requests\Posts\PatchPostRequest;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Return Inertia::render('Posts/Index', [
            'pageTitle' => 'Публичные посты',
            'posts' => Post::query()
                ->public()
                ->with(['user.subscribers', 'tags', 'comments' => fn (Builder $query) => $query->latest(), 'comments.user'])
                ->latest()
                ->paginate(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexSubscribed(Request $request): Response
    {
        Return Inertia::render('Posts/Index', [
            'pageTitle' => 'Посты по подписке',
            'posts' => Post::query()
                ->public()
                ->forSubscriber($request->user()->id)
                ->with('user.subscribers', 'tags', 'comments.user')
                ->latest()
                ->paginate(),
            'hideSubscribeButton' => true,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexByTag(Request $request): Response
    {
        $tagId = $request->query('tag_id');

        Return Inertia::render('Posts/Index', [
            'pageTitle' => 'Публичные посты по тегу #' . Tag::query()->findOrFail($tagId)->name,
            'posts' => Post::query()
                ->public()
                ->forTag($tagId)
                ->with(['user.subscribers', 'tags', 'comments' => fn (Builder $query) => $query->latest(), 'comments.user'])
                ->latest()
                ->paginate(),
            'hideSubscribeButton' => true,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function indexPrivate(Request $request): Response
    {
        Return Inertia::render('Posts/Index', [
            'pageTitle' => 'Скрытые посты',
            'posts' => Post::query()
                ->privateAvailableFor($request->user()->id)
                ->with(['user.subscribers', 'tags', 'comments' => fn (Builder $query) => $query->latest(), 'comments.user'])
                ->latest()
                ->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Return Inertia::render('Posts/Create', [
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePostRequest $request, CreatePostAction $action): RedirectResponse
    {
        $model = $action->execute($request->validated());

        return to_route('posts.show', ['post' => $model->id]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function grandAccess(Post $post, GrantPrivatePostAccessRequest $request, GrantPrivatePostAccessAction $action): RedirectResponse
    {
        $action->execute($post, $request->getUserId());

        return back(fallback: '/');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Response
    {
        Return Inertia::render('Posts/Show', [
            'post' => $post->load(['tags', 'privateAccessApprovedUsers', 'comments' => fn (Builder $query) => $query->latest(), 'comments.user']),
            'users' => User::all(['id', 'name']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        Return Inertia::render('Posts/Edit', [
            'post' => $post->load('tags'),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, PatchPostRequest $request, PatchPostAction $action): RedirectResponse
    {
        $model = $action->execute($post, $request->validated());

        return to_route('posts.show', ['post' => $model->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, DeletePostAction $action): RedirectResponse
    {
        $action->execute($post->id);

        return to_route('posts.index');
    }
}
