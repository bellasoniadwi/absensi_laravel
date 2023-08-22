<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class KehadiranExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();

            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();

            if ($userSnapshot->exists()) {
                $nama_akun = $userSnapshot->data()['name'];
                $role_akun = $userSnapshot->data()['role'];
            } else {
                $nama_akun = "Name not found";
                $role_akun = "Role not found";
            }
        } else {
            $nama_akun = "Name ga kebaca";
            $role_akun = "Role ga kebaca";
        }

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('karyawans');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();

        $totals = [];

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

        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $keterangan = $documentData['keterangan'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;

            // ganti bulan dari array
            $indonesianMonth = $monthNames[date('M', strtotime($timestamps))];

            $recordedMonthYear = date('Y-m', strtotime($timestamps));
                if (!isset($totals[$recordedMonthYear])) {
                    $totals[$recordedMonthYear] = [
                        'month' => $indonesianMonth,
                        'year' => date('Y', strtotime($timestamps)),
                        'total_karyawans' => 0,
                        'total_masuk' => 0,
                        'total_izin' => 0,
                        'total_sakit' => 0,
                    ];
                }

                $totals[$recordedMonthYear]['total_karyawans']++;
                if ($keterangan === "Masuk") {
                    $totals[$recordedMonthYear]['total_masuk']++;
                } elseif ($keterangan === "Izin") {
                    $totals[$recordedMonthYear]['total_izin']++;
                } elseif ($keterangan === "Sakit") {
                    $totals[$recordedMonthYear]['total_sakit']++;
                }
        }

        // Convert the $totals array to a collection and return
        return collect(array_values($totals));
    }

    public function headings(): array
    {
        return ['Bulan', 'Tahun', 'Jumlah Akun', 'Jumlah Masuk', 'Jumlah Izin', 'Jumlah Sakit'];
    }
}
