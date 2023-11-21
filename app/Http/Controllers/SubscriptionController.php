<?php

namespace App\Http\Controllers;

use App\Actions\Subscriptions\SubscribeAction;
use App\Actions\Subscriptions\UnsubscribeAction;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\DestroySubscriptionByUsersRequest;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSubscriptionRequest $request, SubscribeAction $action): RedirectResponse
    {
        $action->execute(
            subscriber: $request->getSubscriberId(),
            target: $request->getTargetId(),
        );

        return back(fallback: '/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyByUsers(DestroySubscriptionByUsersRequest $request, UnsubscribeAction $action): RedirectResponse
    {
        $action->execute(
            subscriber: $request->getSubscriberId(),
            target: $request->getTargetId(),
        );

        return back(fallback: '/');
    }
}
