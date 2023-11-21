<?php

namespace App\Actions\Posts;

use App\Enums\PostPrivateAccessStatusEnum;
use App\Models\Post;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GrantPrivatePostAccessAction
{
    public function execute(Post $post, int $userId): void
    {
        if ($post->user_id !== auth()->user()->id) {
            throw new BadRequestHttpException('Нельзя дать доступ к чужому посту');
        }

        $post->privateAccessUsers()->attach($userId, ['status' => PostPrivateAccessStatusEnum::APPROVED]);
    }
}
