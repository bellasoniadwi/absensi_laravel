@extends('newlayout.main')

@section('title')
    Kehadiran Siswa
@endsection

@section('students', 'active bg-gradient-info')

@section('content')
<div class="row">
  <!-- export students data from firestore -->
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <a href="{{ route('export.siswa') }}" class="btn btn-success">Export Excel</a>
  </div>
  <!-- end export students data from firestore -->
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Tabel Siswa</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <div class="d-flex justify-content-end mb-3">
              <a class="btn btn-outline-info btn-sm mb-0 me-3" href="{{route('siswa.form')}}">Tambah Siswa</a>
            </div>
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Datang</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode QR</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                  {{-- <th class="text-secondary opacity-7"></th> --}}
                </tr>
              </thead>
              <tbody>
                @foreach($data as $student)
                <tr>
                  <td class="align-middle text-center">
                    <img src="{{ $student['image'] }}" class="avatar avatar-lg me-3 border-radius-lg" alt="user1">
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">{{ $student['name'] }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ date('Y-m-d', strtotime($student['timestamps'])) }}</span>
                  </td>
                  <td class="align-middle text-center">
                    @php
                        // Ubah timestamps ke dalam format UTC+7
                        $timestamp = new \DateTime($student['timestamps']);
                        $timezone = new \DateTimeZone('Asia/Jakarta');
                        $timestamp->setTimezone($timezone);
                    @endphp
                    <span class="text-secondary text-xs font-weight-bold">{{ $timestamp->format('H:i:s') }}</span>
                  </td>
                  <td>
                    <div class="visible-print align-middle text-center">
                      {!! QrCode::size(60)->generate($student['id']); !!} 
                      <p style="font-size: 10px;">{{ $student['id'] }} </p> 
                    </div>
                  </td>                
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-primary">
                      <a href="{{ $student['googleMapsUrl'] }}" class="text-light font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user"> lihat lokasi</a>
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <form action="{{ route('siswa.delete', ['id' => $student['id']]) }}" method="post">
                      @csrf
                      @method('delete')
                      <a href="{{ route('siswa.form.edit', ['id' => $student['id']]) }}">
                        <i class="material-icons" title="Edit Card">edit</i>
                      </a>

                      <button type="submit" class="btn btn-icons show_confirm">
                        <i class="material-icons ms-auto text-dark cursor-pointer" title="Hapus Siswa">delete</i>
                      </button>
                    </form>
                  </td>
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
              title: `Yakin ingin menghapus data?`,
              text: "Data ini akan terhapus permanen setelah anda menyetujui pesan ini",
              icon: "warning",
              buttons: true,
              dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              form.submit();
            } else {
                swal("Data Anda Aman!");
            }
          });
      });
  
</script>
@endsection
