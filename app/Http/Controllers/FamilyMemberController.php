<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\FamilyMemberCreateRequest;
use App\Http\Requests\FamilyMemberUpdateRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
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

            $query = FamilyMember::where(function($q) use ($search) {
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
            ->paginate($size);

            $customResponse = [
                'family_member' => FamilyMemberResource::collection($query),
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
                'Family Member Berhasil Diambil', 
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

    public function store(FamilyMemberCreateRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $familyMember = new FamilyMember();
            $familyMember->user_id = $user->id;
            $familyMember->head_of_family_id = $request->head_of_family_id;
            $familyMember->profile_picture = $request->file('profile_picture')->store('assets/family-member', 'public');
            $familyMember->identity_number = $request->identity_number;
            $familyMember->gender = $request->gender;
            $familyMember->date_of_birth = $request->date_of_birth;
            $familyMember->phone_number = $request->phone_number;
            $familyMember->occupation = $request->occupation;
            $familyMember->martial_status = $request->martial_status;
 
            $familyMember->save();

            DB::commit();

            return ResponseHelper::jsonResponse(
                true,
                'Data Anggota Keluarga Berhasil Ditambahkan',
                new FamilyMemberResource($familyMember),
                201,
            );

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
            $familyMember = FamilyMember::where('id', $id)->first();
            
            if(!$familyMember) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Anggota Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            return ResponseHelper::jsonResponse(
                true, 
                'Detail Anggota Keluarga Berhasil Diambil', 
                new FamilyMemberResource($familyMember), 
                200);
        } catch (\Exception $e) {
        return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function update(FamilyMemberUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $familyMember = FamilyMember::find($id);

             if(!$familyMember) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Anggota Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            if($request->hasFile('profile_picture')) {
                if ($familyMember->profile_picture) {
                    Storage::disk('public')->delete($familyMember->profile_picture);
                }

                $familyMember->profile_picture = $request->file('profile_picture')->store(
                    'assets/family-member',
                    'public'
                );
            }

            $familyMember->identity_number = $request->identity_number;
            $familyMember->gender = $request->gender;
            $familyMember->date_of_birth = $request->date_of_birth;
            $familyMember->phone_number = $request->phone_number;
            $familyMember->occupation = $request->occupation;
            $familyMember->martial_status = $request->martial_status;
            $familyMember->relation = $request->relation;

            $familyMember->save();

            $user = $familyMember->user;
            $user->name = $request->name;
            $user->email = $request->email;

            if($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return ResponseHelper::jsonResponse(
                true,
                'Data Anggota Keluarga Berhasil Ditambahkan',
                new FamilyMemberResource($familyMember),
                201,
            );

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
            $familyMember = FamilyMember::find($id);

            if(!$familyMember) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Data Anggota Keluarga Tidak Ditemukan',
                    null,
                    404);
            }

            if ($familyMember->profile_picture && Storage::disk('public')->exists($familyMember->profile_picture)) {
                Storage::disk('public')->delete($familyMember->profile_picture);
            }
            $familyMember->delete();
            

            DB::commit();

            return ResponseHelper::jsonResponse(
                true, 
                'Data Anggota Keluarga berhasil dihapus', 
                new FamilyMemberResource($familyMember), 
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
