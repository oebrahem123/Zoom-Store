<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService,
    ) {}

    public function index(Request $request): View
    {
        $employees = $this->employeeService->getEmployees($request->only(['search', 'role_id', 'is_active']));
        $roles = Role::where('name', '!=', 'customer')->get();

        return view('admin.employees.index', compact('employees', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::where('name', '!=', 'customer')->get();
        $permissions = Permission::all()->groupBy('group');

        return view('admin.employees.create', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user = $this->employeeService->createEmployee($validated);

        if ($request->has('permissions')) {
            $this->employeeService->saveEmployeePermissions($user, $request->input('permissions', []));
        }

        return redirect()->route('admin.employees.index')
            ->with('success', __('Employee created successfully.'));
    }

    public function edit(User $employee): View
    {
        $roles = Role::where('name', '!=', 'customer')->get();
        $permissions = Permission::all()->groupBy('group');
        $userPermissions = $employee->userPermissions()
            ->with('permission')
            ->get()
            ->keyBy('permission_id');

        return view('admin.employees.edit', compact('employee', 'roles', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        try {
            $this->employeeService->updateEmployee($employee, $validated, Auth::guard('admin')->user());

            $this->employeeService->saveEmployeePermissions($employee, $request->input('permissions', []));
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->validator);
        }

        return redirect()->route('admin.employees.index')
            ->with('success', __('Employee updated successfully.'));
    }

    public function toggleStatus(User $employee): RedirectResponse
    {
        try {
            $updated = $this->employeeService->toggleStatus($employee, Auth::guard('admin')->user());
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->validator->errors()->first());
        }

        $status = $updated->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.employees.index')
            ->with('success', __("Employee {$status} successfully."));
    }
}
