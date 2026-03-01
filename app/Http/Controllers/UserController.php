<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'size' => ['nullable', 'integer', 'max:10']
        ]);

        try {
            $search = $request->search;
            $size = $request->input('size', 10);

            $users = User::query()
                    ->when($search, function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                            });
                        })
                    ->latest()
                    ->paginate($size);



            $customResponse = [
                'users' => UserResource::collection($users),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'from'         => $users->firstItem(),
                    'last_page'    => $users->lastPage(),
                    'path'         => $users->path(),
                    'per_page'     => $users->perPage(),
                    'to'           => $users->lastItem(),
                    'total'        => $users->total(),
                ],
                
            ];

            return ResponseHelper::jsonResponse(
                true, 
                'Data user berhasil didapatkan', 
                $customResponse, 
                200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function store(UserCreateRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'User berhasil ditambahkan', 
                new UserResource($user), 
                201);

        } catch (\Exception $e) {
            DB::rollBack();

            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if(!$user) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'User tidak ditemukan',
                    null,
                    404);
            }

            return ResponseHelper::jsonResponse(
                true, 
                'Detail user berhasil diambil', 
                new UserResource($user), 
                200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
        
            
            if(!$user) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'User tidak ditemukan',
                    null,
                    404);
            }

            $user->name = $request->name;

            if($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'User berhasil diubah', 
                new UserResource($user), 
                200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
        
            
            if(!$user) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'User tidak ditemukan',
                    null,
                    404);
            }

            $user->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'User berhasil dihapus', 
                new UserResource($user), 
                200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }

    }
}
