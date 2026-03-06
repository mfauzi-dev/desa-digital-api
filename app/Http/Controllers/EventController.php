<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
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

            $query = Event::with('eventParticipants');

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $query = $query->latest()->paginate($size);

            $customResponse = [
                'social_assistance_recipient' => EventResource::collection($query),
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
                'Event Berhasil Diambil', 
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
