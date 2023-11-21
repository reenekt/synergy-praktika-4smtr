<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class DeleteCommentAction
{
    public function execute(int $id): void
    {
        Comment::destroy($id);
    }
}
