<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SocialAssistanceCreateRequest;
use App\Http\Requests\SocialAssistanceUpdateRequest;
use App\Http\Resources\SocialAssistanceResource;
use App\Models\SocialAssistance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SocialAssistanceController extends Controller
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

           $query = SocialAssistance::where(function ($q) use ($search) {
                if($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('provider', 'like', '%' . $search . '%');
                        }
                })
                ->latest()
                ->paginate($size);
            
        $customResponse = [
            'social_assistance' => SocialAssistanceResource::collection($query),
            'meta' => [
                    'current_page' => $query->currentPage(),
                    'from'         => $query->firstItem(),
                    'last_page'    => $query->lastPage(),
                    'path'         => $query->path(),
                    'per_page'     => $query->perPage(),
                    'to'           => $query->lastItem(),
                    'total'        => $query->total(),
            ]
        ];

        return ResponseHelper::jsonResponse(
            true,
            'Data Bantuan Sosial Berhasil Diambil',
            $customResponse,
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

    public function store(SocialAssistanceCreateRequest $request)
    {
        DB::beginTransaction();

        try {
            $socialAssistance = SocialAssistance::create([
                'thumbnail' => $request->file('thumbnail')->store('assets/social-assistance', 'public'),
                'name' => $request->name,
                'category' => $request->category,
                'amount' => $request->amount,
                'provider' => $request->provider,
                'description' => $request->description,
                'is_available' => $request->is_available ? true : false,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                true,
                'Bantuan Sosial Berhasil Ditambahkan',
                new SocialAssistanceResource($socialAssistance),
                201
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
            $socialAssistance = SocialAssistance::where('id', $id)->first();

             if(!$socialAssistance) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }

            return ResponseHelper::jsonResponse(
                true,
                'Detail Bantuan Sosial Berhasil Ditampilkan',
                new SocialAssistanceResource($socialAssistance),
                201
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
        DB::beginTransaction();
        
        try {
            $socialAssistance = SocialAssistance::find($id);

            if(!$socialAssistance) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }
            
            if($request->hasFile('thumbnail')) {
                if($socialAssistance->thumbnail) {
                    Storage::disk('public')->delete($socialAssistance->thumbnail);
                }

                $socialAssistance->thumbnail = $request->file('thumbnail')->store('assets/social-assistance', 'public');
            }

           $socialAssistance->name = $request->name;
           $socialAssistance->category = $request->category;
           $socialAssistance->amount = $request->amount;
           $socialAssistance->provider = $request->provider;
           $socialAssistance->description = $request->description;
           $socialAssistance->is_available = $request->is_available;

           $socialAssistance->save();

           DB::commit();

           return ResponseHelper::jsonResponse(
                true,
                'Bantuan Sosial Berhasil Diupdate',
                new SocialAssistanceResource($socialAssistance),
                201
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
            $socialAssistance = SocialAssistance::find($id);

            if(!$socialAssistance) {
                return ResponseHelper::jsonResponse(
                    false, 
                    'Bantuan Sosial Tidak Ditemukan',
                    null,
                    404);
            }

            if($socialAssistance->thumbnail && Storage::disk('public')->exists($socialAssistance->thumbnail)) {
                Storage::disk('public')->delete($socialAssistance->thumbnail);
            }

            $socialAssistance->delete();

            return ResponseHelper::jsonResponse(
                true, 
                'Dana Bencana Berhasil Dihapus', 
                new SocialAssistanceResource($socialAssistance), 
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
