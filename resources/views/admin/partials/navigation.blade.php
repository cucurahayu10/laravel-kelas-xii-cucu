<div class="container" style="margin-top: 15px;">
  <nav class="navbar navbar-default" style="border-radius: 4px; margin-bottom: 0;">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
        </li>
        <li class="{{ request()->routeIs('admin.films*', 'admin.film.*') ? 'active' : '' }}">
          <a href="{{ route('admin.films') }}"><i class="fa fa-film"></i> Kelola Film</a>
        </li>
        <li class="{{ request()->routeIs('admin.comments*') ? 'active' : '' }}">
          <a href="{{ route('admin.comments') }}"><i class="fa fa-comments"></i> Kelola Komentar</a>
        </li>
      </ul>
      <form action="{{ route('logout') }}" method="POST" class="navbar-form navbar-right">
        @csrf
        <button type="submit" class="btn btn-default">
          <i class="fa fa-sign-out"></i> Logout
        </button>
      </form>
    </div>
  </nav>
</div>
