<?php

namespace App\Actions\Posts;

use App\Models\Post;

class DeletePostAction
{
    public function execute(int $id): void
    {
        /** @var Post $post */
        $post = Post::query()->findOrFail($id);

        $post->tags()->detach();

        $post->delete();
    }
}
