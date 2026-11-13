<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin OneMovies</title>
  <link href="{{ asset('template/css/bootstrap.css') }}" rel="stylesheet">
  <link href="{{ asset('template/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('template/css/admin.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="admin-body">
  <div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
      <a class="admin-brand" href="{{ route('admin.dashboard') }}">
        <i class="fa fa-film"></i>
        <span>One<span class="admin-brand-accent">Movies</span></span>
        <small>ADMIN</small>
      </a>

      <div class="admin-menu">
        <div class="admin-menu-label">MENU UTAMA</div>
        <a class="admin-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
        <a class="admin-menu-link {{ request()->routeIs('admin.films*', 'admin.film.*') ? 'active' : '' }}" href="{{ route('admin.films') }}">
          <i class="fa fa-film"></i> <span>Kelola Film</span>
        </a>
        <a class="admin-menu-link {{ request()->routeIs('admin.casts*') ? 'active' : '' }}" href="{{ route('admin.casts') }}">
          <i class="fa fa-users"></i> <span>Cast &amp; Aktor</span>
        </a>

        <div class="admin-menu-label">INTERAKSI &amp; USER</div>
        <a class="admin-menu-link {{ request()->routeIs('admin.comments*') ? 'active' : '' }}" href="{{ route('admin.comments') }}">
          <i class="fa fa-comments"></i> <span>Komentar</span>
        </a>
        <a class="admin-menu-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
          <i class="fa fa-user"></i> <span>Kelola User</span>
        </a>

        <div class="admin-menu-label">AKSI CEPAT</div>
        <a class="admin-menu-link" href="{{ route('home') }}">
          <i class="fa fa-external-link"></i> <span>Lihat Website</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="admin-logout-form">
          @csrf
          <button type="submit" class="admin-menu-link admin-logout">
            <i class="fa fa-sign-out"></i> <span>Keluar (Logout)</span>
          </button>
        </form>
      </div>

      <div class="admin-current-user">
        <span class="admin-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
        <span class="admin-user-copy">
          <strong>{{ auth()->user()->name }}</strong>
          <small><i class="fa fa-shield"></i> Administrator</small>
        </span>
      </div>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <button type="button" class="admin-sidebar-toggle" aria-label="Buka navigasi" aria-controls="adminSidebar" aria-expanded="false">
          <i class="fa fa-bars"></i>
        </button>
        <div class="admin-page-heading">
          <h1>@yield('page-title', 'Dashboard Ringkasan')</h1>
          <p>@yield('page-description', 'Pantau statistik film, cast, komentar, dan aktivitas terkini.')</p>
        </div>
        <div class="admin-top-actions">
          <a href="{{ route('admin.films.create') }}" class="admin-button admin-button-primary">
            <i class="fa fa-plus"></i> Tambah Film
          </a>
          <a href="{{ route('home') }}" class="admin-button admin-button-muted">
            <i class="fa fa-globe"></i> Buka Website
          </a>
        </div>
      </header>

      <main class="admin-content">
        @if (session('success'))
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('success') }}
          </div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
          </div>
        @endif
        @yield('content')
      </main>
    </div>
  </div>
  <script src="{{ asset('template/js/jquery-2.1.4.min.js') }}"></script>
  <script src="{{ asset('template/js/bootstrap.min.js') }}"></script>
  <script>
    (function () {
      var sidebar = document.getElementById('adminSidebar');
      var toggle = document.querySelector('.admin-sidebar-toggle');

      toggle.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }());
  </script>
  @stack('scripts')
</body>
</html>
