<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class StudentsExport implements FromCollection, WithHeadings
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
                $nomor_induk_akun = $userSnapshot->data()['nomor_induk'];
                $angkatan_akun = $userSnapshot->data()['angkatan'];
            } else {
                $nama_akun = "Name not found";
                $role_akun = "Role not found";
                $nomor_induk_akun = "Nomor Induk not found";
                $angkatan_akun = "Angkatan not found";
            }
        } else {
            $nama_akun = "Name ga kebaca";
            $role_akun = "Role ga kebaca";
            $nomor_induk_akun = "Nomor Induk not found";
            $angkatan_akun = "Angkatan not found";
        }

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('students');
        $userData = [];

        // Retrieve users data
        $usersCollection = $firestore->collection('users')->documents();
        foreach ($usersCollection as $userDoc) {
            $userData[$userDoc->data()['name']] = [
                'nomor_induk' => $userDoc->data()['nomor_induk'],
                'angkatan' => $userDoc->data()['angkatan'],
            ];
        }

        if ($role_akun == 'Superadmin') {
            $query = $collectionReference->orderBy('name');
        } elseif ($role_akun == 'Instruktur') {
            $query = $collectionReference->where('instruktur', '=', $nama_akun);
        } else {
            $query = $collectionReference->orderBy('name');
        }

        $documents = $query->documents();
        
        
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $id = $doc->id();
            $name = $documentData['name'] ?? null;
            $keterangan = $documentData['keterangan'] ?? null;
            $instruktur = $documentData['instruktur'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;

            $jam_absen = new \DateTime($timestamps);
            $timezone = new \DateTimeZone('Asia/Jakarta');
            $jam_absen->setTimezone($timezone);
            
            $image = $documentData['image'] ?? null;
            $latitude = $documentData['latitude'] ?? null;
            $longitude = $documentData['longitude'] ?? null;
            
            // Check if the user exists in users collection
            $userDetails = $userData[$name] ?? null;

            $userNomorInduk = $userDetails['nomor_induk'] ?? '';
            $userAngkatan = $userDetails['angkatan'] ?? '';
            

            $data[] = [
                'id' => $id,
                'name' => $name,
                'nomor_induk' => $userNomorInduk,
                'angkatan' => $userAngkatan,
                'keterangan' => $keterangan,
                'instruktur' => $instruktur,
                'tanggal' => date('d-M-Y', strtotime($timestamps)),
                'jam_absen' => $jam_absen->format('H:i:s'),
                'image' => $image,
                'latitude' => $latitude,
                'longitude' => $longitude,
                
                
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Nomor Induk', 'Angkatan', 'Keterangan','Instruktur','Tanggal','Jam Absen', 'Image', 'Latitude', 'Longitude' ];
    }
}
