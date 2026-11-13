@extends('admin.layout')

@section('page-title', 'Kelola Cast Film')
@section('page-description', 'Atur pemeran untuk ' . $film->title . '.')
@section('content')
<div class="general-agileits-w3l">
  <div class="w3l-medile-movies-grids">
    <div class="movie-browse-agile">
      <div class="browse-agile-w3ls general-w3ls">
        <div class="tittle-head">
          <h4 class="latest-text">Kelola Cast — {{ $film->title }}</h4>
          <div class="container">
            <div class="agileits-single-top">
              <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li><a href="{{ route('admin.films') }}">Film</a></li>
                <li class="active">Cast — {{ $film->title }}</li>
              </ol>
            </div>
          </div>
        </div>

        <div class="container" style="margin-top: 25px; margin-bottom: 40px;">
          <div class="row">
            {{-- Kolom Kiri: Form Tambah Cast --}}
            <div class="col-md-5" style="margin-bottom: 25px;">
              <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
                <div class="panel-heading" style="font-weight: bold; background-color: #FF8D1B; color: #fff; font-size: 15px;">
                  <i class="fa fa-plus"></i> Tambah Cast ke Film
                </div>
                <div class="panel-body">

                  @if ($errors->any())
                    <div class="alert alert-danger" style="font-size: 13px;">
                      <ul style="margin: 0; padding-left: 15px;">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif

                  <form action="{{ route('admin.film.cast.store', $film->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                      <label for="cast_id">Pilih Cast / Aktor</label>
                      <select name="cast_id" id="cast_id" class="form-control" required>
                        <option value="">-- Pilih Cast --</option>
                        @foreach ($allCasts as $cast)
                          <option value="{{ $cast->id }}" {{ old('cast_id') == $cast->id ? 'selected' : '' }}>
                            {{ $cast->name }} (Usia: {{ $cast->age }})
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="actor">Nama Peran / Karakter</label>
                      <input type="text" name="actor" id="actor" class="form-control" value="{{ old('actor') }}" placeholder="Contoh: Inspektur Polisi, Dokter, dll." required>
                    </div>

                    <button type="submit" class="btn btn-block" style="background-color: #FF8D1B; color: #fff; font-weight: bold;">
                      <i class="fa fa-plus"></i> Tambah Cast
                    </button>
                  </form>
                </div>
              </div>

              {{-- Info Film --}}
              <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
                <div class="panel-heading" style="font-weight: bold; background-color: #f7f7f7; font-size: 15px;">
                  <i class="fa fa-info-circle"></i> Info Film
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-xs-4">
                      <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 100%; border-radius: 4px;">
                    </div>
                    <div class="col-xs-8">
                      <p><strong>Judul:</strong> {{ $film->title }}</p>
                      <p><strong>Tahun:</strong> {{ $film->year }}</p>
                      <p><strong>Jumlah Cast:</strong> {{ $film->perans->count() }}</p>
                      <a href="{{ route('movies.show', $film->id) }}" class="btn btn-xs btn-default">
                        <i class="fa fa-eye"></i> Lihat di situs
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Kolom Kanan: Daftar Cast yang Sudah Ada --}}
            <div class="col-md-7" style="margin-bottom: 25px;">
              <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
                <div class="panel-heading" style="font-weight: bold; background-color: #f7f7f7; font-size: 15px;">
                  <i class="fa fa-users"></i> Daftar Cast Film ({{ $film->perans->count() }})
                </div>
                <div class="panel-body" style="padding: 0;">
                  <table class="table table-striped table-hover" style="margin-bottom: 0;">
                    <thead>
                      <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Cast / Aktor</th>
                        <th>Peran / Karakter</th>
                        <th style="width: 80px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($film->perans as $index => $peran)
                        <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>{{ $peran->cast->name ?? 'Cast tidak ditemukan' }}</td>
                          <td>{{ $peran->actor ?: '-' }}</td>
                          <td>
                            <form action="{{ route('admin.film.cast.destroy', [$film->id, $peran->id]) }}" method="POST" onsubmit="return confirm('Hapus cast &quot;{{ $peran->cast->name ?? '' }}&quot; dari film ini?')">
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
                          <td colspan="4" class="text-center" style="padding: 20px; font-style: italic; color: #777;">
                            Data cast belum tersedia untuk film ini
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
