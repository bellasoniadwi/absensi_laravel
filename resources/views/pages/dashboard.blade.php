@extends('newlayout.main')

@section('title')
    Dashboard
@endsection

@section('dashboard', 'active bg-gradient-info')

@section('content')
    <div class="row">
      <!-- export rekap data from firestore -->
      {{-- <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <a href="{{ route('export.kehadiran') }}" class="btn btn-success">Export Excel</a>
      </div> --}}
      <!-- end rekap karyawans data from firestore -->
    </div>
    <br>
    <div class="row">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-header p-3 pt-2">
                <div
                    class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">person</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Jumlah Pengguna</p>
                    <h4 class="mb-0">{{ $totalKaryawans }}</h4>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
                <p class="mb-0">Tercatat <span class="text-success text-md font-weight-bolder">{{ $totalKaryawans }}</span> karyawan aktif</p>
            </div>
        </div>
    </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div
                        class="icon icon-lg icon-shape bg-gradient-info shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Jumlah Masuk</p>
                        <h4 class="mb-0">{{ $totalMasukTepatWaktu }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-md font-weight-bolder">{{ $totalMasukTepatWaktu }}</span> karyawan masuk hari ini</p>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div
                        class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Jumlah Izin</p>
                        <h4 class="mb-0">{{ $totalTidakMasukIzin }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-md font-weight-bolder">{{ $totalTidakMasukIzin }}</span> karyawan izin hari ini</p>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
              <div class="card-header p-3 pt-2">
                  <div
                      class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                      <i class="material-icons opacity-10">person</i>
                  </div>
                  <div class="text-end pt-1">
                      <p class="text-sm mb-0 text-capitalize">Jumlah Terlambat</p>
                      <h4 class="mb-0">{{ $totalMasukTelat }}</h4>
                  </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
                  <p class="mb-0"><span class="text-success text-md font-weight-bolder">{{ $totalMasukTelat }}</span> karyawan telat hari ini</p>
              </div>
          </div>
          <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
              <div class="card">
                  
              </div>
          </div>
      </div>
    </div>
    <br><br>
    <div class="row">
      <!-- export rekap data from firestore -->
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <a href="{{ route('export.rekap') }}" class="btn btn-success">Export Excel</a>
      </div>
      <!-- end rekap Karyawans data from firestore -->
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Rekap Kehadiran {{ $currentMonthYearNow}}</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah Masuk</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah Terlambat</th>
                      {{-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah Tepat Waktu</th> --}}
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah Izin</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanpa Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($totals as $name => $total)
                    <tr>
                      <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $name }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $total['masuk'] }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $total['terlambat'] }}</p>
                      </td>
                      {{-- <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $total['tepat_waktu'] }}</p>
                      </td> --}}
                      <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $total['izin'] }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <p class="text-xs font-weight-bold mb-0">{{ $totalWithoutKeteranganPerName[$name] }}</p>
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