<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreCompanySupervisorRequest;
use App\Http\Requests\StoreLawyerRequest;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\CompanySupervisor;
use App\Models\Lawyer;
use App\Models\Profile;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function addCompany(StoreCompanyRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company = Company::create($data);

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company,
        ], 201);
    }


    public function addCompanySupervisor(StoreCompanySupervisorRequest $request)
{
    $data = $request->validated();

    $user = DB::transaction(function () use ($request, $data) {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'company_supervisor',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }

        Profile::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'preferred_language' => $data['preferred_language'],
            'image' => $imagePath,
        ]);

        CompanySupervisor::create([
            'user_id' => $user->id,
            'company_id' => $data['company_id'],
            'position' => $data['position'],
        ]);

        return $user->load('profile', 'companySupervisor');
    });

    return response()->json([
        'message' => 'Company supervisor created successfully',
        'user' => $user,
    ], 201);
}
       public function addLawyer(StoreLawyerRequest $request)
       {
           $data = $request->validated();

           $user = DB::transaction(function () use ($request, $data) {
               $user = User::create([
                   'email' => $data['email'],
                   'password' => Hash::make($data['password']),
                   'role' => 'lawyer',
               ]);

               $imagePath = null;

               if ($request->hasFile('image')) {
                   $imagePath = $request->file('image')->store('profiles', 'public');
               }

               Profile::create([
                   'user_id' => $user->id,
                   'name' => $data['name'],
                   'phone' => $data['phone'],
                   'preferred_language' => $data['preferred_language'],
                   'image' => $imagePath,
               ]);

               Lawyer::create([
                   'user_id' => $user->id,
                   'license_number' => $data['license_number'],
                   'specialization' => $data['specialization'],
               ]);

               return $user->load('profile', 'lawyer');
           });

           return response()->json([
               'message' => 'Lawyer created successfully',
               'user' => $user,
           ], 201);
       }

      public function addWorker(StoreWorkerRequest $request)
      {
          $data = $request->validated();

          $supervisor = $request->user()->companySupervisor;

          if (! $supervisor) {
              return response()->json([
                  'message' => 'Only company supervisor can add workers',
              ], 403);
          }

          $user = DB::transaction(function () use ($request, $data, $supervisor) {
              $user = User::create([
                  'email' => $data['email'],
                  'password' => Hash::make($data['password']),
                  'role' => 'worker',
              ]);

              $imagePath = null;

              if ($request->hasFile('image')) {
                  $imagePath = $request->file('image')->store('profiles', 'public');
              }

              Profile::create([
                  'user_id' => $user->id,
                  'name' => $data['name'],
                  'phone' => $data['phone'],
                  'preferred_language' => $data['preferred_language'],
                  'image' => $imagePath,
              ]);

              Worker::create([
                  'user_id' => $user->id,
                  'company_id' => $supervisor->company_id,
                  'nationality' => $data['nationality'],
                  'job_title' => $data['job_title'],
              ]);

              return $user->load('profile', 'worker');
          });

          return response()->json([
              'message' => 'Worker created successfully',
              'user' => $user,
          ], 201);
      }

      public function registerAdmin(Request $request)
      {
          $data = $request->validate([
              'email' => 'required|email|unique:users,email',
              'password' => 'required|string|min:8|confirmed',

              'name' => 'required|string|max:255',
              'phone' => 'required|string|max:20',
              'preferred_language' => 'required|string|max:20',
              'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
          ]);

          $user = DB::transaction(function () use ($request, $data) {
              $user = User::create([
                  'email' => $data['email'],
                  'password' => Hash::make($data['password']),
                  'role' => 'admin',
              ]);

              $imagePath = null;

              if ($request->hasFile('image')) {
                  $imagePath = $request->file('image')->store('profiles', 'public');
              }

              Profile::create([
                  'user_id' => $user->id,
                  'name' => $data['name'],
                  'phone' => $data['phone'],
                  'preferred_language' => $data['preferred_language'],
                  'image' => $imagePath,
              ]);

              return $user->load('profile');
          });

          return response()->json([
              'message' => 'Admin registered successfully',
              'user' => $user,
          ], 201);
      }

      public function login(LoginRequest $request)
      {
          $data = $request->validated();

          $user = User::where('email', $data['email'])->first();

          if (! $user || ! Hash::check($data['password'], $user->password)) {
              throw ValidationException::withMessages([
                  'email' => ['Invalid email or password'],
              ]);
          }

          $token = $user->createToken('auth_token')->plainTextToken;

          return response()->json([
              'message' => 'Login successful',
              'token' => $token,
              'user' => new UserResource($user),
          ]);
      }

      public function me()
      {
          return response()->json([
              'user' => new UserResource(auth()->user()),
          ]);
      }

 public function logout(Request $request)
 {
     $request->user()->currentAccessToken()->delete();

     return response()->json([
         'message' => 'Logged out successfully',
     ]);
 }
}
