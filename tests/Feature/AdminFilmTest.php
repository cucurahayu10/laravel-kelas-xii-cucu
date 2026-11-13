<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFilmTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        view()->share('genres', Genre::all());
    }

    public function test_admin_routes_reject_guests_and_non_admin_users(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->createUser())
            ->get(route('admin.films'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    }

    public function test_admin_can_create_update_and_delete_a_film(): void
    {
        $admin = $this->createUser(true);
        $genre = Genre::create(['name' => 'Drama']);

        $this->actingAs($admin)
            ->get(route('admin.films.create'))
            ->assertOk()
            ->assertSee('Tambah Film Baru');

        $this->post(route('admin.films.store'), [
            'title' => 'Film Baru',
            'year' => 2025,
            'genre_id' => $genre->id,
            'sinopsis' => 'Sinopsis baru.',
            'poster' => 'storage/images/new.jpg',
        ])->assertRedirect(route('admin.film.cast', Film::where('title', 'Film Baru')->value('id')));

        $film = Film::where('title', 'Film Baru')->firstOrFail();
        $this->get(route('admin.films.edit', $film))
            ->assertOk()
            ->assertSee('Edit Film');

        $this->put(route('admin.films.update', $film), [
            'title' => 'Film Diperbarui',
            'year' => 2026,
            'genre_id' => $genre->id,
            'sinopsis' => 'Sinopsis diperbarui.',
            'poster' => 'storage/images/updated.jpg',
        ])->assertRedirect(route('admin.films'));

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Film Diperbarui',
            'year' => 2026,
        ]);

        $film->kritiks()->create([
            'comment' => 'Komentar yang ikut terhapus',
            'rating' => 0,
            'user_id' => $admin->id,
        ]);
        $this->delete(route('admin.films.destroy', $film))
            ->assertRedirect(route('admin.films'));
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
        $this->assertDatabaseMissing('kritiks', ['comment' => 'Komentar yang ikut terhapus']);
    }

    public function test_film_forms_validate_required_and_existing_fields(): void
    {
        $admin = $this->createUser(true);

        $this->actingAs($admin)
            ->from(route('admin.films.create'))
            ->post(route('admin.films.store'), [
                'title' => '',
                'year' => 1800,
                'genre_id' => 999,
            ])
            ->assertRedirect(route('admin.films.create'))
            ->assertSessionHasErrors(['title', 'year', 'genre_id']);
    }

    public function test_film_list_can_search_and_paginate_results(): void
    {
        $admin = $this->createUser(true);
        $genre = Genre::create(['name' => 'Drama']);

        foreach (range(1, 21) as $index) {
            $this->createFilm($genre, sprintf('Searchable Film %02d', $index));
        }

        $this->actingAs($admin)
            ->get(route('admin.films'))
            ->assertOk()
            ->assertSee('page=2', false);

        $this->get(route('admin.films', ['search' => 'Searchable Film 21']))
            ->assertOk()
            ->assertSee('Searchable Film 21')
            ->assertDontSee('Searchable Film 01');
    }

    public function test_admin_seeder_creates_a_repeatable_admin_account(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue(Hash::check('12345678', $admin->password));
        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
    }

    public function test_admin_can_search_and_delete_comments(): void
    {
        $admin = $this->createUser(true);
        $genre = Genre::create(['name' => 'Drama']);
        $film = $this->createFilm($genre, 'Film dengan komentar');
        $comment = $film->kritiks()->create([
            'comment' => 'Komentar untuk dikelola',
            'rating' => 0,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.comments', ['search' => 'Komentar untuk dikelola']))
            ->assertOk()
            ->assertSee($admin->name)
            ->assertSee($film->title)
            ->assertSee('Komentar untuk dikelola');

        $this->delete(route('admin.comments.destroy', $comment))
            ->assertRedirect(route('admin.comments'));
        $this->assertDatabaseMissing('kritiks', ['id' => $comment->id]);
    }

    public function test_dashboard_matches_admin_overview_and_routes_are_available(): void
    {
        $admin = $this->createUser(true);
        $user = $this->createUser();
        $genre = Genre::create(['name' => 'Drama']);
        $film = $this->createFilm($genre, 'Dashboard Film');
        $film->kritiks()->create([
            'comment' => 'Dashboard comment',
            'rating' => 0,
            'user_id' => $admin->id,
        ]);
        \App\Models\Cast::create([
            'name' => 'Cast Dashboard',
            'age' => 35,
            'bio' => 'Aktor pengujian.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Ringkasan')
            ->assertSee('Total Cast / Aktor')
            ->assertSee('Dashboard Film')
            ->assertSee('Dashboard comment')
            ->assertSee(route('admin.casts'))
            ->assertSee(route('admin.users'));

        $this->get(route('admin.casts'))->assertOk()->assertSee('Cast Dashboard');
        $this->get(route('admin.users'))->assertOk()->assertSee($user->name);
    }

    public function test_admin_can_delete_a_regular_user_but_not_an_admin(): void
    {
        $admin = $this->createUser(true);
        $user = $this->createUser();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        $this->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    private function createFilm(Genre $genre, string $title): Film
    {
        return Film::create([
            'title' => $title,
            'sinopsis' => 'Film untuk pengujian.',
            'year' => 2026,
            'poster' => 'storage/images/test.jpg',
            'genre_id' => $genre->id,
        ]);
    }

    private function createUser(bool $admin = false): User
    {
        $roleId = $admin ? 2 : 1;
        $role = Role::firstOrCreate(['id' => $roleId], [
            'name' => $admin ? 'admin' : 'user',
        ]);
        $profile = Profile::create([
            'umur' => 25,
            'bio' => 'Pengguna pengujian.',
            'alamat' => '-',
        ]);

        return User::create([
            'name' => $admin ? 'Admin Test' : 'User Test',
            'email' => $admin ? 'admin-test@example.com' : 'user-test@example.com',
            'password' => Hash::make('password123'),
            'role' => $admin ? 'admin' : 'user',
            'role_id' => $role->id,
            'profile_id' => $profile->id,
        ]);
    }
}
