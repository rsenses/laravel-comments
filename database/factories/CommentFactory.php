<?php

declare(strict_types=1);

namespace Rsenses\Comments\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rsenses\Comments\Models\Comment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Rsenses\Comments\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'content' => fake()->words(rand(3, 10), asText: true),
        ];
    }
}
