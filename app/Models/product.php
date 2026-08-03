<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class product extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productphotos(): HasMany
    {
        return $this->hasMany(ProductPoto::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function printAreas(): HasMany
    {
        return $this->hasMany(PrintArea::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class, 'product_template_id');
    }

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
        'category_id',
        'imagepath',
        'type',
        'is_designable',
        'print_cost_type',
        'product_template_id',
        'template_version',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'is_designable' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    protected $appends = ['design_areas'];

    /**
     * Derived from ProductType — is_designable is true when type is Custom.
     * The column remains temporarily for backward compatibility.
     */
    public function getIsDesignableAttribute(): bool
    {
        return $this->attributes['is_designable']
            || $this->type === ProductType::Custom;
    }

    public function getDesignAreasAttribute()
    {
        return $this->printAreas->map(function ($area) {
            return [
                'id' => $area->id,
                'name' => $area->name,
                'view_name' => $area->view_name,
                'view_index' => $area->view_index,
                'area_type' => $area->area_type,
                'slot_key' => $area->slot_key,
                'slot_type' => $area->slot_type,
                'x' => $area->x,
                'y' => $area->y,
                'width' => $area->width,
                'height' => $area->height,
            ];
        });
    }

    public function getEditorImageData()
    {
        $baseImages = [];
        $colorImages = [];
        $pathViewName = [];

        if ($this->productphotos) {
            foreach ($this->productphotos as $img) {
                $path = str_replace('\\', '/', $img->imagepath);
                if (! $path) {
                    continue;
                }

                if (! isset($pathViewName[$path])) {
                    $pathViewName[$path] = $img->view_name ?? '';
                }

                $normalizedColor = strtolower(trim((string) $img->color));

                if ($normalizedColor === '') {
                    if (! in_array($path, $baseImages)) {
                        $baseImages[] = $path;
                    }
                    continue;
                }

                if (! isset($colorImages[$normalizedColor])) {
                    $colorImages[$normalizedColor] = [];
                }

                if (! in_array($path, $colorImages[$normalizedColor])) {
                    $colorImages[$normalizedColor][] = $path;
                }
            }
        }

        if (empty($baseImages) && ! empty($colorImages)) {
            $firstColorImages = reset($colorImages);
            $baseImages = is_array($firstColorImages) ? $firstColorImages : [];
        }

        $viewPriority = ['front' => 0, 'back' => 1, 'left_sleeve' => 2, 'right_sleeve' => 3];
        usort($baseImages, function ($a, $b) use ($pathViewName, $viewPriority) {
            $pa = isset($pathViewName[$a]) ? ($viewPriority[$pathViewName[$a]] ?? 99) : 99;
            $pb = isset($pathViewName[$b]) ? ($viewPriority[$pathViewName[$b]] ?? 99) : 99;
            return $pa <=> $pb;
        });

        return ['base_images' => $baseImages, 'color_images' => $colorImages];
    }

    public function getEditorViewMapping()
    {
        $imageData = $this->getEditorImageData();
        $baseImages = $imageData['base_images'];
        $colorImages = $imageData['color_images'];

        $photoViewMap = [];
        foreach ($this->productphotos as $img) {
            $photoViewMap[str_replace('\\', '/', $img->imagepath)] = $img->view_name;
        }

        $viewNames = [];
        foreach ($baseImages as $imgPath) {
            $cleanPath = str_replace('\\', '/', $imgPath);
            $viewNames[] = $photoViewMap[$cleanPath] ?? 'front';
        }

        $colorViewNames = [];
        foreach ($colorImages as $colorKey => $images) {
            $colorViewNames[$colorKey] = [];
            foreach ($images as $imgPath) {
                $cleanPath = str_replace('\\', '/', $imgPath);
                $colorViewNames[$colorKey][] = $photoViewMap[$cleanPath] ?? 'front';
            }
        }

        return ['view_names' => $viewNames, 'color_view_names' => $colorViewNames];
    }

    public function getEditorAreasByView()
    {
        $areasByView = [];
        foreach ($this->design_areas as $area) {
            $vn = $area['view_name'];
            if (! isset($areasByView[$vn])) {
                $areasByView[$vn] = [];
            }
            $areasByView[$vn][] = $area;
        }

        return $areasByView;
    }

    public function getEditorVariantsData()
    {
        $variantsData = [];
        foreach ($this->variants as $variant) {
            if ($variant->quantity > 0) {
                $size = $variant->size;
                $color = $variant->color;
                if (! isset($variantsData[$size])) {
                    $variantsData[$size] = [];
                }
                $variantsData[$size][$color] = [
                    'id' => $variant->id,
                    'quantity' => $variant->quantity,
                    'weight' => $variant->weight,
                    'material' => $variant->material,
                    'color_code' => $variant->color_code ?? null,
                ];
            }
        }

        return $variantsData;
    }
}
