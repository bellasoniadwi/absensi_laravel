<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('users');
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();
        
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $id = $doc->id();
            $nomor_induk = $documentData['nomor_induk'] ?? null;
            $name = $documentData['name'] ?? null;
            $email = $documentData['email'] ?? null;
            $telepon = $documentData['telepon'] ?? null;
            $jabatan = $documentData['jabatan'] ?? null;
            $role = $documentData['role'] ?? null;
            $image = $documentData['image'] ?? null;
            

            $data[] = [
                'nomor_induk' => $nomor_induk,
                'name' => $name,
                'email' => $email,
                'telepon' => $telepon,
                'jabatan' => $jabatan,
                'role' => $role,
                'image' => $image,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['Nomor Induk', 'Nama', 'Email', 'Telepon', 'Jabatan', 'Role Akun', 'Foto Profil'];
    }
}
