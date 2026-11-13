@extends('admin.layout')

@section('page-title', 'Kelola Film')
@section('page-description', 'Cari, tambah, ubah, dan hapus data film.')
@section('content')
<div class="general-agileits-w3l">
  <div class="w3l-medile-movies-grids">
    <div class="movie-browse-agile">
      <div class="browse-agile-w3ls general-w3ls">
        <div class="tittle-head">
          <h4 class="latest-text">Kelola Film</h4>
          <div class="container">
            <div class="agileits-single-top">
              <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="active">Kelola Film</li>
              </ol>
            </div>
          </div>
        </div>

        <div class="container" style="margin-top: 25px; margin-bottom: 40px;">
          <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
            <div class="panel-heading clearfix" style="background-color: #f7f7f7; padding: 12px 15px;">
              <span style="font-weight: bold; font-size: 15px; line-height: 30px;">
                <i class="fa fa-film"></i> Daftar Film
              </span>
              <a href="{{ route('admin.films.create') }}" class="btn btn-sm pull-right" style="background-color: #FF8D1B; color: #fff; font-weight: bold;">
                <i class="fa fa-plus"></i> Tambah Film Baru
              </a>
            </div>
            <div class="panel-body" style="padding: 0;">
              <form action="{{ route('admin.films') }}" method="GET" class="form-inline" style="padding: 15px;">
                <div class="form-group">
                  <label class="sr-only" for="search">Cari film</label>
                  <input type="search" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Cari judul atau tahun">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                @if ($search !== '')
                  <a href="{{ route('admin.films') }}" class="btn btn-default">Reset</a>
                @endif
              </form>
              <div class="table-responsive">
              <table class="table table-striped table-hover" style="margin-bottom: 0;">
                <thead>
                  <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Judul Film</th>
                    <th style="width: 80px;">Tahun</th>
                    <th style="width: 100px;">Jumlah Cast</th>
                    <th style="width: 230px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($films as $film)
                    <tr>
                      <td>{{ $film->id }}</td>
                      <td>{{ $film->title }}</td>
                      <td>{{ $film->year }}</td>
                      <td>
                        <span class="label {{ $film->perans_count > 0 ? 'label-success' : 'label-default' }}">
                          {{ $film->perans_count }} cast
                        </span>
                      </td>
                      <td>
                        <a href="{{ route('admin.films.edit', $film) }}" class="btn btn-xs btn-warning">
                          <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.film.cast', $film->id) }}" class="btn btn-xs btn-primary">
                          <i class="fa fa-users"></i> Kelola Cast
                        </a>
                        <form action="{{ route('admin.films.destroy', $film) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus film &quot;{{ $film->title }}&quot; beserta cast dan komentarnya?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-danger">
                            <i class="fa fa-trash"></i> Hapus
                          </button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center" style="padding: 20px;">{{ $search !== '' ? 'Film tidak ditemukan' : 'Belum ada film' }}</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
              </div>
            </div>
          </div>

          {{-- Pagination --}}
          @if ($films->hasPages())
            <div class="text-center">
              {!! $films->links() !!}
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
