<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\HeadOfFamilyCreateRequest;
use App\Http\Requests\HeadOfFamilyUpdateRequest;
use App\Http\Resources\HeadOfFamilyResource;
use App\Models\HeadOfFamily;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HeadOfFamilyController extends Controller
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

            $query = HeadOfFamily::where(function($q) use ($search) {
                        if($search) {
                            $q->whereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('email', 'like', '%' . $search . '%');
                            })
                            ->orWhere('phone_number', 'like', '%' . $search . '%')
                            ->orWhere('identity_number', 'like', '%' . $search . '%');
                        }
                    })
                    ->latest()
                    ->paginate($size)
                    ->withQueryString();



            $customResponse = [
                'head_of_family' => HeadOfFamilyResource::collection($query),
                'meta' => [
                    'current_page' => $query->currentPage(),
                    'from'         => $query->firstItem(),
                    'last_page'    => $query->lastPage(),
                    'path'         => $query->path(),
                    'per_page'     => $query->perPage(),
                    'to'           => $query->lastItem(),
                    'total'        => $query->total(),
                ],
                
            ];

            return ResponseHelper::jsonResponse(
                true, 
                'Data Kepala Keluarga Berhasil Diambil', 
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

    public function store(HeadOfFamilyCreateRequest $request)
    {
        
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $headOfFamily = new HeadOfFamily;
            $headOfFamily->user_id = $user->id;
            $headOfFamily->profile_picture = $request->file('profile_picture')->store('assets/head-of-families', 'public');
            $headOfFamily->identity_number = $request->identity_number;
            $headOfFamily->gender = $request->gender;
            $headOfFamily->date_of_birth = $request->date_of_birth;
            $headOfFamily->phone_number = $request->phone_number;
            $headOfFamily->occupation = $request->occupation;
            $headOfFamily->martial_status = $request->martial_status;

            $headOfFamily->save();

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'Kepala Keluarga Berhasil Ditambahkan', 
                new HeadOfFamilyResource($headOfFamily), 
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
            $headOfFamily = HeadOfFamily::where('id', $id)->first();

            if(!$headOfFamily) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Kepala Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            return ResponseHelper::jsonResponse(
                true, 
                'Detail Kepala Keluarga Berhasil Diambil', 
                new HeadOfFamilyResource($headOfFamily), 
                200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function update(HeadOfFamilyUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
           $headOfFamily = HeadOfFamily::find($id);

           if(!$headOfFamily) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Kepala Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            if($request->hasFile('profile_picture')) {
                if ($headOfFamily->profile_picture) {
                    Storage::disk('public')->delete($headOfFamily->profile_picture);
                }

                $headOfFamily->profile_picture = $request->file('profile_picture')->store(
                    'assets/head-of-families',
                    'public'
                );
            }

            $headOfFamily->identity_number = $request->identity_number;
            $headOfFamily->gender = $request->gender;
            $headOfFamily->date_of_birth = $request->date_of_birth;
            $headOfFamily->phone_number = $request->phone_number;
            $headOfFamily->occupation = $request->occupation;
            $headOfFamily->martial_status = $request->martial_status;
            $headOfFamily->save();

            $user = $headOfFamily->user;
            $user->name = $request->name;
            $user->email = $request->email;

            if($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();
            

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'User berhasil diubah', 
                new HeadOfFamilyResource($headOfFamily), 
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
            $headOfFamily = HeadOfFamily::find($id);

            if(!$headOfFamily) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Kepala Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            if ($headOfFamily->profile_picture && Storage::disk('public')->exists($headOfFamily->profile_picture)) {
                Storage::disk('public')->delete($headOfFamily->profile_picture);
            }
            $headOfFamily->delete();
            

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'User berhasil dihapus', 
                new HeadOfFamilyResource($headOfFamily), 
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
