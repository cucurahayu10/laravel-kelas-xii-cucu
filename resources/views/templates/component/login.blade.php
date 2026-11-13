<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Klik Disini</div>
              </div>

              {{-- FORM LOGIN --}}
              <div class="form" id="loginFormContainer">
                <h3>Login to your account</h3>

                {{-- Alert Error Login --}}
                @if (isset($errors) && $errors->has('login'))
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px; border-radius: 3px;">
                    <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('login') }}
                  </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                  @csrf
                  <input type="text" name="login" value="{{ old('login') }}" placeholder="Username atau Email" required autofocus autocomplete="username">
                  <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
                  <input type="submit" value="Login">
                </form>
                <div class="cta" style="margin-top: 15px; font-size: 13px; color: #666; text-align: center;">
                  Belum punya akun? <a href="javascript:void(0)" class="switch-to-register" style="color: #FF8D1B; font-weight: bold;">Daftar di sini</a>
                </div>
              </div>

              {{-- FORM REGISTER --}}
              <div class="form" id="registerFormContainer">
                <h3>Create an account</h3>

                {{-- Alert Error Register --}}
                @if (isset($errors) && $errors->any() && (session('auth_modal') === 'register' || $errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation')))
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px; border-radius: 3px;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach ($errors->all() as $error)
                        @if ($error !== $errors->first('login'))
                          <li>{{ $error }}</li>
                        @endif
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                  @csrf
                  <input type="text" name="name" value="{{ old('name') }}" placeholder="Username / Nama Lengkap" required autocomplete="name">
                  <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autocomplete="email">
                  <input type="password" name="password" placeholder="Password (min. 8 karakter)" required minlength="8" autocomplete="new-password">
                  <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required minlength="8" autocomplete="new-password">
                  <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Nomor Telepon (opsional)">
                  <input type="submit" value="Register">
                </form>
                <div class="cta" style="margin-top: 15px; font-size: 13px; color: #666; text-align: center;">
                  Sudah punya akun? <a href="javascript:void(0)" class="switch-to-login" style="color: #FF8D1B; font-weight: bold;">Login di sini</a>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  function toggleForms() {
    $('.toggle').children('i').toggleClass('fa-pencil');
    $('.form').animate({
      height: "toggle",
      'padding-top': 'toggle',
      'padding-bottom': 'toggle',
      opacity: "toggle"
    }, "slow");
  }

  $(document).ready(function(){
    // Klik tombol pensil di kanan atas modal
    $('.toggle').click(function(){
      toggleForms();
    });

    // Klik link ganti form di dalam form
    $('.switch-to-register, .switch-to-login').click(function(){
      toggleForms();
    });

    // Otomatis buka modal dan beralih ke form yang sesuai jika ada error validasi atau parameter ?auth=
    @if ((isset($errors) && $errors->any()) || request()->has('auth') || session('auth_modal'))
      $('#myModal').modal('show');

      @if (session('auth_modal') === 'register' || request('auth') === 'register' || (isset($errors) && $errors->any() && !$errors->has('login')))
        // Tampilkan form register
        $('.toggle').children('i').toggleClass('fa-pencil');
        $('#loginFormContainer').hide();
        $('#registerFormContainer').show();
      @endif
    @endif
  });
</script>
