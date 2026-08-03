<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPoto;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private ProductTemplateService $templateService,
    ) {}

    /**
     * Create a new product with variants and optional template print areas.
     */
    public function create(array $data, string $productType, UploadedFile $photo): Product
    {
        $isCustom = $productType === 'custom';

        $product = new Product;
        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->description = $data['description'];
        $product->category_id = $data['category_id'] ?? null;
        $product->type = $productType;
        $product->is_designable = $isCustom ? true : ($data['is_designable'] ?? false);
        $product->print_cost_type = $isCustom ? 'per_area' : ($data['print_cost_type'] ?? null);
        $product->product_template_id = $isCustom ? ($data['product_template_id'] ?? null) : null;
        $product->quantity = 0;
        $product->imagepath = $this->uploadFile($photo, 'uploads');

        $product->save();

        $this->syncVariants($product, $data['variants'] ?? []);

        if ($isCustom && $product->product_template_id) {
            $template = ProductTemplate::find($product->product_template_id);
            if ($template) {
                $this->templateService->createPrintAreasFromTemplate($product, $template->key);
            }
        }

        return $product;
    }

    /**
     * Update an existing product, its image, and its variants.
     */
    public function update(Product $product, array $data, ?UploadedFile $photo = null): Product
    {
        $product->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            'type' => $data['type'] ?? $product->type,
            'is_designable' => $data['is_designable'] ?? false,
            'print_cost_type' => $data['print_cost_type'] ?? null,
            'product_template_id' => $data['product_template_id'] ?? $product->product_template_id,
        ]);

        if ($photo) {
            $this->deleteFile($product->imagepath);
            $product->imagepath = $this->uploadFile($photo, 'uploads');
            $product->save();
        }

        $this->syncVariants($product, $data['variants'] ?? []);

        return $product->fresh();
    }

    /**
     * Delete a product and its associated files and variants.
     */
    public function delete(Product $product): void
    {
        $this->deleteFile($product->imagepath);
        $product->variants()->delete();
        $product->delete();
    }

    /**
     * Upload a product photo for the multi-image gallery.
     */
    public function storeProductPhoto(ProductPoto $photo, UploadedFile $file, ?string $color, ?string $viewName): ProductPoto
    {
        $photo->color = $color;
        $photo->view_name = $viewName;
        $photo->imagepath = $this->uploadFile($file, 'uploads');
        $photo->save();

        return $photo;
    }

    /**
     * Delete a product photo and its file.
     */
    public function deleteProductPhoto(ProductPoto $photo): void
    {
        $this->deleteFile($photo->imagepath);
        $photo->delete();
    }

    /**
     * Synchronize variants for a product: create new, update existing, delete removed.
     */
    public function syncVariants(Product $product, array $variantsData): void
    {
        $existingIds = $product->variants->pluck('id')->toArray();
        $incomingIds = [];

        foreach ($variantsData as $variant) {
            if (empty($variant['size']) || empty($variant['color'])) {
                continue;
            }

            if (!empty($variant['id']) && in_array($variant['id'], $existingIds)) {
                ProductVariant::where('id', $variant['id'])->update([
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'quantity' => $variant['quantity'] ?? 0,
                    'material' => $variant['material'] ?? null,
                    'weight' => $variant['weight'] ?? null,
                ]);
                $incomingIds[] = $variant['id'];
            } else {
                $new = $product->variants()->create([
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'quantity' => $variant['quantity'] ?? 0,
                    'material' => $variant['material'] ?? null,
                    'weight' => $variant['weight'] ?? null,
                ]);
                $incomingIds[] = $new->id;
            }
        }

        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            ProductVariant::whereIn('id', $toDelete)->delete();
        }
    }

    /**
     * Generate a safe unique filename using UUID and the original file extension.
     */
    public function uploadFile(UploadedFile $file, string $directory): string
    {
        $fileName = Str::uuid().'.'.$file->extension();
        $file->move(public_path($directory), $fileName);

        return $directory.'/'.$fileName;
    }

    /**
     * Safely delete a file from disk if it exists.
     */
    public function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
