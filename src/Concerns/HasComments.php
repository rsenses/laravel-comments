<?php

declare(strict_types=1);

namespace Rsenses\Comments\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Rsenses\Comments\Contracts\IsComment;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasComments
{
    /** @return MorphMany<IsComment> */
    public function comments(): MorphMany
    {
        return $this->morphMany(config('comments.model'), 'commentable');
    }

    public function comment(string $content, ?Model $user = null, ?IsComment $parent = null): IsComment
    {
        return $this->comments()->create([
            'content' => $content,
            'user_id' => $user instanceof Model ? $user->getKey() : Auth::id(),
            'parent_id' => $parent?->getKey(),
        ]);
    }
}
