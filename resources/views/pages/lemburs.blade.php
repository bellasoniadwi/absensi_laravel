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
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Nama</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal Pengajuan</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Durasi Lembur</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Alasan</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $lembur)
                                    <tr>
                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $lembur['name'] }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-secondary text-xs font-weight-bold">{{ date('d-m-Y', strtotime($lembur['timestamps'])) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $lembur['durasi'] }}</p>
                                        </td>
                                        <td class="align-middle text-center text-xs font-weight-bold mb-0s">
                                            @php
                                                $alasan = $lembur['alasan'];
                                                $alasanChunks = str_split($alasan, 60);
                                            @endphp
                                            <div class="short-text">
                                                @foreach (array_slice($alasanChunks, 0, 1) as $chunk)
                                                    {{ $chunk }}<br>
                                                @endforeach
                                                @if (count($alasanChunks) > 1)
                                                    <a href="#" class="show-more text-info" data-target=".full-text"> Lihat selengkapnya..</a>
                                                @endif
                                            </div>
                                            <div class="full-text" style="display: none;">
                                                @foreach (array_slice($alasanChunks, 1) as $chunk)
                                                    {{ $chunk }}<br>
                                                @endforeach
                                                  <a href="#" class="show-less text-info" data-target=".full-text">Tutup</a>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <form action="{{ route('lembur.updateStatus', ['id' => $lembur['id']]) }}"
                                                method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-icons show_confirm_status">
                                                    @if ($lembur['status'] == false)
                                                        <span class="badge badge-sm bg-gradient-warning">
                                                            Belum Disetujui
                                                        </span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-success">
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
@section('js')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript">
        $('.show_confirm_status').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Yakin ingin mengubah status lembur?`,
                    text: "Pengajuan lembur akan disetujui",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    } else {
                        swal("Status lembur batal diubah");
                    }
                });
        });

        $('.show-more').click(function(event) {
            event.preventDefault();
            var target = $(this).data("target");
            $(target).show();
            $(this).hide();
        });

        $('.show-less').click(function(event) {
            event.preventDefault();
            var target = $(this).data("target");
            $(target).hide();
            $(this).closest("td").find(".show-more").show();
        });
    </script>
@endsection