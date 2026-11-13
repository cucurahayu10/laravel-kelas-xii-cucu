@extends('admin.layout')

@php($isEditing = isset($film))
@section('page-title', $isEditing ? 'Edit Film' : 'Tambah Film')
@section('page-description', 'Lengkapi informasi film dengan data yang benar.')
@section('content')
<div class="general-agileits-w3l">
  <div class="w3l-medile-movies-grids">
    <div class="movie-browse-agile">
      <div class="browse-agile-w3ls general-w3ls">
        <div class="tittle-head">
          <h4 class="latest-text">{{ $isEditing ? 'Edit Film' : 'Tambah Film Baru' }}</h4>
          <div class="container">
            <div class="agileits-single-top">
              <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li><a href="{{ route('admin.films') }}">Kelola Film</a></li>
                <li class="active">{{ $isEditing ? 'Edit Film' : 'Tambah Film' }}</li>
              </ol>
            </div>
          </div>
        </div>

        <div class="container" style="margin-top: 25px; margin-bottom: 40px; max-width: 800px;">
          <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
            <div class="panel-heading" style="font-weight: bold; background-color: #FF8D1B; color: #fff; font-size: 16px;">
              <i class="fa fa-film"></i> {{ $isEditing ? 'Form Edit Film' : 'Form Tambah Film & Pilihan Cast' }}
            </div>
            <div class="panel-body" style="padding: 25px;">

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul style="margin: 0; padding-left: 15px;">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form action="{{ $isEditing ? route('admin.films.update', $film) : route('admin.films.store') }}" method="POST">
                @csrf
                @if ($isEditing)
                  @method('PUT')
                @endif

                <div class="form-group">
                  <label for="title">Judul Film <span class="text-danger">*</span></label>
                  <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $film->title ?? '') }}" placeholder="Masukkan judul film" required>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="year">Tahun Rilis <span class="text-danger">*</span></label>
                      <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $film->year ?? date('Y')) }}" min="1900" max="2100" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="genre_id">Genre Film <span class="text-danger">*</span></label>
                      <select name="genre_id" id="genre_id" class="form-control" required>
                        <option value="">-- Pilih Genre --</option>
                        @foreach ($genres as $genre)
                          <option value="{{ $genre->id }}" {{ old('genre_id', $film->genre_id ?? '') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="poster">Path Poster (Opsional)</label>
                  <input type="text" name="poster" id="poster" class="form-control" value="{{ old('poster', $film->poster ?? 'storage/images/c1.jpg') }}" placeholder="Contoh: storage/images/c1.jpg">
                </div>

                <div class="form-group">
                  <label for="sinopsis">Sinopsis</label>
                  <textarea name="sinopsis" id="sinopsis" rows="4" class="form-control" placeholder="Tuliskan ringkasan sinopsis film...">{{ old('sinopsis', $film->sinopsis ?? '') }}</textarea>
                </div>

                <hr style="margin: 25px 0;">

                @if (!$isEditing)
                <div class="panel panel-info" style="border-radius: 4px;">
                  <div class="panel-heading" style="font-weight: bold; background-color: #f0f7fd;">
                    <i class="fa fa-user-plus"></i> Tambah Cast Awal (Opsional)
                  </div>
                  <div class="panel-body">
                    <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                      Anda bisa langsung memilih aktor/cast dan perannya, atau menambahkannya nanti setelah film disimpan.
                    </p>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="cast_id">Pilih Cast</label>
                          <select name="cast_id" id="cast_id" class="form-control">
                            <option value="">-- Tanpa Cast Dulu --</option>
                            @foreach ($allCasts as $cast)
                              <option value="{{ $cast->id }}" {{ old('cast_id') == $cast->id ? 'selected' : '' }}>
                                {{ $cast->name }} (Usia: {{ $cast->age }})
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="actor">Peran / Karakter</label>
                          <input type="text" name="actor" id="actor" class="form-control" value="{{ old('actor') }}" placeholder="Contoh: Tokoh Utama, Antagonis">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif

                <div style="margin-top: 20px;">
                  <button type="submit" class="btn" style="background-color: #FF8D1B; color: #fff; font-weight: bold; padding: 10px 25px;">
                    <i class="fa fa-save"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Film & Kelola Cast' }}
                  </button>
                  <a href="{{ route('admin.films') }}" class="btn btn-default" style="padding: 10px 20px; margin-left: 10px;">
                    Batal
                  </a>
                </div>

              </form>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
