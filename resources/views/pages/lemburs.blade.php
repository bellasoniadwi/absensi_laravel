@extends('newlayout.main')

@section('title')
    Lembur
@endsection

@section('lemburs', 'active bg-gradient-info')

@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Pengajuan Lembur</h6>
          </div>
        </div>
        
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Pengajuan</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durasi Lembur</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Alasan</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data as $lembur)
              <tr>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $lembur['name'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs font-weight-bold">{{ date('Y-m-d', strtotime($lembur['timestamps'])) }}</span>
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $lembur['durasi'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <p class="text-xs font-weight-bold mb-0">{{ $lembur['alasan'] }}</p>
                </td>
                <td class="align-middle text-center">
                  <form action="{{ route('lembur.updateStatus', ['id' => $lembur['id']]) }}"
                      method="post">
                      @csrf
                      <button type="submit" class="btn btn-icons show_confirm_status">
                          @if ($lembur['isblocking'] == false)
                              <span class="badge badge-sm bg-gradient-success">
                                  Menunggu
                              </span>
                          @else
                              <span class="badge badge-sm bg-gradient-warning">
                                  Disetujui
                              </span>
                          @endif
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
