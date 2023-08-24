<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Kreait\Firebase\Contract\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Collection;

class UsersImport implements ToCollection, WithHeadingRow
{
    protected $auth;

    public function __construct(Auth $auth) {
       $this->auth = $auth;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $userProperties = [
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'name' => $row['name'],
                'nomor_induk' => $row['nomor_induk'],
                'telepon' => $row['telepon'],
                'jabatan' => $row['jabatan'],
                'role' => $row['role'],
                'image' => $row['image'],
            ];

            $createdUser = $this->auth->createUser($userProperties);

            $firestore = app(Firestore::class);
            $userRef = $firestore->database()->collection('users')->document($createdUser->uid);
            $userRef->set([
                'nomor_induk' => $row['nomor_induk'],
                'name' => $row['name'],
                'email' => $row['email'],
                'telepon' => $row['telepon'],
                'jabatan' => $row['jabatan'],
                'role' => $row['role'],
                'image' => $row['image'],
            ]);
        }

        Alert::success('Akun-akun baru berhasil ditambahkan');
    }

    public function startRow(): int
    {
        return 2; // Data dimulai dari baris kedua (baris pertama adalah judul kolom)
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required',
            'nomor_induk' => 'required',
            'telepon' => 'required',
            'jabatan' => 'required',
        ];
    }
}