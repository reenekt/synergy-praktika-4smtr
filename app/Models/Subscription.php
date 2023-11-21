<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id ID
 * @property int $subscriber_id Пользователь, который подписался
 * @property int $target_id Пользователь, на которого подписался
 * @property Carbon $created_at
 * @property Carbon updated_at
 * @property-read User $subscriber Пользователь, который подписался
 * @property-read User $target Пользователь, на которого подписался
 */
class Subscription extends Pivot
{
    use HasFactory;

    protected $table = 'subscriptions';

    public $incrementing = true;

    protected $fillable = ['subscriber_id', 'target_id'];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'subscriber_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'target_id');
    }
}
