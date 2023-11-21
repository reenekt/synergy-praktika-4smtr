<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class CreateCommentAction
{
    public function execute(array $fields): Comment
    {
        /** @var Comment $comment */
        $comment = Comment::query()->create($fields);

        return $comment;
    }
}
