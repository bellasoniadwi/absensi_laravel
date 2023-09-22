<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class RekapExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('karyawans');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();
        $totals = [];
        $rekapData = [];
        // $userData = []; //perhitungan gaji

        // digunakan dalam Perhitungan gaji
        // $usersCollection = $firestore->collection('users')->documents();
        // foreach ($usersCollection as $userDoc) {
        //     $userData[$userDoc->data()['name']] = [
        //         'jabatan' => $userDoc->data()['jabatan'],
        //     ];
        // }

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
                if ($recordedMonthYear === $currentMonthYear) {
                    if (!isset($totals[$name])) {
                        $totals[$name] = [
                            'masuk' => 0,
                            'izin' => 0,
                            'terlambat' => 0,
                            'tepat_waktu' => 0,
                        ];
                    }

                    // Menghitung total per nama
                    if ($keterangan === "Masuk") {
                        if ($status == "Tepat Waktu") {
                            $totals[$name]['masuk']++;
                        }elseif ($status == "Terlambat") {
                            $totals[$name]['terlambat']++;
                        }
                    } elseif ($keterangan === "Izin") {
                        $totals[$name]['izin']++;
                    }
                }
            }
        }

        // pengambilan bulan dalam indonesia
        $monthNames = [
            'Jan' => 'Januari',
            'Feb' => 'Februari',
            'Mar' => 'Maret',
            'Apr' => 'April',
            'May' => 'Mei',
            'Jun' => 'Juni',
            'Jul' => 'Juli',
            'Aug' => 'Agustus',
            'Sep' => 'September',
            'Oct' => 'Oktober',
            'Nov' => 'November',
            'Dec' => 'Desember',
        ];

        foreach ($totals as $name => $nameTotal) {

            // KODE PROGRAM UNTUK GAJI
            // $userDetails = $userData[$name] ?? null;
            // $userJabatan = $userDetails['jabatan'] ?? '';
            // if ($userJabatan == 'Golongan 1') {
            //     $pengali = 100000;
            // } elseif ($userJabatan == 'Golongan 2') {
            //     $pengali = 80000;
            // } elseif ($userJabatan == 'Golongan 3') {
            //     $pengali = 60000;
            // }else {
            //     $pengali = 50000;
            // }

            // ganti bulan dari array
            $indonesianMonth = $monthNames[date('M', strtotime($timestamps))];

            $rekapData[] = [
                'name' => $name,
                'month' => $indonesianMonth,
                'year' => date('Y', strtotime($timestamps)),
                'total_masuk' => $nameTotal['masuk'],
                'terlambat' => $nameTotal['terlambat'],
                // 'tepat_waktu' => $nameTotal['tepat_waktu'],
                'total_izin' => $nameTotal['izin'],
                // 'total_sakit' => $nameTotal['sakit'],
            ];
        }

        return collect($rekapData);
    }

    public function headings(): array
    {
        return ['Name', 'Bulan', 'Tahun', 'Jumlah Masuk', 'Jumlah Terlambat', 'Jumlah Izin'];
        // return ['Name', 'Bulan', 'Tahun', 'Jumlah Masuk', 'Jumlah Terlambat','Jumlah Tepat Waktu', 'Jumlah Izin', 'Jumlah Sakit'];
    }
}
