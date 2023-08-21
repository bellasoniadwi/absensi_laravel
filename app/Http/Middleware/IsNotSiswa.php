<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsNotSiswa
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();
            $userData = $userSnapshot->data();

            if ($userSnapshot->exists() && isset($userData['role']) && $userData['role'] === "Siswa") {
                return redirect()->route('notauthorize');
            }
        }

        return $next($request);
    }
}
