<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class KehadiranExport implements FromCollection, WithHeadings
{
    
    public function collection()
    {   
        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        // Fetch and count users
        $collectionReferenceUser = $firestore->collection('users');
        $queryUser = $collectionReferenceUser->orderBy('name', 'asc');
        $documentsUser = $queryUser->documents();

        $dataUser = [];  // Initialize $dataUser

        foreach ($documentsUser as $docUser) {
            $documentDataUser = $docUser->data();
            $name = $documentDataUser['name'] ?? null;
            if ($name) {
                $dataUser[] = ['name' => $name];
            }
        }

        $totalUsers = count($dataUser);

        // Fetch attendance data
        $collectionReference = $firestore->collection('karyawans');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();

        $totals = [];

        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $keterangan = $documentData['keterangan'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;

            if ($timestamps) {  // Ensure timestamps is not null
                $currentDate = date('Y-m-d', strtotime($timestamps));
                if (!isset($totals[$currentDate])) {
                    $totals[$currentDate] = [
                        'tanggal' => date('d-M-Y', strtotime($timestamps)),
                        'data_absen_terekam' => 0,
                        'total_masuk' => 0,
                        'total_izin' => 0,
                        'total_sakit' => 0,
                    ];
                }

                $totals[$currentDate]['data_absen_terekam']++;
                if ($keterangan === "Masuk") {
                    $totals[$currentDate]['total_masuk']++;
                } elseif ($keterangan === "Izin") {
                    $totals[$currentDate]['total_izin']++;
                } elseif ($keterangan === "Sakit") {
                    $totals[$currentDate]['total_sakit']++;
                }
            }
        }

        return collect(array_values($totals),$totalUsers);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jumlah Data Absen Terekam', 'Jumlah Masuk', 'Jumlah Izin', 'Jumlah Sakit',];
    }
}
