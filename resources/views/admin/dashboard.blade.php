@extends('admin.layout')

@section('page-title', 'Dashboard Ringkasan')
@section('page-description', 'Pantau statistik film, cast, komentar, dan aktivitas terkini.')

@section('content')
<section class="admin-stat-grid" aria-label="Ringkasan statistik">
  <a href="{{ route('admin.films') }}" class="admin-stat-card">
    <span class="admin-stat-icon orange"><i class="fa fa-film"></i></span>
    <span>
      <span class="admin-stat-value">{{ $totalFilms }}</span>
      <span class="admin-stat-label">Total Film</span>
    </span>
  </a>
  <a href="{{ route('admin.casts') }}" class="admin-stat-card">
    <span class="admin-stat-icon blue"><i class="fa fa-users"></i></span>
    <span>
      <span class="admin-stat-value">{{ $totalCasts }}</span>
      <span class="admin-stat-label">Total Cast / Aktor</span>
    </span>
  </a>
  <div class="admin-stat-card">
    <span class="admin-stat-icon green"><i class="fa fa-tags"></i></span>
    <span>
      <span class="admin-stat-value">{{ $totalGenres }}</span>
      <span class="admin-stat-label">Total Genre</span>
    </span>
  </div>
  <a href="{{ route('admin.comments') }}" class="admin-stat-card">
    <span class="admin-stat-icon purple"><i class="fa fa-comments"></i></span>
    <span>
      <span class="admin-stat-value">{{ $totalKritiks }}</span>
      <span class="admin-stat-label">Total Ulasan / Komentar</span>
    </span>
  </a>
  <a href="{{ route('admin.users') }}" class="admin-stat-card">
    <span class="admin-stat-icon blue"><i class="fa fa-user"></i></span>
    <span>
      <span class="admin-stat-value">{{ $totalUsers }}</span>
      <span class="admin-stat-label">Pengguna Terdaftar</span>
    </span>
  </a>
</section>

<section class="admin-dashboard-columns">
  <div class="admin-panel">
    <div class="admin-panel-heading">
      <h2><i class="fa fa-film"></i> Film Terbaru</h2>
      <a href="{{ route('admin.films') }}" class="admin-panel-link">Lihat Semua ({{ $totalFilms }})</a>
    </div>
    <div class="admin-table-wrap">
      <table class="table admin-table">
        <thead>
          <tr>
            <th>Poster</th>
            <th>Judul</th>
            <th>Genre</th>
            <th>Tahun</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recentFilms as $film)
            <tr>
              <td>
                <img class="admin-film-poster" src="{{ asset($film->poster) }}" alt="Poster {{ $film->title }}" loading="lazy">
              </td>
              <td>
                <span class="admin-film-title">{{ $film->title }}</span>
                <span class="admin-film-description">{{ $film->sinopsis }}</span>
              </td>
              <td><span class="admin-badge">{{ $film->genre->name ?? 'Genre tidak tersedia' }}</span></td>
              <td><span class="admin-year">{{ $film->year }}</span></td>
              <td>
                <div class="admin-row-actions">
                  <a class="admin-icon-button" href="{{ route('admin.films.edit', $film) }}" title="Edit film" aria-label="Edit {{ $film->title }}">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <a class="admin-icon-button" href="{{ route('movies.show', $film) }}" title="Lihat film" aria-label="Lihat {{ $film->title }}">
                    <i class="fa fa-eye"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr><td class="admin-empty" colspan="5">Belum ada data film.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="admin-panel">
    <div class="admin-panel-heading">
      <h2><i class="fa fa-comments admin-heading-purple"></i> Komentar Terbaru</h2>
      <a href="{{ route('admin.comments') }}" class="admin-panel-link">Semua</a>
    </div>
    <div>
      @forelse ($recentKritiks as $kritik)
        <article class="admin-comment-item">
          <div class="admin-comment-author">
            <span>{{ $kritik->user->name ?? 'Pengguna tidak dikenal' }}</span>
            <small>{{ $kritik->created_at?->diffForHumans() ?? '-' }}</small>
          </div>
          <div class="admin-comment-film">
            pada film:
            @if ($kritik->film)
              <a href="{{ route('movies.show', $kritik->film) }}">{{ $kritik->film->title }}</a>
            @else
              <span>Film tidak ditemukan</span>
            @endif
          </div>
          <div class="admin-comment-text">"{{ $kritik->comment }}"</div>
        </article>
      @empty
        <div class="admin-empty">Belum ada komentar.</div>
      @endforelse
    </div>
  </div>
</section>
@endsection
