<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KritikTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_link_and_cannot_submit_a_comment(): void
    {
        $film = $this->createFilm();
        view()->share('genres', Genre::all());

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('Login untuk berkomentar')
            ->assertDontSee('name="comment"', false);

        $this->post(route('comments.store', $film), ['comment' => 'Komentar tamu'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('kritiks', ['comment' => 'Komentar tamu']);
    }

    public function test_authenticated_user_comment_uses_logged_in_user_id(): void
    {
        $film = $this->createFilm();
        $user = $this->createUser('user@example.com');
        $otherUser = $this->createUser('other@example.com');

        $this->actingAs($user)
            ->post(route('comments.store', $film), [
                'comment' => 'Komentar valid',
                'user_id' => $otherUser->id,
                'film_id' => 999,
            ])
            ->assertRedirect(route('movies.show', $film));

        $this->assertDatabaseHas('kritiks', [
            'comment' => 'Komentar valid',
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('kritiks', [
            'comment' => 'Komentar valid',
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_comment_is_required_and_limited_to_500_characters(): void
    {
        $film = $this->createFilm();
        $user = $this->createUser('user@example.com');

        $this->actingAs($user)
            ->from(route('movies.show', $film))
            ->post(route('comments.store', $film), ['comment' => ''])
            ->assertRedirect(route('movies.show', $film))
            ->assertSessionHasErrors('comment');

        $this->actingAs($user)
            ->from(route('movies.show', $film))
            ->post(route('comments.store', $film), ['comment' => str_repeat('a', 501)])
            ->assertRedirect(route('movies.show', $film))
            ->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('kritiks', ['film_id' => $film->id]);
    }

    public function test_login_after_selecting_commenting_returns_to_the_film(): void
    {
        $film = $this->createFilm();
        $user = $this->createUser('user@example.com');

        $this->get(route('login', ['redirect' => route('movies.show', $film)]))
            ->assertRedirect(route('home', ['auth' => 'login']));

        $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('movies.show', $film));
    }

    public function test_film_comments_show_user_name_and_relative_time(): void
    {
        $film = $this->createFilm();
        $user = $this->createUser('commenter@example.com');
        $film->kritiks()->create([
            'comment' => 'Komentar dengan nama pengguna',
            'rating' => 0,
            'user_id' => $user->id,
        ]);
        view()->share('genres', Genre::all());

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('Komentar dengan nama pengguna')
            ->assertSee($user->name)
            ->assertSee('ago');
    }

    public function test_unknown_comment_user_has_a_safe_fallback(): void
    {
        $film = $this->createFilm();
        view()->share('genres', Genre::all());
        $comment = new Kritik([
            'comment' => 'Komentar tanpa user valid',
            'user_id' => 999,
        ]);
        $comment->created_at = now();

        $response = view('components.movie-show', [
            'film' => $film,
            'filmByGenre' => collect(),
            'filmByRelease' => collect(),
            'comments' => collect([$comment]),
            'perans' => collect(),
        ])->render();

        $this->assertStringContainsString('Pengguna tidak dikenal', $response);
        $this->assertStringContainsString('Komentar tanpa user valid', $response);
    }

    public function test_admin_dashboard_shows_recent_comment_username(): void
    {
        $film = $this->createFilm();
        $user = $this->createUser('commenter@example.com');
        $admin = $this->createUser('admin@example.com', true);
        $film->kritiks()->create([
            'comment' => 'Komentar terbaru untuk admin',
            'rating' => 0,
            'user_id' => $user->id,
        ]);
        view()->share('genres', Genre::all());

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Komentar terbaru untuk admin')
            ->assertSee($user->name)
            ->assertSee($film->title);
    }

    private function createFilm(): Film
    {
        $genre = Genre::create(['name' => 'Drama']);

        return Film::create([
            'title' => 'Test Film',
            'sinopsis' => 'Film untuk pengujian.',
            'year' => 2026,
            'poster' => 'poster.jpg',
            'genre_id' => $genre->id,
        ]);
    }

    private function createUser(string $email, bool $isAdmin = false): User
    {
        $roleId = $isAdmin ? 2 : 1;
        $role = Role::firstOrCreate(['id' => $roleId], ['name' => $isAdmin ? 'admin' : 'user']);
        $profile = Profile::create([
            'umur' => 20,
            'bio' => 'Pengguna test.',
            'alamat' => '-',
        ]);

        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => $isAdmin ? 'admin' : 'user',
            'role_id' => $role->id,
            'profile_id' => $profile->id,
        ]);
    }
}
