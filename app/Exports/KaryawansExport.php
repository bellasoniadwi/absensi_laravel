<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class KaryawansExport implements FromCollection, WithHeadings
{
    public function collection()
    {
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
            $status = $documentData['status'] ?? null;
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
                'status' => $status,
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
        return ['Nomor Induk', 'Nama', 'Email', 'Telepon', 'Jabatan', 'Keterangan','Status','Tanggal','Jam Absen', 'Image', 'Latitude', 'Longitude' ];
    }
}
