<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SocialAssistanceRecipientCreateRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdate;
use App\Http\Requests\SocialAssistanceUpdateRequest;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialAssistanceRecipientController extends Controller
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

            $query = SocialAssistanceRecipient::with('socialAssistance', 'headOfFamily');

            if ($search) {
                $query->whereHas('headOfFamily', function ($headOfFamilyQuery) use ($search) {
                    $headOfFamilyQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                    });
                });
            }

            $query = $query->latest()->paginate($size);

             $customResponse = [
                'social_assistance_recipient' => SocialAssistanceRecipientResource::collection($query),
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
                'Data Penerima Bantuan Sosial Berhasil diambil', 
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

    public function store(SocialAssistanceRecipientCreateRequest $request)
    {
        DB::beginTransaction();

        try {
           $socialAssistanceRecipient = new SocialAssistanceRecipient;
           $socialAssistanceRecipient->social_assistance_id = $request->social_assistance_id;
           $socialAssistanceRecipient->head_of_family_id = $request->head_of_family_id;
           $socialAssistanceRecipient->amount = $request->amount;
           $socialAssistanceRecipient->reason = $request->reason;
           $socialAssistanceRecipient->bank = $request->bank;
           $socialAssistanceRecipient->account_number = $request->account_number;
           $socialAssistanceRecipient->proof = $request->proof;
           $socialAssistanceRecipient->status = $request->status;

           $socialAssistanceRecipient->save();

           DB::commit();

           return ResponseHelper::jsonResponse(
                true, 
                'Data Penerima Bantuan Sosial Berhasil Ditambahkan', 
                new SocialAssistanceRecipientResource($socialAssistanceRecipient), 
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

    public function show($id)
    {
        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::where('id', $id)->first();

             if(!$socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Data Penerima Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Penerima Bantuan Sosial Berhasil Ditemukan',
                new SocialAssistanceRecipientResource($socialAssistanceRecipient),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function update(SocialAssistanceUpdateRequest $request, $id)
    {
        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::where('id', $id)->first();

            if(!$socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Data Penerima Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }

            $socialAssistanceRecipient->social_assistance_id = $request->social_assistance_id;
            $socialAssistanceRecipient->head_of_family_id = $request->head_of_family_id;
            $socialAssistanceRecipient->amount = $request->amount;
            $socialAssistanceRecipient->reason = $request->reason;
            $socialAssistanceRecipient->bank = $request->bank;
            $socialAssistanceRecipient->account_number = $request->account_number;
            $socialAssistanceRecipient->proof = $request->proof;
            $socialAssistanceRecipient->status = $request->status;

            $socialAssistanceRecipient->save();

             return ResponseHelper::jsonResponse(
                true, 
                'Data Penerima Bantuan Sosial Berhasil Diupdate', 
                new SocialAssistanceRecipientResource($socialAssistanceRecipient), 
                200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }

    public function destroy($id)
    {
        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::where('id', $id)->first();

            if(!$socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Data Penerima Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }

            $socialAssistanceRecipient->delete();
            return ResponseHelper::jsonResponse(
                true, 
                'Data Penerima Bantuan Sosial Berhasil Dihapus', 
                new SocialAssistanceRecipientResource($socialAssistanceRecipient), 
                200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false, 
                $e->getMessage(), 
                null, 
                500);
        }
    }
}
