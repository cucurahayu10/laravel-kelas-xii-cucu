<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Models\Cast;
use App\Models\Peran;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard administrator.
     */
    public function dashboard()
    {
        $totalFilms = Film::count();
        $totalGenres = Genre::count();
        $totalCasts = Cast::count();
        $totalUsers = User::count();
        $totalKritiks = Kritik::count();

        $recentFilms = Film::with('genre:id,name')
            ->withCount('perans')
            ->latest()
            ->take(8)
            ->get();
        $recentKritiks = Kritik::with(['user:id,name', 'film:id,title'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalFilms',
            'totalGenres',
            'totalCasts',
            'totalUsers',
            'totalKritiks',
            'recentFilms',
            'recentKritiks'
        ));
    }

    /**
     * Tampilkan daftar cast dan aktor.
     */
    public function castList(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $casts = Cast::withCount('perans')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('bio', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.cast-list', compact('casts', 'search'));
    }

    /**
     * Tampilkan daftar akun pengguna non-admin.
     */
    public function userList(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $users = User::where(function ($query) {
            $query->whereNull('role')
                ->orWhere('role', '!=', 'admin');
        })
            ->where(function ($query) {
                $query->whereNull('role_id')
                    ->orWhere('role_id', '!=', 2);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.user-list', compact('users', 'search'));
    }

    /**
     * Hapus akun user non-admin dan komentarnya.
     */
    public function userDestroy(User $user)
    {
        if ($user->isAdmin() || $user->is(auth()->user())) {
            return redirect()->route('admin.users')
                ->with('error', 'Akun administrator tidak dapat dihapus dari halaman ini.');
        }

        DB::transaction(function () use ($user) {
            $user->kritiks()->delete();
            $profile = $user->profile;
            $user->delete();

            if ($profile && !User::where('profile_id', $profile->id)->exists()) {
                $profile->delete();
            }
        });

        return redirect()->route('admin.users')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Tampilkan form tambah film baru.
     */
    public function filmCreate()
    {
        $genres = Genre::all();
        $allCasts = Cast::orderBy('name')->get();

        return view('admin.film-create', compact('genres', 'allCasts'));
    }

    /**
     * Tampilkan komentar untuk dikelola administrator.
     */
    public function commentList(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $comments = Kritik::with(['user:id,name', 'film:id,title'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('comment', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('film', function ($filmQuery) use ($search) {
                            $filmQuery->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.comment-list', compact('comments', 'search'));
    }

    /**
     * Hapus komentar dari dashboard admin.
     */
    public function commentDestroy(Kritik $kritik)
    {
        $kritik->delete();

        return redirect()->route('admin.comments')
            ->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Tampilkan form edit film.
     */
    public function filmEdit(Film $film)
    {
        $genres = Genre::all();
        $allCasts = Cast::orderBy('name')->get();

        return view('admin.film-create', compact('film', 'genres', 'allCasts'));
    }

    /**
     * Simpan film baru beserta cast awal (opsional).
     */
    public function filmStore(Request $request)
    {
        $validated = $this->validateFilm($request);

        $film = Film::create([
            'title'    => $validated['title'],
            'year'     => $validated['year'],
            'genre_id' => $validated['genre_id'],
            'sinopsis' => $validated['sinopsis'] ?? '-',
            'poster'   => !empty($validated['poster']) ? $validated['poster'] : 'storage/images/c1.jpg',
        ]);

        if (!empty($validated['cast_id'])) {
            Peran::create([
                'film_id' => $film->id,
                'cast_id' => $validated['cast_id'],
                'actor'   => $validated['actor'] ?? 'Pemeran Utama',
            ]);
        }

        return redirect()->route('admin.film.cast', $film->id)
            ->with('success', 'Film "' . $film->title . '" berhasil ditambahkan! Anda dapat mengelola cast film di sini.');
    }

    /**
     * Perbarui data film.
     */
    public function filmUpdate(Request $request, Film $film)
    {
        $validated = $this->validateFilm($request);

        $film->update([
            'title'    => $validated['title'],
            'year'     => $validated['year'],
            'genre_id' => $validated['genre_id'],
            'sinopsis' => $validated['sinopsis'] ?? '-',
            'poster'   => $validated['poster'] ?: 'storage/images/c1.jpg',
        ]);

        if (!empty($validated['cast_id'])) {
            Peran::create([
                'film_id' => $film->id,
                'cast_id' => $validated['cast_id'],
                'actor'   => $validated['actor'] ?? 'Pemeran Utama',
            ]);
        }

        return redirect()->route('admin.films')
            ->with('success', 'Film "' . $film->title . '" berhasil diperbarui.');
    }

    /**
     * Validasi data film yang digunakan pada form tambah dan edit.
     */
    private function validateFilm(Request $request): array
    {
        return $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'year'     => ['required', 'integer', 'min:1900', 'max:2100'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sinopsis' => ['nullable', 'string'],
            'poster'   => ['nullable', 'string', 'max:2048'],
            'cast_id'  => ['nullable', 'exists:casts,id'],
            'actor'    => ['nullable', 'string', 'max:255'],
        ], [
            'title.required'    => 'Judul film wajib diisi.',
            'title.max'         => 'Judul film maksimal 255 karakter.',
            'year.required'     => 'Tahun rilis wajib diisi.',
            'year.integer'      => 'Tahun rilis harus berupa angka.',
            'year.min'          => 'Tahun rilis tidak boleh sebelum 1900.',
            'year.max'          => 'Tahun rilis tidak boleh setelah 2100.',
            'genre_id.required' => 'Genre wajib dipilih.',
            'genre_id.exists'   => 'Genre yang dipilih tidak valid.',
            'cast_id.exists'    => 'Cast yang dipilih tidak valid.',
        ]);
    }

    /**
     * Hapus film beserta data cast dan komentar terkait.
     */
    public function filmDestroy(Film $film)
    {
        DB::transaction(function () use ($film) {
            $film->kritiks()->delete();
            $film->perans()->delete();
            $film->delete();
        });

        return redirect()->route('admin.films')
            ->with('success', 'Film "' . $film->title . '" berhasil dihapus.');
    }

    /**
     * Tampilkan halaman kelola cast untuk film tertentu.
     */
    public function filmCast(Film $film)
    {
        $film->load(['perans.cast']);
        $allCasts = Cast::orderBy('name')->get();

        return view('admin.film-cast', compact('film', 'allCasts'));
    }

    /**
     * Tambah cast ke film (simpan ke tabel perans).
     */
    public function filmCastStore(Request $request, Film $film)
    {
        $validated = $request->validate([
            'cast_id' => ['required', 'exists:casts,id'],
            'actor'   => ['required', 'string', 'max:255'],
        ], [
            'cast_id.required' => 'Cast wajib dipilih.',
            'cast_id.exists'   => 'Cast yang dipilih tidak valid.',
            'actor.required'   => 'Nama peran/karakter wajib diisi.',
        ]);

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $validated['cast_id'],
            'actor'   => $validated['actor'],
        ]);

        return redirect()->route('admin.film.cast', $film->id)
            ->with('success', 'Cast berhasil ditambahkan ke film "' . $film->title . '".');
    }

    /**
     * Hapus cast dari film (hapus dari tabel perans).
     */
    public function filmCastDestroy(Film $film, Peran $peran)
    {
        if ($peran->film_id !== $film->id) {
            return redirect()->route('admin.film.cast', $film->id)
                ->with('error', 'Data peran tidak ditemukan untuk film ini.');
        }

        $castName = $peran->cast->name ?? 'Unknown';
        $peran->delete();

        return redirect()->route('admin.film.cast', $film->id)
            ->with('success', 'Cast "' . $castName . '" berhasil dihapus dari film "' . $film->title . '".');
    }

    /**
     * Tampilkan daftar semua film untuk kelola cast.
     */
    public function filmList(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $films = Film::withCount('perans')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('year', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('admin.film-list', compact('films', 'search'));
    }
}
