<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                        'author_name',
                        'title',
                        'description',
                        'rating',
                    ],
                ],
            ]);
    }

    public function test_get_last_review(): void
    {
        Review::factory(3)
            ->for(User::factory()->set('name', 'Test Author'), relationship: 'author')
            ->create([
                'title' => 'Test Review',
                'description' => 'Test Description',
                'rating' => 5,
            ]);

        $response = $this->getJson('/api/reviews/last');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'author_name' => 'Test Author',
                    'title' => 'Test Review',
                    'description' => 'Test Description',
                    'rating' => 5,
                ],
            ]);
    }

    public function test_create_review(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/reviews', [
                'title' => 'Test Review',
                'description' => 'Test Description',
                'rating' => 5,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas(Review::class, [
            'author_id' => $user->id,
        ]);
    }

    public function test_get_specific_review(): void
    {
        $review = Review::factory()
            ->for(User::factory()->set('name', 'Test Author'), relationship: 'author')
            ->create([
                'title' => 'Test Review',
                'description' => 'Test Description',
                'rating' => 5,
            ]);

        $response = $this->getJson("/api/reviews/$review->id");

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'author_name' => 'Test Author',
                    'title' => 'Test Review',
                    'description' => 'Test Description',
                    'rating' => 5,
                ],
            ]);
    }

    public function test_update_review(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()
            ->for($user, relationship: 'author')
            ->create();

        $response = $this
            ->actingAs($user)
            ->putJson("/api/reviews/$review->id", [
                'title' => 'Test Review (edited)',
                'description' => 'Test Description (edited)',
                'rating' => 4,
            ]);

        $response->assertOk();

        $review->refresh();

        $this->assertEquals('Test Review (edited)', $review->title);
        $this->assertEquals('Test Description (edited)', $review->description);
        $this->assertEquals(4, $review->rating);
    }

    public function test_update_review_with_different_user(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()
            ->for(User::factory(), relationship: 'author')
            ->create();

        $response = $this
            ->actingAs($user)
            ->putJson("/api/reviews/$review->id", [
                'title' => 'Test Review (edited)',
                'description' => 'Test Description (edited)',
                'rating' => 4,
            ]);

        $response->assertForbidden();
    }

    public function test_delete_review(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()
            ->for($user, relationship: 'author')
            ->create();

        $response = $this
            ->actingAs($user)
            ->delete("/api/reviews/$review->id");

        $response->assertNoContent();

        $this->assertSoftDeleted($review);
    }

    public function test_delete_review_with_different_user(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()
            ->for(User::factory(), relationship: 'author')
            ->create();

        $response = $this
            ->actingAs($user)
            ->delete("/api/reviews/$review->id");

        $response->assertForbidden();
    }
}