<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtistProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_artist_directory_and_profile_show_their_public_artwork(): void
    {
        $artwork = Artwork::query()->firstOrFail();
        $artist = $artwork->artist;

        $this->get(route('artists.index'))
            ->assertOk()
            ->assertSee($artist->name);

        $this->get(route('artists.show', $artist))
            ->assertOk()
            ->assertSee($artist->name)
            ->assertSee($artwork->title)
            ->assertSee(route('artworks.show', $artwork));

        $this->get(route('artworks.show', $artwork))
            ->assertOk()
            ->assertSee(route('artists.show', $artist));
    }

    public function test_admin_can_create_artist_profile_with_a_photo_and_assign_artwork(): void
    {
        Storage::fake('public');
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $category = Category::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.artists.store'), [
                'name' => 'New Profile Artist',
                'bio' => 'An artist bio that describes their creative work.',
                'specialization' => 'Character Design',
                'portfolio_url' => 'https://example.com/portfolio',
                'photo' => UploadedFile::fake()->createWithContent(
                    'artist.png',
                    file_get_contents(public_path('images/logo.png')),
                ),
            ])
            ->assertRedirect(route('admin.artists.index'));

        $artist = Artist::query()->where('name', 'New Profile Artist')->firstOrFail();
        $this->assertNotNull($artist->photo_path);
        Storage::disk('public')->assertExists($artist->photo_path);

        $response = $this->actingAs($admin)
            ->post(route('admin.artworks.store'), [
                'title' => 'New Artist Artwork',
                'category_id' => $category->id,
                'artist_id' => $artist->id,
                'description' => 'A sample character created for artist assignment testing.',
                'price' => '25.00',
                'file_format' => 'PNG',
                'is_active' => '1',
            ]);

        $artwork = Artwork::query()->where('title', 'New Artist Artwork')->firstOrFail();
        $this->assertSame($artist->id, $artwork->artist_id);
        $response->assertRedirect(route('admin.artworks.show', $artwork));
    }

    public function test_admin_cannot_delete_an_artist_who_has_artworks(): void
    {
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $artist = Artwork::query()->firstOrFail()->artist;

        $this->actingAs($admin)
            ->delete(route('admin.artists.destroy', $artist))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('artists', ['id' => $artist->id]);
    }

    public function test_admin_can_search_update_and_delete_an_unassigned_artist(): void
    {
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $artist = Artist::query()->create([
            'name' => 'Searchable Artist',
            'slug' => Artist::uniqueSlug('Searchable Artist'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.artists.index', ['q' => 'Searchable']))
            ->assertOk()
            ->assertSee($artist->name);

        $this->put(route('admin.artists.update', $artist), [
            'name' => 'Updated Artist',
            'bio' => 'A concise biography for an updated artist profile.',
            'specialization' => 'Pixel Art',
            'portfolio_url' => 'https://example.com/updated',
        ])->assertRedirect(route('admin.artists.index'));

        $artist->refresh();
        $this->assertSame('Updated Artist', $artist->name);
        $this->assertSame('updated-artist', $artist->slug);

        $this->delete(route('admin.artists.destroy', $artist))
            ->assertRedirect(route('admin.artists.index'));

        $this->assertDatabaseMissing('artists', ['id' => $artist->id]);
    }

    public function test_customers_cannot_manage_artist_profiles(): void
    {
        $customer = User::query()->where('role', User::ROLE_CUSTOMER)->firstOrFail();

        $this->actingAs($customer)
            ->get(route('admin.artists.index'))
            ->assertForbidden();
    }
}
