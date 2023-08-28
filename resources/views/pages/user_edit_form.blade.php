@extends('newlayout.main')

@section('title')
    Edit Data
@endsection

@section('users', 'active bg-gradient-info')

@section('content')
        <div class="row justofy-content-center">
          <div class="col-xl-8 col-lg-8 col-md-8 mx-auto">
            <div class="card card-plain">
                <h4 class="font-weight-bolder text-center">
                    Form Edit Data user
                </h4>
                <div class="card-body">
                    <form id="userForm" role="form" method="POST" action="{{ route('user.update', $documentId) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $user->get('name') ? 'active' : '' }}">Name</label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ $user->get('name') }}"
                                required autocomplete="name" readonly>
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $user->get('jabatan') ? 'active' : '' }}">Jabatan</label>
                            <input type="text" id="jabatan" name="jabatan"
                                class="form-control @error('jabatan') is-invalid @enderror" value="{{ $user->get('jabatan') }}"
                                required autocomplete="jabatan">
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label"></label>
                            <select class="form-control has-feedback-right" id="role" name="role" value="{{ $user->get('role') }}">
                                {{-- <option value=""> --Pilih role--</option> --}}
                                <option value="Admin" selected>Admin</option>
                                <option value="Karyawan" selected>Karyawan</option>
                            </select>
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $user->get('telepon') ? 'active' : '' }}">Telepon</label>
                            <input type="number" id="telepon" name="telepon"
                                class="form-control @error('telepon') is-invalid @enderror" value="{{ $user->get('telepon') }}"
                                required autocomplete="telepon">
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <div class="col-md-6">
                                <label class="form-label"></label>
                                <input type="file" id="image" name="image"
                                    class="form-control @error('image') is-invalid @enderror" value="{{ old('image') }}"
                                    autocomplete="image" onchange="previewImage(event)">
                                    
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <img id="preview" width="80px" height="100px" src="{{ $user['image'] }}" alt="user1">
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit"
                                class="btn btn-lg bg-gradient-info btn-lg w-100 mt-4 mb-0">Simpan</button>
                        </div>
                        <div class="form-row">
                            <script>
                                // Kode script JS
                                function previewImage(event) {
                                    var input = event.target;
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        reader.onload = function (e) {
                                            var previewImage = document.getElementById('preview');
                                            previewImage.src = e.target.result;
                                            previewImage.style.display = 'block'; // Tampilkan gambar setelah di-upload
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                                
                                // Tambahkan event listener untuk form saat form dikirimkan
                                document.getElementById('userForm').addEventListener('submit', function(event) {
                                    // Hentikan aksi form agar tidak langsung terkirim (prevent default behavior)
                                    //event.preventDefault();
                                });
                            </script>       
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                </div>
            </div>
        </div>
    </div>
@endsection
