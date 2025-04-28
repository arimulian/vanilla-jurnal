<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class CustomerController extends Controller
{


    private function findById($id): Customer
    {
        $customer = Customer::where('id', '=', $id)->first();
        if (!$customer) {
            throw new HttpResponseException(response()->json([
                'message' => 'Customer not found'
            ], 404));
        }
        return $customer;
    }

    public function create(CustomerCreateRequest $request): JsonResponse
    {

        $validatedData = $request->validated();
        $customer = new Customer();
        if ($customer->where('name', '=', $validatedData['name'])->exists()) {
            return response()->json([
                'message' => 'Customer already exists',
            ], 400);
        }
        $data = $customer->query()->create($validatedData);
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $data->get(),
        ], 201);
    }

    public function get(): JsonResponse
    {
        $customers = Customer::get();
        if ($customers->isEmpty()) {
            return response()->json([
                'message' => 'No customers found',
            ], 404);
        }
        return response()->json([
            'data' => $customers->toArray(),
        ], 200);
    }

    public function getById($id)
    {
        $customer = $this->findById($id);
        return response()->json([
            'data' => $customer,
        ], 200);
    }

    public function update(CustomerUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $customer = $this->findById($id);
        $customer->query()->update($validatedData);
        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer->query()->get()->toArray(),
        ], 200);
    }

    public function delete(string $id): JsonResponse
    {
        $this->findById($id);
        return response()->json([
            'message' => 'Customer deleted successfully',
            'data' => true,
        ], 200);
    }
}
