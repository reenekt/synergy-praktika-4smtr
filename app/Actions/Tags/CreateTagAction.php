<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use Illuminate\Support\Arr;

class CreateTagAction
{
    public function execute(array $fields): Tag
    {
        /** @var Tag $tag */
        $tag = Tag::query()->create($fields);

        return $tag;
    }
}
