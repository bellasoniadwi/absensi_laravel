<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#" target="_blank">
        <img src="{{ asset('assets/img/logo-ct.png')}}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">
          <?php
                // Mendapatkan informasi akun dari autentikasi Firebase
                $user = auth()->user(); // Ubah sesuai dengan cara Anda mendapatkan informasi akun

                if ($user) {
                  $id = $user->localId;
                  $firestore = app('firebase.firestore');
                  $database = $firestore->database();
                  $userDocRef = $database->collection('users')->document($id);
                  $userSnapshot = $userDocRef->snapshot();
                  if ($userSnapshot->exists()) {
                      $name = $userSnapshot->data()['name'];
                  } else {
                      $name = "Name not found";
                  }
                  echo $name;
                } else {
                    echo "Proyek Firebase";
                }
            ?>
        </span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white @yield('dashboard')" href="{{route('dashboard')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white @yield('users')" href="{{route('user.index')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Akun Pengguna</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white @yield('students')" href="{{route('siswa')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Kehadiran Siswa</span>
          </a>
        </li>
        {{-- <li class="nav-item">
          <a class="nav-link text-white @yield('rekap')" href="{{route('rekap')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">Rekap</span>
          </a>
        </li> --}}
        {{-- <li class="nav-item">
          <a class="nav-link text-white @yield('ceksaya')" href="{{route('ceksaya')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">Cek</span>
          </a>
        </li> --}}
      </ul>
    </div>
  </aside>