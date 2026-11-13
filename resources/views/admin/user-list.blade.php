@extends('admin.layout')

@section('page-title', 'Kelola User')
@section('page-description', 'Lihat dan kelola akun pengguna terdaftar.')

@section('content')
<section class="admin-panel">
  <div class="admin-panel-heading">
    <h2><i class="fa fa-user"></i> Pengguna Terdaftar</h2>
  </div>
  <form action="{{ route('admin.users') }}" method="GET" class="form-inline" style="padding: 16px;">
    <div class="form-group">
      <label class="sr-only" for="search">Cari pengguna</label>
      <input type="search" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Cari nama atau email">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
    @if ($search !== '')
      <a href="{{ route('admin.users') }}" class="btn btn-default">Reset</a>
    @endif
  </form>
  <div class="admin-table-wrap">
    <table class="table admin-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Tanggal Daftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td><strong>{{ $user->name }}</strong></td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
            <td>
              <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}? Komentarnya juga akan dihapus.')">
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
            <td colspan="4" class="admin-empty">{{ $search !== '' ? 'Pengguna tidak ditemukan.' : 'Belum ada pengguna.' }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@if ($users->hasPages())
  <div class="text-center">{!! $users->links() !!}</div>
@endif
@endsection
