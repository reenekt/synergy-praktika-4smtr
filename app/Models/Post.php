<?php

namespace App\Models;

use App\Enums\PostPrivateAccessStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id ID
 * @property string $title Заголовок
 * @property string $content Содержимое html
 * @property int $user_id ID автора поста
 * @property bool $is_private Является ли пост скрытым
 * @property Carbon $created_at
 * @property Carbon updated_at
 * @property-read User $user Автор
 * @property-read Collection<int,Tag>|Tag[] $tags Теги
 * @property-read Collection<int,Comment>|Comment[] $comments Комментарии
 * @method Builder public()
 * @method Builder private()
 * @method Builder privateAvailableFor(int $userId)
 * @method Builder forSubscriber(int $userId)
 * @method Builder forTag(int $tagId)
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'is_private',
    ];

    protected $attributes = [
        'is_private' => false,
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function privateAccessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'private_posts_access_users');
    }

    public function privateAccessApprovedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'private_posts_access_users')->where('status', PostPrivateAccessStatusEnum::APPROVED);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_private', false);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('is_private', true);
    }

    public function scopePrivateAvailableFor(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $query) use ($userId) {
            $query->where(function (Builder $query) use ($userId) {
                $query
                    ->where('is_private', true)
                    ->whereRelation('privateAccessApprovedUsers', function (Builder $query) use ($userId) {
                        $query->where((new User())->qualifyColumn('id'), $userId);
                    });
            })->orWhere(function (Builder $query) use ($userId) {
                $query
                    ->where('is_private', true)
                    ->where('user_id', $userId);
            });
        });
    }

    public function scopeForSubscriber(Builder $query, int $userId): Builder
    {
        return $query->whereIn(
            'user_id',
            Subscription::query()->select('target_id')->where('subscriber_id', $userId)
        );
    }

    public function scopeForTag(Builder $query, int $tagId): Builder
    {
        return $query->whereRelation('tags', (new Tag())->qualifyColumn('id'), $tagId);
    }
}
