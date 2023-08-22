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

class KaryawanController extends Controller
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

        $collectionReference = $firestore->collection('karyawans');
        $data = [];
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();

        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $documentId = $doc->id();
            $name = $documentData['name'] ?? null;
            $keterangan = $documentData['keterangan'] ?? null;
            $timestamps = $documentData['timestamps'] ?? null;
            $image = $documentData['image'] ?? null;
            $latitude = $documentData['latitude'] ?? null;
            $longitude = $documentData['longitude'] ?? null;
            $googleMapsUrl = sprintf('https://www.google.com/maps?q=%f,%f', $latitude, $longitude);

            $data[] = [
                'name' => $name,
                'keterangan' => $keterangan,
                'timestamps' => $timestamps,
                'image' => $image,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'googleMapsUrl' => $googleMapsUrl,
                'id'=>$documentId
            ];
        }
        return view('pages.karyawans', compact('data'));
    }

    public function create_form() {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();

            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();

            if ($userSnapshot->exists()) {
                $nama_akun = $userSnapshot->data()['name'];
            } else {
                $nama_akun = "Name not found";
            }
        } else {
            $nama_akun = "Name ga kebaca";
        }

        $karyawanCollection = app('firebase.firestore')->database()->collection('users');
    
        // Mengambil dokumen dari collection dan mengubahnya menjadi array
        $karyawanDocuments = $karyawanCollection->documents();
        $list_karyawan = [];
        foreach ($karyawanDocuments as $document) {
            $list_karyawan[] = $document->data();
        }
    
        return view('pages.karyawan_form', ['list_karyawan' => $list_karyawan]);
    }

    public function edit_form($documentId) {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();

            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();

            if ($userSnapshot->exists()) {
                $nama_akun = $userSnapshot->data()['name'];
            } else {
                $nama_akun = "Name not found";
            }
        } else {
            $nama_akun = "Name ga kebaca";
        }

        $karyawanCollection = app('firebase.firestore')->database()->collection('users');
    
        // Mengambil dokumen dari collection dan mengubahnya menjadi array
        $karyawanDocuments = $karyawanCollection->documents();
        $list_karyawan = [];
        foreach ($karyawanDocuments as $document) {
            $list_karyawan[] = $document->data();
        }

        try {
            $karyawan = app('firebase.firestore')->database()->collection('karyawans')->document($documentId)->snapshot();

            return view('pages.karyawan_edit_form', compact('karyawan', 'documentId', 'list_karyawan'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data karyawan: ' . $e->getMessage()], 500);
        }
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'keterangan' => ['required', 'string', 'max:255'],
            'image' => ['mimes:png,jpg,jpeg', 'max:2048']
        ]);
    }

    public function create(Request $request) {
        try {
            $user = auth()->user();
    
            if ($user) {
                $id = $user->localId;
                $firestore = app('firebase.firestore');
                $database = $firestore->database();
    
                $userDocRef = $database->collection('users')->document($id);
                $userSnapshot = $userDocRef->snapshot();
    
                if ($userSnapshot->exists()) {
                    $name = $userSnapshot->data()['name'];
                } else {
                    $name = "Tidak Dikenali";
                }
            } else {
                $name = "Tidak Dikenali";
            }
    
            $this->validator($request->all())->validate();
    
            // Handle image upload and store its path in Firebase Storage
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');

                $storage = Firebase::storage();
                $uniqueId = microtime(true) * 10000;
                $storagePath = 'images/' . $uniqueId . '_' . now()->format('Y-m-d') . '.jpg';

                $storage->getBucket()->upload(
                    file_get_contents($imageFile->getRealPath()),
                    ['name' => $storagePath]
                );

                $imagePath = $storage->getBucket()->object($storagePath)->signedUrl(now()->addYears(10));
            } else {
                $imagePath = null; // If no image is uploaded, set the image path to null
            }
    
            $firestore = app(Firestore::class);
            $karyawanRef = $firestore->database()->collection('karyawans');
            $tanggal = new Timestamp(new DateTime());

            $karyawanRef->add([
                'name' => $request->input('name'),
                'keterangan' => $request->input('keterangan'),
                'timestamps' => $tanggal,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'image' => $imagePath,
            ]);
            
            Alert::success('Data absensi karyawan berhasil ditambahkan');
            return redirect()->route('karyawan');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function update(Request $request, $documentId)
    {
        try{
            $this->validator($request->all())->validate();
        
            // Handle image upload and store its path in Firebase Storage
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');

                $storage = Firebase::storage();
                $uniqueId = microtime(true) * 10000;
                $storagePath = 'images/' . $uniqueId . '_' . now()->format('Y-m-d') . '.jpg';

                $storage->getBucket()->upload(
                    file_get_contents($imageFile->getRealPath()),
                    ['name' => $storagePath]
                );

                $imagePath = $storage->getBucket()->object($storagePath)->signedUrl(now()->addYears(10));
            } else {
                $firestore = app(Firestore::class);
                $karyawanRef = $firestore->database()->collection('karyawans')->document($documentId)->snapshot();
                $imagePath = $karyawanRef->get('image');
            }
        
                $firestore = app(Firestore::class);
                $karyawanRef = $firestore->database()->collection('karyawans')->document($documentId);
                $tanggal = new Timestamp(new DateTime());

                $karyawanRef->update([
                    ['path' => 'name', 'value' => $request->input('name')],
                    ['path' => 'keterangan', 'value' => $request->input('keterangan')],
                    ['path' => 'timestamps', 'value' => $tanggal],
                    ['path' => 'latitude', 'value' => $request->input('latitude')],
                    ['path' => 'longitude', 'value' => $request->input('longitude')],
                    ['path' => 'image', 'value' => $imagePath],
                ]);

                Alert::success('Data absensi karyawan berhasil diubah');
                return redirect()->route('karyawan');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }
    
    public function delete($documentId)
    {
        try {
            app('firebase.firestore')->database()->collection('karyawans')->document($documentId)->delete();
            Alert::success('Data absensi karyawan berhasil dihapus');
            return redirect()->route('karyawan');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data karyawan: ' . $e->getMessage()], 500);
        }
    }
    
    //export excel untuk data bukti kehadiran karyawan
    public function exportExcel()
    {
        return Excel::download(new KaryawansExport(), 'bukti_kehadiran.xlsx');
    }
}
