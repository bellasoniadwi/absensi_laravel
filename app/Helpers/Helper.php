<?php

namespace App\Helpers;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;

class Helper {
    public static function NomorKaryawanGenerator(){
        $now = Carbon::now();
        $month = $now->format('m');
        $year = $now->format('y');

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReferenceUser = $firestore->collection('users');
        $queryUser = $collectionReferenceUser->orderBy('name', 'asc');
        $documentsUser = $queryUser->documents();
        $dataUser = [];

        foreach ($documentsUser as $docUser) {
            $documentDataUser = $docUser->data();
            $name = $documentDataUser['name'] ?? null;

            $dataUser[] = [
                'name' => $name
            ];
        }

        $totalKaryawans = count($dataUser);
        // menampilkan nomor urut
        $urutan = $totalKaryawans + 1;

        $nomor = 'SGS-' . $month . $year . str_pad($urutan, 4, '0', STR_PAD_LEFT);

        return $nomor;
    }
}

