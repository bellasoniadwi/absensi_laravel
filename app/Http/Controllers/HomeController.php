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

        // Inisialisasi array
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
            $timestamps = $documentData['timestamps'] ?? null;
            $name = $documentData['name'] ?? null;

            $recordedMonthYear = date('Y-m', strtotime($timestamps));
            if ($recordedMonthYear === $currentMonthYear) {
                if (!isset($totals[$name])) {
                    $totals[$name] = [
                        'masuk' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'terlambat' => 0,
                        'tepat_waktu' => 0,
                    ];
                }
                // Hitung total keterangan "Masuk", "Izin", dan "Sakit" per field "name"
                if ($keterangan === "Masuk") {
                    $totals[$name]['masuk']++;
                    if (!isset($totalKeteranganPerName[$name])) {
                        $totalKeteranganPerName[$name] = 0;
                    }
                    $totalKeteranganPerName[$name]++;
                } elseif ($keterangan === "Izin") {
                    $totals[$name]['izin']++;
                    if (!isset($totalKeteranganPerName[$name])) {
                        $totalKeteranganPerName[$name] = 0;
                    }
                    $totalKeteranganPerName[$name]++;
                } elseif ($keterangan === "Sakit") {
                    $totals[$name]['sakit']++;
                    if (!isset($totalKeteranganPerName[$name])) {
                        $totalKeteranganPerName[$name] = 0;
                    }
                    $totalKeteranganPerName[$name]++;
                }

                if ($status === "Terlambat") {
                    $totals[$name]['terlambat']++;
                    
                } elseif ($status === "Tepat Waktu") {
                    $totals[$name]['tepat_waktu']++;
                    
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
        $totalSakit = 0;
        $totalTerlambat = 0;
        $totalTepatWaktu = 0;

        // menghitung totalMasuk, totalIzin, dan totalSakit dari value field "nama" yang sama
        foreach ($totals as $nameTotal) {
            $totalMasuk += $nameTotal['masuk'];
            $totalIzin += $nameTotal['izin'];
            $totalSakit += $nameTotal['sakit'];
            $totalTerlambat += $nameTotal['terlambat'];
            $totalTepatWaktu += $nameTotal['tepat_waktu'];
        }

            

        // Menghitung total tanpa keterangan per nama
        foreach ($totals as $name => $nameTotal) {
            $totalWithoutKeteranganPerName[$name] = $activeDaysInMonth - $totalKeteranganPerName[$name];
        }

        // Menghitung jumlah total tanpa keterangan
        $totalWithoutKeterangan = $activeDaysInMonth - ($totalMasuk + $totalIzin + $totalSakit);

        //Start function untuk menampilkan Akun Pengguna Tercatat di dashboard
        $collectionReferenceUser = $firestore->collection('users');
        $queryUser = $collectionReferenceUser->orderBy('name', 'asc');
        $documentsUser = $queryUser->documents();

        //menghitung akun pengguna yang tercatatat pada firestore collections users
        foreach ($documentsUser as $docUser) {
            $documentDataUser = $docUser->data();
            $name = $documentDataUser['name'] ?? null;

            $dataUser[] = [
                'name' => $name
            ];
        }
        $totalKaryawans = count($dataUser);


        // Ambil data absensi hanya untuk hari ini
        $currentDate = date('Y-m-d');
        $documents = $query->documents();

        // Inisialisasi nilai awal
        $totalMasuk = 0;
        $totalIzin = 0;
        $totalSakit = 0;

        // Menghitung totalMasuk, totalIzin, dan totalSakit dari value field "nama" yang sama
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $keterangan = $documentData['keterangan'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;

            // Filter hanya data hari ini
            if (date('Y-m-d', strtotime($timestamps)) === $currentDate) {
                if ($keterangan === "Masuk") {
                    $totalMasuk++;
                } elseif ($keterangan === "Izin") {
                    $totalIzin++;
                } elseif ($keterangan === "Sakit") {
                    $totalSakit++;
                }
            }
        }

        return view('pages.dashboard', compact('totals', 'totalMasuk', 'totalIzin', 'totalSakit', 'totalTerlambat','totalTepatWaktu','totalKaryawans', 'currentMonthYearNow', 'totalWithoutKeteranganPerName'));

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

    public function notauthorize() {
        return view('newlayout.authorization');
    }
}