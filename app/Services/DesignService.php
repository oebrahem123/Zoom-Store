<?php

namespace App\Services;

use App\Models\Design;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class DesignService
{
    public function getAvailableDesigns(): array
    {
        return Design::all()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'image' => asset($d->image),
        ])->toArray();
    }

    public function getAllPaginated(): LengthAwarePaginator
    {
        return Design::latest()->paginate(20);
    }

    public function findById(int $id): Design
    {
        return Design::findOrFail($id);
    }

    public function create(UploadedFile $image): Design
    {
        $path = $this->uploadImage($image);
        $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

        return Design::create([
            'name' => $name,
            'image' => $path,
        ]);
    }

    public function update(Design $design, ?UploadedFile $image = null): Design
    {
        if ($image) {
            $this->deleteImageFile($design->image);
            $design->image = $this->uploadImage($image);
        }

        $design->save();
        return $design;
    }

    public function delete(Design $design): void
    {
        $this->deleteImageFile($design->image);
        $design->delete();
    }

    public function uploadImage(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = time() . '-' . uniqid() . '.' . $ext;
        $file->move(public_path('uploads/designs'), $name);
        return 'uploads/designs/' . $name;
    }

    public function deleteImageFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
