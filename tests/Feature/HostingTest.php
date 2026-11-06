<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('Accept-Language', 'fr');
    }

    protected function tearDown(): void
    {
        Image::all()->each->delete();
        parent::tearDown();
    }

    private function user(bool $admin = false): User
    {
        return User::create(['name' => $admin ? 'admin' : 'ami', 'password' => 'secret123', 'is_admin' => $admin]);
    }

    public function test_first_account_setup_only_when_empty(): void
    {
        $this->get('/login')->assertSee('crée le compte admin');
        $this->post('/setup', ['name' => 'moi', 'password' => 'secret123'])->assertRedirect('/');
        $this->assertTrue(User::first()->is_admin);

        auth()->logout();
        $this->get('/login')->assertDontSee('crée le compte admin');
        $this->post('/setup', ['name' => 'pirate', 'password' => 'secret123'])->assertForbidden();
    }

    public function test_language_switch(): void
    {
        $this->get('/login', ['Accept-Language' => 'en-US,en;q=0.9'])->assertSee('create the admin account');

        $this->get('/lang/fr');
        $this->get('/login', ['Accept-Language' => 'en'])->assertSee('crée le compte admin');

        $this->get('/lang/en');
        $this->actingAs($this->user())->get('/')->assertSee('Drop your images here')->assertSee('Log out');
        $this->post('/admin/users', [])->assertForbidden();
        $this->get('/lang/de')->assertNotFound();
    }

    public function test_validation_messages_are_translated(): void
    {
        $this->user(true);
        $admin = User::where('is_admin', true)->first();

        $this->actingAs($admin)->withSession(['locale' => 'fr'])->post('/admin/users', ['name' => 'admin', 'password' => '123'])
            ->assertSessionHasErrors(['name' => 'Ce nom est déjà pris.', 'password' => 'Le champ mot de passe doit faire au moins 6 caractères.']);

        $this->withSession(['locale' => 'en'])->post('/admin/users', ['name' => 'admin', 'password' => '123'])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);

        $this->withHeader('Accept-Language', 'en')->post('/api/images')->assertUnauthorized()->assertJson(['message' => 'Invalid token']);
    }

    public function test_guest_is_redirected_and_can_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->user();

        $this->post('/login', ['name' => 'ami', 'password' => 'faux'])->assertSessionHasErrors('name');
        $this->post('/login', ['name' => 'ami', 'password' => 'secret123'])->assertRedirect('/');
        $this->get('/')->assertOk()->assertSee('Glisse tes images ici');
    }

    public function test_web_upload_returns_html(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->postJson('/images', [
            'images' => [UploadedFile::fake()->image('a.png'), UploadedFile::fake()->image('b.jpg')],
        ])->assertOk();

        $this->assertCount(2, $user->images);
        $this->assertSame(2, substr_count($response->json('html'), '<img src='));
        $this->assertFileExists($user->images->first()->path());
    }

    public function test_rejects_non_images(): void
    {
        $this->actingAs($this->user())->postJson('/images', [
            'images' => [UploadedFile::fake()->create('x.php', 1, 'text/x-php')],
        ])->assertUnprocessable();
    }

    public function test_api_upload_list_and_delete(): void
    {
        $user = $this->user();
        $token = $user->regenerateToken();

        $this->post('/api/images', ['image' => UploadedFile::fake()->image('a.png')])->assertUnauthorized();

        $html = $this->withToken($token)->post('/api/images', [
            'images' => [UploadedFile::fake()->image('a.png'), UploadedFile::fake()->image('b.png')],
        ])->assertCreated()->assertHeader('Content-Type', 'text/html; charset=UTF-8')->getContent();
        $this->assertSame(2, substr_count($html, '<img src='));

        $this->withToken($token)->post('/api/images?format=json', ['image' => UploadedFile::fake()->image('c.png')])
            ->assertCreated()->assertJsonCount(1, 'images');

        $list = $this->withToken($token)->getJson('/api/images')->assertOk()->assertJsonCount(3);

        $image = Image::find($list->json('0.id'));
        $this->withToken($token)->delete('/api/images/'.$image->id)->assertNoContent();
        $this->assertFileDoesNotExist($image->path());
    }

    public function test_cannot_delete_someone_elses_image(): void
    {
        $owner = $this->user(true);
        $image = Image::store($owner, UploadedFile::fake()->image('a.png'));

        $this->actingAs($this->user())->delete('/images/'.$image->id)->assertForbidden();
    }

    public function test_admin_panel(): void
    {
        $this->actingAs($this->user())->get('/admin')->assertForbidden();

        $admin = $this->user(true);
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->post('/admin/users', ['name' => 'pote', 'password' => 'motdepasse'])->assertSessionHasNoErrors();

        $pote = User::where('name', 'pote')->first();
        $this->assertFalse($pote->is_admin);

        $this->delete('/admin/users/'.$pote->id);
        $this->assertModelMissing($pote);
        $this->delete('/admin/users/'.$admin->id)->assertStatus(422);
    }
}
