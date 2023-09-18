@extends('newlayout.main')

@section('title')
    Kehadiran Karyawan
@endsection

@section('karyawans', 'active bg-gradient-info')

@section('content')
<div class="row">
  <!-- start export karyawans data from firestore -->
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <a href="{{ route('export.karyawan') }}" class="btn btn-success">Export Excel</a>
  </div>
  <!-- end export karyawans data from firestore -->
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
          <h6 class="text-white text-capitalize ps-3">Tabel Karyawan</h6>
        </div>
      </div>
      <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
          <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="mx-4 mb-3">
              <label for="kategori-filter" class="form-label">Filter Kategori:</label>
              <select class="form-select" id="kategori-filter">
                  <option value="">Semua</option>
                  <option value="Datang">Datang</option>
                  <option value="Pulang">Pulang</option>
              </select>
            </div>
          </div>
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keterangan</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Absen</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $karyawan)
              <tr data-kategori="{{ $karyawan['kategori'] }}">
                <td class="align-middle text-center">
                  <img src="{{ $karyawan['image'] }}" class="avatar avatar-lg me-3 border-radius-lg" alt="user1">
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $karyawan['name'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $karyawan['kategori'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $karyawan['keterangan'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $karyawan['status'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs font-weight-bold">{{ date('Y-m-d', strtotime($karyawan['timestamps'])) }}</span>
                </td>
                <td class="align-middle text-center">
                  @php
                      // Ubah timestamps ke dalam format UTC+7
                      $timestamp = new \DateTime($karyawan['timestamps']);
                      $timezone = new \DateTimeZone('Asia/Jakarta');
                      $timestamp->setTimezone($timezone);
                  @endphp
                  <span class="text-secondary text-xs font-weight-bold">{{ $timestamp->format('H:i:s') }}</span>
                </td>
                <td class="align-middle text-center">
                  <span class="badge badge-sm bg-gradient-info">
                    <a href="{{ $karyawan['googleMapsUrl'] }}" class="text-light font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Lihat lokasi" target="_blank">Lihat Lokasi</a>
                  </span>
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
    $(document).ready(function() {
        $('#kategori-filter').change(function() {
            var selectedKategori = $(this).val();

            $('tbody tr').each(function() {
                var rowKategori = $(this).data('kategori');

                if (selectedKategori === '' || selectedKategori === rowKategori) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection
