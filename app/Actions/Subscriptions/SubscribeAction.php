<?php

namespace App\Actions\Subscriptions;

use App\Models\User;

class SubscribeAction
{
    public function execute(int $subscriber, int $target): void
    {
        /** @var User $subscriber */
        $subscriber = User::query()->findOrFail($subscriber);
        /** @var User $target */
        $target = User::query()->findOrFail($target);

        $target->subscribers()->attach($subscriber);
    }
}
