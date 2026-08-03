<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DesignService;
use Illuminate\Http\Request;

class DesignManagementController extends Controller
{
    public function __construct(private DesignService $designService) {}

    public function index()
    {
        $designs = $this->designService->getAllPaginated();
        return view('admin.designs.index', compact('designs'));
    }

    public function create()
    {
        return view('admin.designs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:svg,png,jpg,jpeg,webp|max:2048',
        ]);

        $this->designService->create($request->file('image'));

        return redirect()->route('admin.designs.index')
            ->with('success', 'تم إضافة التصميم بنجاح');
    }

    public function edit($id)
    {
        $design = $this->designService->findById($id);
        return view('admin.designs.edit', compact('design'));
    }

    public function update(Request $request, $id)
    {
        $design = $this->designService->findById($id);

        $request->validate([
            'image' => 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
        ]);

        $this->designService->update($design, $request->file('image'));

        return redirect()->route('admin.designs.index')
            ->with('success', 'تم تحديث التصميم بنجاح');
    }

    public function destroy($id)
    {
        $design = $this->designService->findById($id);
        $this->designService->delete($design);

        return redirect()->route('admin.designs.index')
            ->with('success', 'تم حذف التصميم بنجاح');
    }
}
