<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use Kreait\Firebase\Contract\Firestore;
use App\Exports\KaryawansExport;
use DateTime;
use Google\Cloud\Core\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;

class LemburController extends Controller
{
    public function index()
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
        }

        $firestore = new FirestoreClient([
            'projectId' => 'absensi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('lemburs');
        $data = [];
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();

        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $documentId = $doc->id();
            $name = $documentData['name'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;
            $durasi = $documentData['durasi'] ?? null;
            $alasan = $documentData['alasan'] ?? null;
            $status = $documentData['status'] ?? null;

            $data[] = [
                'name' => $name,
                'timestamps' => $timestamps,
                'durasi' => $durasi,
                'alasan' => $alasan,
                'status' => $status,
                'id'=>$documentId
            ];
        }
        return view('pages.lemburs', compact('data'));
    }

    public function updateStatus($id)
    {
        $firestore = app(Firestore::class);
        $lemburRef = $firestore->database()->collection('lemburs')->document($id);

        $lembur = $lemburRef->snapshot();

        if (!$lembur) {
            return redirect()->back()->with('error', 'Data lembur tidak ditemukan.');
        }

        $lemburRef->update([
            ['path' => 'status', 'value' => !$lembur['status']],
        ]);

        return redirect()->back()->with('success', 'Status lembur berhasil diubah.');
    }

}
