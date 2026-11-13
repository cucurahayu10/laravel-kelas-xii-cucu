@extends('admin.layout')

@section('page-title', 'Kelola Komentar')
@section('page-description', 'Pantau dan moderasi komentar dari pengguna.')
@section('content')
<div class="general-agileits-w3l">
  <div class="w3l-medile-movies-grids">
    <div class="movie-browse-agile">
      <div class="browse-agile-w3ls general-w3ls">
        <div class="tittle-head">
          <h4 class="latest-text">Kelola Komentar</h4>
          <div class="container">
            <div class="agileits-single-top">
              <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="active">Kelola Komentar</li>
              </ol>
            </div>
          </div>
        </div>

        <div class="container" style="margin-top: 25px; margin-bottom: 40px;">
          <div class="panel panel-default" style="border-radius: 4px; border: 1px solid #e5e5e5;">
            <div class="panel-heading" style="font-weight: bold; background-color: #f7f7f7; padding: 12px 15px;">
              <i class="fa fa-comments"></i> Daftar Komentar
            </div>
            <div class="panel-body" style="padding: 0;">
              <form action="{{ route('admin.comments') }}" method="GET" class="form-inline" style="padding: 15px;">
                <div class="form-group">
                  <label class="sr-only" for="search">Cari komentar</label>
                  <input type="search" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Cari komentar, pengguna, atau film">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                @if ($search !== '')
                  <a href="{{ route('admin.comments') }}" class="btn btn-default">Reset</a>
                @endif
              </form>

              <div class="table-responsive">
                <table class="table table-striped table-hover" style="margin-bottom: 0;">
                  <thead>
                    <tr>
                      <th>Pengguna</th>
                      <th>Film</th>
                      <th>Komentar</th>
                      <th>Waktu</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($comments as $comment)
                      <tr>
                        <td>{{ $comment->user->name ?? 'Pengguna tidak dikenal' }}</td>
                        <td>{{ $comment->film->title ?? 'Film tidak ditemukan' }}</td>
                        <td>{{ $comment->comment }}</td>
                        <td>{{ $comment->created_at?->diffForHumans() ?? '-' }}</td>
                        <td>
                          <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
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
                        <td colspan="5" class="text-center" style="padding: 20px;">
                          {{ $search !== '' ? 'Komentar tidak ditemukan' : 'Belum ada komentar' }}
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          @if ($comments->hasPages())
            <div class="text-center">
              {!! $comments->links() !!}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
