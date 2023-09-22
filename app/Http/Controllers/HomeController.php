<?php

namespace App\Http\Controllers;

use App\Exports\KehadiranExport;
use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);
        $collectionReference = $firestore->collection('karyawans');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();
        $totals = [];
        $dataUser = [];
        $totalKeteranganPerName = [];
        $totalWithoutKeteranganPerName = [];

        // ambil data di bulan dan tahun ini
        $currentMonthYear = date('Y-m', strtotime('now'));
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $keterangan = $documentData['keterangan'] ?? null;
            $status = $documentData['status'] ?? null;
            $kategori = $documentData['kategori'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;
            $name = $documentData['name'] ?? null;

            $recordedMonthYear = date('Y-m', strtotime($timestamps));
            if ($kategori == "Datang") {
                if ($recordedMonthYear == $currentMonthYear) {
                    if (!isset($totals[$name])) {
                        $totals[$name] = [
                            'masuk' => 0,
                            'izin' => 0,
                            'terlambat' => 0,
                            'tepat_waktu' => 0,
                        ];
                    }
                    // Hitung total keterangan "Masuk", "Izin", per field "name"
                    if ($keterangan == "Masuk") {
                        if ($keterangan == "Masuk") {
                            if ($status == "Tepat Waktu") {
                                $totals[$name]['masuk']++;
                                if (!isset($totalKeteranganPerName[$name])) {
                                    $totalKeteranganPerName[$name] = 0;
                                }
                                $totalKeteranganPerName[$name]++;
                            }elseif ($status == "Terlambat") {
                                $totals[$name]['terlambat']++;
                                if (!isset($totalKeteranganPerName[$name])) {
                                    $totalKeteranganPerName[$name] = 0;
                                }
                                $totalKeteranganPerName[$name]++;
                            }
                        }
                    } elseif ($keterangan == "Izin") {
                        $totals[$name]['izin']++;
                        if (!isset($totalKeteranganPerName[$name])) {
                            $totalKeteranganPerName[$name] = 0;
                        }
                        $totalKeteranganPerName[$name]++;
                    }

                    // if ($status == "Terlambat") {
                    //     $totals[$name]['terlambat']++;
                    // } elseif ($status == "Tepat Waktu") {
                    //     $totals[$name]['tepat_waktu']++;
                    // }
                }
            }
        }

        // menampilkan bulan dan tahun ini dalam indonesia
        $currentMonthYearNow = date('M Y', strtotime('now'));
        $monthNames = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $currentMonthYearNow = $monthNames[date('m')] . ' ' . date('Y');

        // Hitung jumlah hari dalam bulan ini (dengan mengabaikan hari Sabtu dan Minggu)
        $currentYear = date('Y');
        $currentMonth = date('m');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        $activeDaysInMonth = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDayOfWeek = date('N', strtotime("$currentYear-$currentMonth-$day"));
            if ($currentDayOfWeek >= 1 && $currentDayOfWeek <= 5) { // Hari Senin-Jumat
                $activeDaysInMonth++;
            }
        }

        // Inisiasi nilai awal
        $totalMasuk = 0;
        $totalIzin = 0;
        $totalTerlambat = 0;
        // $totalTepatWaktu = 0;

        // menghitung totalMasuk, totalIzin, dari value field "nama" yang sama
        foreach ($totals as $nameTotal) {
            $totalMasuk += $nameTotal['masuk'];
            $totalIzin += $nameTotal['izin'];
            $totalTerlambat += $nameTotal['terlambat'];
            // $totalTepatWaktu += $nameTotal['tepat_waktu'];
        }
        // Menghitung total tanpa keterangan per nama
        foreach ($totals as $name => $nameTotal) {
            $totalWithoutKeteranganPerName[$name] = $activeDaysInMonth - $totalKeteranganPerName[$name];
        }
        // Menghitung jumlah total tanpa keterangan
        $totalWithoutKeterangan = $activeDaysInMonth - ($totalMasuk + $totalIzin);

        // CARD PENGGUNA
        $collectionReferenceUser = $firestore->collection('users');
        $queryUser = $collectionReferenceUser->where('role', '=', 'Karyawan')->where('isblocking', '==', false);
        $documentsUser = $queryUser->documents();
        foreach ($documentsUser as $docUser) {
            $documentDataUser = $docUser->data();
            $name = $documentDataUser['name'] ?? null;
            $dataUser[] = [
                'name' => $name
            ];
        }
        $totalKaryawans = count($dataUser);


        // 3 CARD REKAPAN
        $currentDate = date('Y-m-d');
        $queryCard = $collectionReference->where('kategori', '=', 'Datang');
        $documentsCard = $queryCard->documents();
        $totalMasukTepatWaktu = 0;
        $totalTidakMasukIzin = 0;
        $totalMasukTelat = 0;
        foreach ($documentsCard as $docCard) {
            $documentDataCard = $docCard->data();
            $keterangan = $documentDataCard['keterangan'] ?? null;
            $status = $documentDataCard['status'] ?? null;
            $timestamps = $documentDataCard['timestamps'] ?? null;

            if (date('Y-m-d', strtotime($timestamps)) == $currentDate) {
                if ($keterangan == "Masuk") {
                    if ($status == "Tepat Waktu") {
                        $totalMasukTepatWaktu++;
                    } else {
                        $totalMasukTelat++;
                    }
                } elseif ($keterangan == "Izin") {
                    $totalTidakMasukIzin++;
                }
            }
        }
        return view('pages.dashboard', compact('totalKaryawans', 'totalTidakMasukIzin', 'totalMasukTepatWaktu', 'totalMasukTelat', 'totals', 'currentMonthYearNow','currentMonthYearNow', 'totalWithoutKeteranganPerName'));
        // return view('pages.dashboard', compact('totalKaryawans', 'totalTidakMasukIzin', 'totalMasukTepatWaktu', 'totalMasukTelat', 'totals', 'totalIzin', 'totalTepatWaktu', 'currentMonthYearNow', 'totalWithoutKeteranganPerName'));
    }

    //export Rekap jumlah akun serta keterangannya pada dashboard
    public function exportExcel()
    {
        return Excel::download(new RekapExport(), 'rekap_karyawan.xlsx');
    }

    //export Rekap Kehadiran pada dashboard
    public function exportExcelkehadiran()
    {
        return Excel::download(new KehadiranExport(), 'rekap_kehadiran.xlsx');
    }

    public function notauthorize()
    {
        return view('newlayout.authorization');
    }
}
