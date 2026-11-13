@extends('admin.layout')

@section('page-title', 'Cast & Aktor')
@section('page-description', 'Lihat data cast dan film yang menggunakan setiap cast.')

@section('content')
<section class="admin-panel">
  <div class="admin-panel-heading">
    <h2><i class="fa fa-users"></i> Daftar Cast &amp; Aktor</h2>
    <a href="{{ route('admin.films') }}" class="admin-panel-link">Kelola Cast pada Film</a>
  </div>
  <form action="{{ route('admin.casts') }}" method="GET" class="form-inline" style="padding: 16px;">
    <div class="form-group">
      <label class="sr-only" for="search">Cari cast</label>
      <input type="search" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Cari nama atau bio cast">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
    @if ($search !== '')
      <a href="{{ route('admin.casts') }}" class="btn btn-default">Reset</a>
    @endif
  </form>
  <div class="admin-table-wrap">
    <table class="table admin-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Usia</th>
          <th>Bio</th>
          <th>Jumlah Film</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($casts as $cast)
          <tr>
            <td><strong>{{ $cast->name }}</strong></td>
            <td>{{ $cast->age }}</td>
            <td>{{ $cast->bio }}</td>
            <td><span class="admin-badge">{{ $cast->perans_count }} film</span></td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="admin-empty">{{ $search !== '' ? 'Cast tidak ditemukan.' : 'Belum ada data cast.' }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@if ($casts->hasPages())
  <div class="text-center">{!! $casts->links() !!}</div>
@endif
@endsection
