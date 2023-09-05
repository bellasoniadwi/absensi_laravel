<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Firestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
// use Kreait\Firebase\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;
use Google\Cloud\Core\Exception\GoogleException;
use Exception;
use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use PHPExcel;
use PhpOffice\PhpSpreadsheet\IOFactory as PHPExcel_IOFactory;




use App\Helpers\Helper;

class UserController extends Controller
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

        $collectionReference = $firestore->collection('users');
        $data = [];
        $query = $collectionReference->orderBy('name');
        $documents = $query->documents();

        foreach ($documents as $doc) {

            $documentData = $doc->data();
            $documentId = $doc->id();

            $name = $documentData['name'] ?? null;
            $email = $documentData['email'] ?? null;
            $nomor_induk = $documentData['nomor_induk'] ?? null;
            $telepon = $documentData['telepon'] ?? null;
            $role = $documentData['role'] ?? null;
            $jabatan = $documentData['jabatan'] ?? null;
            $image = $documentData['image'] ?? null;

            $data[] = [
                'name' => $name,
                'email' => $email,
                'nomor_induk' => $nomor_induk,
                'telepon' => $telepon,
                'role' => $role,
                'jabatan' => $jabatan,
                'image' => $image,
                'id'=>$documentId

            ];
        }

        return view('pages.users', compact('data'));
    }


    public function create_form() {
        return view('pages.user_form');
    }

    protected $auth;

    public function __construct(Auth $auth) {
       $this->auth = $auth;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telepon' => ['required', 'numeric'],
            'jabatan' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            // 'role' => ['required', 'string', 'max:255'],
            // 'image' => ['mimes:png,jpg,jpeg', 'max:2048']
        ]);
    }

    public function create(Request $request) {
        $this->validator($request->all())->validate();
        $userProperties = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'name' => $request->input('name'),
            'nomor_induk' => Helper::NomorKaryawanGenerator(),
            'telepon' => $request->input('telepon'),
            'jabatan' => $request->input('jabatan'),
            'role' => 'Karyawan',
            'image' => 'https://firebasestorage.googleapis.com/v0/b/absensi-sinarindo.appspot.com/o/images%2Fsgs.png?alt=media&token=d93b7e3d-162b-4eb2-8ddc-390dd0588e81'
        ];

        $createdUser = $this->auth->createUser($userProperties);

        $firestore = app(Firestore::class);
        $userRef = $firestore->database()->collection('users')->document($createdUser->uid);
        $userRef->set([
            'nomor_induk' => Helper::NomorKaryawanGenerator(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'telepon' => $request->input('telepon'),
            'jabatan' => $request->input('jabatan'),
            'role' => 'Karyawan',
            'image' => 'https://firebasestorage.googleapis.com/v0/b/absensi-sinarindo.appspot.com/o/images%2Fsgs.png?alt=media&token=d93b7e3d-162b-4eb2-8ddc-390dd0588e81'
        ]);

        Alert::success('Akun baru berhasil ditambahkan');
        return redirect()->route('user.index');
    }

    

    //START FUNCTION EDIT

    public function validator_edit(array $data)
    {
        return Validator::make($data, [
            'telepon' => ['required', 'numeric'],
            'jabatan' => ['required', 'string', 'max:255'],
        ]);
    }

    public function edit_form($documentId) {
        $userCollection = app('firebase.firestore')->database()->collection('users');
    
        // Mengambil dokumen dari collection dan mengubahnya menjadi array
        $userDocuments = $userCollection->documents();
        $list_user = [];
        foreach ($userDocuments as $document) {
            $list_user[] = $document->data();
        }

        try {
            $user = app('firebase.firestore')->database()->collection('users')->document($documentId)->snapshot();

            return view('pages.user_edit_form', compact('user', 'documentId', 'list_user'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data user: ' . $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $documentId)
    {
        try{
            $this->validator_edit($request->all())->validate();
        
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
                $userRef = $firestore->database()->collection('users')->document($documentId)->snapshot();
                $imagePath = $userRef->get('image');
            }
        
                $firestore = app(Firestore::class);
                $userRef = $firestore->database()->collection('users')->document($documentId);

                $userRef->update([
                    ['path' => 'jabatan', 'value' => $request->input('jabatan')],
                    ['path' => 'role', 'value' => $request->input('role')],
                    ['path' => 'telepon', 'value' => $request->input('telepon')],
                    ['path' => 'image', 'value' => $imagePath],
                ]);

                Alert::success('Data akun pengguna berhasil diubah');
                return redirect()->route('user.index');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    //END FUNCTION EDIT

    
    // START FUNCTION DELETE FOR ONLY FIRESTORE COLLECTIONS USERS
    // public function deleteUser($documentId)
    // {
    //     try {
    //         app('firebase.firestore')->database()->collection('users')->document($documentId)->delete();
    //         Alert::success('Data akun pengguna berhasil dihapus');
    //         return redirect()->route('user.index');
    //     } catch (FirebaseException $e) {
    //         return response()->json(['message' => 'Gagal menghapus data akun pengguna: ' . $e->getMessage()], 500);
    //     }
    // }
    // END FUNCTION DELETE FOR ONLY FIRESTORE COLLECTIONS USERS

    // START FUNCTION DELETE FOR FIRESTORE COLLECTIONS USERS AND USERS AUTHENTICATION
    public function delete($documentId, Auth $firebaseAuth)
    {
        try {
            // Get the user's email from Firestore
            $userDocument = app('firebase.firestore')->database()->collection('users')->document($documentId)->snapshot();
            $userEmail = $userDocument->data()['email'];

            // Delete the user document from Firestore
            app('firebase.firestore')->database()->collection('users')->document($documentId)->delete();

            // Delete the user's authentication record from Firebase Authentication
            $user = $firebaseAuth->getUserByEmail($userEmail);
            $firebaseAuth->deleteUser($user->uid);

            Alert::success('Data akun pengguna berhasil dihapus');
            return redirect()->route('user.index');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data akun pengguna: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    // END FUNCTION DELETE FOR FIRESTORE COLLECTIONS USERS AND USERS AUTHENTICATION
    

    //export Data Akun Pengguna
    public function exportUsers()
    {
        return Excel::download(new UsersExport(), 'users.xlsx');
    }


    //Start import Data Akun Pengguna
    //Masih error ==== Error rendering 'projects/{project=*}/databases/{database=*}': expected binding 'project' to match segment '{project=*}', instead got '' Provided bindings: Array ( [project] => [database] => (default) )
    public function importUsers(Request $request, FirestoreClient $firestore, FirebaseAuth $auth)
    {
        $import = new UsersImport($firestore, $auth);
        Excel::import($import, $request->file('users_excel'));
        
        return redirect()->back()->with('success', 'Users imported successfully.');
    }
    //End import Data Akun Pengguna

    //START IMPORT DATA AKUN PENGGUNA
//     public function import(Request $request)
// {
    
//     // Initialize Firebase
//     $factory = (new Factory)->withServiceAccount(__DIR__.'\absensi-sinarindo-firebase-adminsdk-ox7j2-b4a007fb1c.json');
    
//     $firestore = app(Firestore::class);
//     $auth = $factory->createAuth();
    
//     if ($request->hasFile('users_excel')) {
//         $path = $request->file('users_excel')->getRealPath();
        
//         // Use the import method from the Maatwebsite Excel facade
//         $data = Excel::import([], $path)->toArray();
        
//         if (count($data) > 0) {
//             foreach ($data as $key => $value) {
//                 $userData = [
//                     'email' => $value['email'],
//                     'password' => $value['password'],
//                 ];
    
//                 try {
//                     // Create a user in Firebase Authentication
//                     $createdUser = $auth->createUser($userData);
//                     $uid = $createdUser->uid;
    
//                     // Store user data in Firestore
//                     $firestore->collection('users')->document($uid)->set([
//                         'nomor_induk' => $value['nomor_induk'],
//                         'name' => $value['name'],
//                         'email' => $value['email'],
//                         'password' => $value['password'],
//                         'telepon' => $value['telepon'],
//                         'jabatan' => $value['jabatan'],
//                         'role' => $value['role'],
//                         'image' => $value['image'],
//                     ]);
                    
//                 } catch (GoogleException $e) {
//                     return response()->json(['error' => $e->getMessage()], 401);
//                 } catch (GoogleException $e) {
//                     return response()->json(['error' => $e->getMessage()], 500);
//                 }
//             }
    
//             return response()->json(['success' => 'Data imported successfully'], 200);
//         } else {
//             return response()->json(['error' => 'No data found in the Excel file'], 404);
//         }
//     } else {
//         return response()->json(['error' => 'Please select an Excel file'], 400);
//     }
// }
    
    //END IMPORT DATA AKUN PENGGUNA

    //IMPORT DATA EXCEL PAKE PHPOFFICE/PHPSPREADSHEET
    //Keterangan: bisa import user ke collections "users" dan kolom header masih ikut masuk ke firestore
    public function importExcel(Request $request)
{
    // Load the Excel file
    $objPHPExcel = PHPExcel_IOFactory::load('C:\Users\ayian\Downloads\users_excel.xlsx');
    $worksheet = $objPHPExcel->getActiveSheet();
    
    // Initialize Firestore
    $firestore = new FirestoreClient([
        'projectId' => 'absensi-sinarindo',
    ]);
    
    // Specify the Firestore collection
    $collection = $firestore->collection('users');

    // Get all rows starting from the 2nd row (assuming the 1st row is headers)
    $excelData = $worksheet->toArray(null, true, true, true);
    
    // Iterate through each row and add it to Firestore
    foreach ($excelData as $rowData) {
        // Transform Excel data into Firestore data format
        $firebaseData = [
            'nomor_induk' => $rowData['A'],
            'name' => $rowData['B'],
            'email' => $rowData['C'],
            'password' => $rowData['D'],
            'telepon' => $rowData['E'],
            'jabatan' => $rowData['F'],
            'role' => $rowData['G'],
            'image' => $rowData['H'],
        ];

        // Add the data to Firestore
        $collection->add($firebaseData);
    }
    return redirect()->back()->with('success', 'Users imported successfully.');
}

    
    
}
