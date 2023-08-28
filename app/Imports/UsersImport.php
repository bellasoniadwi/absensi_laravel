<?php

namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Kreait\Firebase\Auth as FirebaseAuth;

class UsersImport implements ToCollection, WithHeadingRow
{
    private $firestore;
    private $auth;

    public function __construct(FirestoreClient $firestore, FirebaseAuth $auth)
    {
        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);
        
        $this->firestore = $firestore;
        $this->auth = $auth;
    }

    public function collection(Collection $rows)
    {
        $usersCollection = $this->firestore->collection('users');

        foreach ($rows as $row) {
            $user = [
                'nomor_induk' => $row['nomor_induk'],
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']), // Hash the password
                'telepon' => $row['telepon'],
                'jabatan' => $row['jabatan'],
                'role' => $row['role'],
                'image' => $row['image'],
            ];

            // Save to Firestore collection
            $usersCollection->document($user['email'])->set($user);

            // Create authentication user
            $this->auth->createUser([
                'email' => $user['email'],
                'password' => $row['password'], // Use the original password here
            ]);
        }
    }
}
