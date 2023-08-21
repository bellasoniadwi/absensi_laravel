<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use Kreait\Firebase\Contract\Firestore;
use App\Exports\StudentsExport;
use DateTime;
use Google\Cloud\Core\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;

class StudentController extends Controller
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

        $collectionReference = $firestore->collection('students');
        $data = [];

        if ($role_akun == 'Superadmin') {
            $query = $collectionReference->orderBy('name');
        } elseif ($role_akun == 'Instruktur') {
            $query = $collectionReference->where('instruktur', '=', $nama_akun)->orderBy('name', 'asc');
        } else {
            $query = $collectionReference->orderBy('name');
        }

        $documents = $query->documents();

            foreach ($documents as $doc) {

                $documentData = $doc->data();
                $documentId = $doc->id();

                $name = $documentData['name'] ?? null;
                $timestamps = $documentData['timestamps'] ?? null;
                $image = $documentData['image'] ?? null;
                $latitude = $documentData['latitude'] ?? null;
                $longitude = $documentData['longitude'] ?? null;
                $googleMapsUrl = sprintf('https://www.google.com/maps?q=%f,%f', $latitude, $longitude);

                $data[] = [
                    'name' => $name,
                    'timestamps' => $timestamps,
                    'image' => $image,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'googleMapsUrl' => $googleMapsUrl,
                    'id'=>$documentId
                ];

            }
        return view('pages.students', compact('data'));
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

        $siswaCollection = app('firebase.firestore')->database()->collection('users')->where('didaftarkan_oleh', '=', $nama_akun);
    
        // Mengambil dokumen dari collection dan mengubahnya menjadi array
        $siswaDocuments = $siswaCollection->documents();
        $list_siswa = [];
        foreach ($siswaDocuments as $document) {
            $list_siswa[] = $document->data();
        }
    
        return view('pages.student_form', ['list_siswa' => $list_siswa]);
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

        $siswaCollection = app('firebase.firestore')->database()->collection('users')->where('didaftarkan_oleh', '=', $nama_akun);
    
        // Mengambil dokumen dari collection dan mengubahnya menjadi array
        $siswaDocuments = $siswaCollection->documents();
        $list_siswa = [];
        foreach ($siswaDocuments as $document) {
            $list_siswa[] = $document->data();
        }

        try {
            $siswa = app('firebase.firestore')->database()->collection('students')->document($documentId)->snapshot();

            return view('pages.student_edit_form', compact('siswa', 'documentId', 'list_siswa'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data student: ' . $e->getMessage()], 500);
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
            $studentRef = $firestore->database()->collection('students');
            $tanggal = new Timestamp(new DateTime());

            $studentRef->add([
                'name' => $request->input('name'),
                'keterangan' => $request->input('keterangan'),
                'instruktur' => $name,
                'timestamps' => $tanggal,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'image' => $imagePath,
            ]);
            
            Alert::success('Data absensi siswa berhasil ditambahkan');
            return redirect()->route('siswa');
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
                $studentRef = $firestore->database()->collection('students')->document($documentId)->snapshot();
                $imagePath = $studentRef->get('image');
            }
        
                $firestore = app(Firestore::class);
                $studentRef = $firestore->database()->collection('students')->document($documentId);
                $tanggal = new Timestamp(new DateTime());

                $studentRef->update([
                    ['path' => 'name', 'value' => $request->input('name')],
                    ['path' => 'keterangan', 'value' => $request->input('keterangan')],
                    ['path' => 'timestamps', 'value' => $tanggal],
                    ['path' => 'latitude', 'value' => $request->input('latitude')],
                    ['path' => 'longitude', 'value' => $request->input('longitude')],
                    ['path' => 'image', 'value' => $imagePath],
                ]);

                Alert::success('Data absensi siswa berhasil diubah');
                return redirect()->route('siswa');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }
    
    public function delete($documentId)
    {
        try {
            app('firebase.firestore')->database()->collection('students')->document($documentId)->delete();
            Alert::success('Data absensi siswa berhasil dihapus');
            return redirect()->route('siswa');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data student: ' . $e->getMessage()], 500);
        }
    }
    
    //export excel untuk data bukti kehadiran siswa
    public function exportExcel()
    {
        return Excel::download(new StudentsExport(), 'bukti_kehadiran.xlsx');
    }
}
