<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchCreateRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use Illuminate\Http\Exceptions\HttpResponseException;


class BranchController extends Controller
{

    private function findById(int $branchId)
    {
        $branch = Branch::query()->find($branchId);
        if (!$branch) {
            throw new HttpResponseException(response()->json([
                'message' => 'Branch not found',
            ], 404));
        }
        return $branch;
    }

    public function create(BranchCreateRequest $request)
    {
        $validatedData = $request->validated();
        $branch = Branch::query()->create($validatedData);
        return response()->json([
            'message' => 'Branch created successfully',
            'data' => $branch
        ], 201);
    }

    public function get()
    {
        $branches = Branch::query()->get();
        return response()->json([
            'data' => $branches
        ]);
    }

    public function getById(int $branchId)
    {
        $branch = $this->findById($branchId);
        return response()->json([
            'data' => $branch->toArray()
        ]);
    }

    public function delete(int $id)
    {
        $branch = $this->findById($id);
        $branch->delete();
        return response()->json([
            'message' => 'Branch deleted successfully',
            'data' => true
        ]);
    }

    public function update(BranchUpdateRequest $request, int $branchId)
    {
        $branch = $this->findById($branchId);
        $validatedData = $request->validated();
        $branch->update($validatedData);
        return response()->json([
            'message' => 'Branch updated successfully',
            'data' => $branch
        ]);
    }
}
