<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Support\Arr;

class PatchPostAction
{
    public function execute(Post $post, array $fields): Post
    {
        $tags = Arr::pull($fields, 'tags');

        $post->update($fields);

        $post->tags()->sync($tags);

        return $post;
    }
}
