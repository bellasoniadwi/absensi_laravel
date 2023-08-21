<?php

namespace App\Http\Middleware;

use Closure;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();

            // Mengakses data dari snapshot menggunakan metode data()
            $userData = $userSnapshot->data();

            if ($userSnapshot->exists() && isset($userData['role']) && $userData['role'] === "Superadmin") {
                return $next($request);
            }
        }

        return redirect()->route('login');
    }
}
