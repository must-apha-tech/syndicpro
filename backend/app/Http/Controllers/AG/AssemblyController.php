<?php

namespace App\Http\Controllers\AG;

use App\Http\Controllers\BaseController;
use App\Models\Assemblee;
use App\Models\PointOrdre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AssemblyController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $assemblies = Assemblee::with('residence')->orderBy('date_heure', 'desc')->get();
        return $this->sendResponse($assemblies, 'Assemblies retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'residence_id'  => 'required|exists:residences,id',
            'type'          => 'required|in:ordinaire,extraordinaire',
            'date_heure'    => 'required|date',
            'lieu'          => 'required|string',
            'quorum_requis' => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $assembly = Assemblee::create($request->all());

        return $this->sendResponse($assembly, 'Assembly scheduled successfully.', 201);
    }
}
