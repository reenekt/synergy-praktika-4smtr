<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Support\Arr;

class CreatePostAction
{
    public function execute(array $fields): Post
    {
        $tags = Arr::pull($fields, 'tags');

        /** @var Post $post */
        $post = Post::query()->create($fields);

        $post->tags()->sync($tags);

        return $post;
    }
}
