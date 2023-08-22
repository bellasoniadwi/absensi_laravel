<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class KaryawansExport implements FromCollection, WithHeadings
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
            $nomor_induk_akun = "Nomor Induk not found";
        }

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        // Retrieve users data
        $userData = [];
        $usersCollection = $firestore->collection('users')->documents();
        foreach ($usersCollection as $userDoc) {
            $userData[$userDoc->data()['name']] = [
                'email' => $userDoc->data()['email'],
                'nomor_induk' => $userDoc->data()['nomor_induk'],
                'jabatan' => $userDoc->data()['jabatan'],
                'telepon' => $userDoc->data()['telepon'],
                'email' => $userDoc->data()['email'],
            ];
        }

        $collectionReference = $firestore->collection('karyawans');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();
        
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $id = $doc->id();
            $name = $documentData['name'] ?? null;
            $keterangan = $documentData['keterangan'] ?? null;
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
            $userJabatan = $userDetails['jabatan'] ?? '';
            $userTelepon = $userDetails['telepon'] ?? '';
            $userEmail = $userDetails['email'] ?? '';
            

            $data[] = [
                'nomor_induk' => $userNomorInduk,
                'name' => $name,
                'email' => $userEmail,
                'telepon' => $userTelepon,
                'jabatan' => $userJabatan,
                'keterangan' => $keterangan,
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
        return ['Nomor Induk', 'Nama', 'Email', 'Telepon', 'Jabatan', 'Keterangan','Tanggal','Jam Absen', 'Image', 'Latitude', 'Longitude' ];
    }
}
