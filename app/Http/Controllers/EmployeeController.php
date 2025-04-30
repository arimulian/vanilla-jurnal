<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeController extends Controller
{

    private function findById($id): Employee
    {
        $employee = Employee::where('id', '=', $id)->first();
        if (!$employee) {
            throw new HttpResponseException(response()->json([
                'message' => 'Employee not found'
            ], 404));
        }
        return $employee;
    }

    public function create(EmployeeCreateRequest $request)
    {
        $validatedData = $request->validated();
        $employee = Employee::query()->create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'salary' => $validatedData['salary'],
            'hire_date' => $validatedData['hire_date'],
            'status' => $validatedData['status'],
        ]);
        return response()->json([
            'message' => 'Employee created successfully',
            'data' => $employee,
        ], 201);
    }

    public function get()
    {
        $employees = Employee::query()->get();
        if ($employees->isEmpty()) {
            return response()->json([
                'message' => 'No employees found',
            ], 404);
        }
        return response()->json([
            'data' => $employees,
        ], 200);
    }

    public function getById(int $id)
    {
        $employee = $this->findById($id);
        return response()->json([
            'data' => $employee,
        ], 200);
    }

    public function update(EmployeeUpdateRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $employee = $this->findById($id);
        $employee->update([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'salary' => $validatedData['salary'],
            'hire_date' => $validatedData['hire_date'],
            'status' => $validatedData['status'],
        ]);
        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $employee,
        ]);
    }

    public function delete(int $id)
    {
        $employee = $this->findById($id);
        $employee->delete();
        return response()->json([
            'message' => 'Employee deleted successfully',
            'data' => true,
        ]);
    }
}
