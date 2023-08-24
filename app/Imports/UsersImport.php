<?php
// app/Imports/UsersImport.php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FirestoreClient;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $usersCollection = $firestore->collection('users');
        $auth = app('firebase.auth');

        foreach ($rows as $row) {
            $user = [
                'nomor_induk' => $row['nomor_induk'],
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => $row['password'],
                'telepon' => $row['telepon'],
                'jabatan' => $row['jabatan'],
                'role' => $row['role'],
                'image' => $row['image'],
            ];

            // Save to Firestore collection
            $usersCollection->document($user['nomor_induk'])->set($user);

            // Create authentication user
            // Create authentication user
            $auth->createUser([
                'email' => $user['email'],
                'password' => $user['password'],
                'displayName' => $user['name']
            ]);
        }
        return collect($user);
    }

}
