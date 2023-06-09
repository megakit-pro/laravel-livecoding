<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_reviews(): void
    {
        Review::factory(3)->for(User::factory(), relationship: 'author')->create();

        $response = $this->getJson('/api/reviews');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'author_name',
                        'title',
                        'body',
                        'rating',
                    ],
                ],
            ]);
    }

    public function test_get_latest_review(): void
    {
        $reviews = Review::factory(3)
            ->for(User::factory()->set('name', 'Test Author'), relationship: 'author')
            ->create([
                'title' => 'Test Review',
                'body' => 'Test Body',
                'rating' => 5,
            ]);

        $response = $this->getJson('/api/reviews/latest');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $reviews->last()->id,
                    'author_name' => 'Test Author',
                    'title' => 'Test Review',
                    'body' => 'Test Body',
                    'rating' => 5,
                ],
            ]);
    }

    public function test_create_review(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/reviews', [
            'title' => 'Test Review',
            'body' => 'Test Body',
            'rating' => 5,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas(Review::class, [
            'author_id' => $user->id,
        ]);
    }

    public function test_create_review_with_forbidden_words(): void
    {
        /** @var string[] $forbiddenWords */
        $forbiddenWords = config('reviews.forbidden_words');

        if (empty($forbiddenWords)) {
            $this->markTestSkipped('No forbidden words in config/reviews.php');
        }

        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/reviews', [
            'title' => 'title with forbidden word: ' . $forbiddenWords[0],
            'body' => 'Test Body',
            'rating' => 5,
        ]);

        $response->assertBadRequest();
    }

    public function test_get_specific_review(): void
    {
        $review = Review::factory()
            ->for(User::factory()->set('name', 'Test Author'), relationship: 'author')
            ->create([
                'title' => 'Test Review',
                'body' => 'Test Body',
                'rating' => 5,
            ]);

        $response = $this->getJson("/api/reviews/$review->id");

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $review->id,
                    'author_name' => 'Test Author',
                    'title' => 'Test Review',
                    'body' => 'Test Body',
                    'rating' => 5,
                ],
            ]);
    }

    public function test_update_review(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $review = Review::factory()
            ->for($user, relationship: 'author')
            ->create();

        $response = $this->putJson("/api/reviews/$review->id", [
            'title' => 'Test Review (edited)',
            'body' => 'Test Body (edited)',
            'rating' => 4,
        ]);

        $response->assertOk();

        $review->refresh();

        $this->assertEquals('Test Review (edited)', $review->title);
        $this->assertEquals('Test Body (edited)', $review->body);
        $this->assertEquals(4, $review->rating);
    }

    public function test_update_review_with_forbidden_words(): void
    {
        /** @var string[] $forbiddenWords */
        $forbiddenWords = config('reviews.forbidden_words');

        if (empty($forbiddenWords)) {
            $this->markTestSkipped('No forbidden words in config/reviews.php');
        }

        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $review = Review::factory()
            ->for($user, relationship: 'author')
            ->create();

        $response = $this->putJson("/api/reviews/$review->id", [
            'title' => 'title with forbidden word: ' . $forbiddenWords[0],
            'body' => 'Test Body (edited)',
            'rating' => 4,
        ]);

        $response->assertBadRequest();
    }

    public function test_update_review_with_different_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $review = Review::factory()
            ->for(User::factory(), relationship: 'author')
            ->create();

        $response = $this->putJson("/api/reviews/$review->id", [
            'title' => 'Test Review (edited)',
            'body' => 'Test Body (edited)',
            'rating' => 4,
        ]);

        $response->assertForbidden();
    }

    public function test_delete_review(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $review = Review::factory()
            ->for($user, relationship: 'author')
            ->create();

        $response = $this->delete("/api/reviews/$review->id");

        $response->assertNoContent();

        $this->assertSoftDeleted($review);
    }

    public function test_delete_review_with_different_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $review = Review::factory()
            ->for(User::factory(), relationship: 'author')
            ->create();

        $response = $this->delete("/api/reviews/$review->id");

        $response->assertForbidden();
    }
}
