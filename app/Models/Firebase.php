<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class Firebase extends Model
{
    //Model ini digunakan untuk menginisialisasi collection firestore "karyawans"
    use HasFactory;

    protected $table = 'karyawans';
    protected $fillable = [
        'name', 'nim', 'angkatan', 'timestamps', 'image', 'latitude', 'longitude'
    ];

    protected $firestore;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->firestore = new FirestoreClient([
            'projectId' => 'project-sinarindo',
        ]);
    }
}
