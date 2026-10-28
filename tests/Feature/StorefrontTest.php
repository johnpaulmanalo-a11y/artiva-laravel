<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_shows_featured_artwork(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Art that');
        $response->assertSee(Artwork::query()->featured()->first()->title);
    }

    public function test_artwork_catalogue_can_be_searched_and_filtered(): void
    {
        $artwork = Artwork::query()->first();

        $response = $this->get('/artworks?q='.urlencode($artwork->title).'&min_price=1&sort=price_low');

        $response->assertOk();
        $response->assertSee($artwork->title);
    }

    public function test_artwork_detail_page_shows_price_and_licence(): void
    {
        $artwork = Artwork::query()->first();

        $response = $this->get('/artworks/'.$artwork->slug);

        $response->assertOk();
        $response->assertSee($artwork->title);
        $response->assertSee(money($artwork->price));
        $response->assertSee('Personal-use licence');
    }

    public function test_hidden_artwork_is_not_publicly_visible(): void
    {
        $artwork = Artwork::query()->first();
        $artwork->update(['is_active' => false]);

        $this->get('/artworks/'.$artwork->slug)->assertNotFound();
    }

    public function test_category_page_lists_its_artwork(): void
    {
        $category = Category::query()->first();
        $artwork = $category->artworks()->first();

        $response = $this->get('/categories/'.$category->slug);

        $response->assertOk();
        $response->assertSee($category->name);
        $response->assertSee($artwork->title);
    }

    public function test_contact_form_stores_a_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Licensing question',
            'message' => 'Could you explain the personal-use licence in more detail?',
        ]);

        $response->assertRedirect('/contact');
        $this->assertDatabaseHas('contact_messages', ['email' => 'visitor@example.com']);
    }
}
