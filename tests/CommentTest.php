<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Factories\UserFactory;
use Rsenses\Comments\Concerns\HasComments;
use Rsenses\Comments\Models\Comment;

class Post extends Model
{
    use HasComments;

    public static function booted(): void
    {
        Schema::dropIfExists('posts');
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
        });
    }
}

it('can be created', function (): void {
    $post = Post::create();
    $comment = $post->comment('Hello, world!');

    expect($comment->commentable)->toBeInstanceOf(Post::class);

    expect($comment)->toBeInstanceOf(Comment::class)
        ->and($comment->commentable_id)->toBeNumeric()
        ->and($comment->commentable_type)->toBeString()
        ->and($comment->content)->toBeString()
        ->and($comment->created_at)->toBeInstanceOf(Carbon::class)
        ->and($comment->updated_at)->toBeInstanceOf(Carbon::class);

    expect(array_keys($comment->toArray()))
        ->toEqual([
            'content',
            'user_id',
            'parent_id',
            'commentable_id',
            'commentable_type',
            'updated_at',
            'created_at',
            'id',
            'commentable',
        ]);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->getKey(),
        'content' => 'Hello, world!',
    ]);
});

it('can belong to a user', function (): void {
    $user = UserFactory::new()->create();

    $this->actingAs($user);

    $post = Post::create();
    $comment = $post->comment('Hello, world!');

    // expect($comment->user)->toBeInstanceOf(User::class);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->getKey(),
        'content' => 'Hello, world!',
        'user_id' => Auth::id(),
    ]);
});

it('can belong to another comment', function (): void {
    $post = Post::create();
    $parent = $post->comment('Hello, world!');
    $child = $post->comment('Thanks!', parent: $parent);

    expect($parent->children)->toBeInstanceOf(Collection::class);

    expect($child->parent)->toBeInstanceOf(Comment::class);

    $this->assertDatabaseHas('comments', [
        'id' => $child->getKey(),
        'content' => 'Thanks!',
        'parent_id' => $parent->getKey(),
    ]);
});
