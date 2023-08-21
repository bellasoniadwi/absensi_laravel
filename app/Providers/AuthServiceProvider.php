<?php

namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Firebase\Guard as FirebaseGuard;
use App\Firebase\FirebaseUserProvider;
class AuthServiceProvider extends ServiceProvider {
   protected $policies = [
      // 'App\Model' => 'App\Policies\ModelPolicy',
   ];
   
   public function boot() {
      $this->registerPolicies();
      \Illuminate\Support\Facades\Auth::provider('firebaseuserprovider', function($app, array $config) {
         return new FirebaseUserProvider($app['hash'], $config['model']);
      });

      Gate::define('superadmin', function ($user) {
         $id = $user->localId;
 
         $firestore = app('firebase.firestore');
         $database = $firestore->database();
         $userDocRef = $database->collection('users')->document($id);
         $userSnapshot = $userDocRef->snapshot();
 
         $userData = $userSnapshot->data();
 
         return $userSnapshot->exists() && isset($userData['role']) && $userData['role'] === "Superadmin";
     });

     Gate::define('instruktur', function ($user) {
         $id = $user->localId;

         $firestore = app('firebase.firestore');
         $database = $firestore->database();
         $userDocRef = $database->collection('users')->document($id);
         $userSnapshot = $userDocRef->snapshot();

         $userData = $userSnapshot->data();

         return $userSnapshot->exists() && isset($userData['role']) && $userData['role'] === "Instruktur";
   });
   }
}