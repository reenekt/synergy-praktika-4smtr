<?php

namespace App\Http\Controllers;

use App\Actions\Comments\CreateCommentAction;
use App\Actions\Comments\DeleteCommentAction;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, CreateCommentAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back(fallback: route('posts.show', ['post' => $request->validated('post_id')]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment, DeleteCommentAction $action): RedirectResponse
    {
        $action->execute($comment->id);

        return to_route('comments.index');
    }
}
