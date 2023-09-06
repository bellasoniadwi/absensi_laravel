@extends('newlayout.main')

@section('title')
    Akun Pengguna
@endsection

@section('users', 'active bg-gradient-info')

@section('content')
<div class="row">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <a href="{{ route('export.users') }}" class="btn btn-success">Export Excel</a>
      </div>
      <div class="col-xl-9 col-sm-6 mb-xl-0 mb-8">
        <form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group d-flex justify-content-end">
                <div class="custom-file">
                        <input type="file" name="users_excel" class="custom-file-input" id="customFile" accept=".xlsx, .xls" required>
                        <button class="btn btn-success ml-2" type="submit">Import</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Akun pengguna</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <div class="d-flex justify-content-end mb-3">
              <a class="btn btn-outline-info btn-sm mb-0 me-3" href="{{route('user.form')}}">Tambah Akun</a>
            </div>
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nomor Induk</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Telepon</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jabatan</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data as $user)
                <tr>
                  <td class="align-middle text-center">
                    <img src="{{ $user['image'] }}" class="avatar avatar-lg me-3 border-radius-lg" alt="user1">
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['name'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['email'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['nomor_induk'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['telepon'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['jabatan'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $user['role'] }}</p>
                  </td>
                  {{-- START fungsi edit dan delete --}}
                  <td class="align-middle text-center">
                    <form action="{{ route('user.delete', ['id' => $user['id']]) }}" method="post">
                      @csrf
                      @method('delete')
                      <a href="{{ route('user.form.edit', ['id' => $user['id']]) }}">
                        <i class="material-icons" title="Edit Card">edit</i>
                      </a>

                      <button type="submit" class="btn btn-icons show_confirm">
                        <i class="material-icons ms-auto text-dark cursor-pointer" title="Hapus user">delete</i>
                      </button>
                    </form>
                  </td>
                  {{-- END fungsi edit dan delete --}}
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
 
     $('.show_confirm').click(function(event) {
          var form =  $(this).closest("form");
          var name = $(this).data("name");
          event.preventDefault();
          swal({
              title: `Yakin ingin menghapus data akun?`,
              text: "Data ini akan terhapus permanen setelah anda menyetujui pesan ini",
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              form.submit();
            } else {
                swal("Akun batal dihapus");
            }
          });
      });

      $(document).ready(function() {
            // Tampilkan SweetAlert setelah proses import selesai
            @if (Session::has('success'))
                swal("Success", "{{ Session::get('success') }}", "success");
            @endif
        });
  
</script>
@endsection