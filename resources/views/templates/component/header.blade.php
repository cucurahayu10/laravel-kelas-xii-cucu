<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register" style="width: auto; max-width: 45%;">
      <ul>
        @guest
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login / Register</a></li>
        @else
          @if (Auth::user()->isAdmin())
            <li><a href="{{ route('admin.dashboard') }}" style="background: #2196F3; margin-right: 5px;"><i class="fa fa-dashboard"></i> Admin</a></li>
          @endif
          <li>
            <span style="color: #212121; font-weight: bold; margin-right: 8px;"><i class="fa fa-user"></i> {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline-block; vertical-align: middle;">
              @csrf
              <button type="submit" style="background: #212121; color: #fff; border: none; padding: 6px 15px; font-weight: bold; text-transform: uppercase; font-size: 13px; cursor: pointer; transition: 0.3s;">
                <i class="fa fa-sign-out"></i> Logout
              </button>
            </form>
          </li>
        @endguest
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>