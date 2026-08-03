<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PrintArea;
use App\Models\ProductTemplate;
use App\Models\ProductTemplateSlot;
use Illuminate\Support\Collection;

class ProductTemplateService
{
    /*
    |--------------------------------------------------------------------------
    | Architecture Conventions
    |--------------------------------------------------------------------------
    |
    | 1. slot_key is mandatory for every Fabric object. Objects belong to exactly
    |    one slot_key. This is a permanent architecture contract.
    |
    | 2. Templates are versioned. A product records the template_version at
    |    creation time. Future template versions never break older products.
    |
    | 3. Templates referenced by products are never physically deleted.
    |    Use deactivateTemplate() which sets is_active = false.
    |
    | 4. slot_key values are permanent, stable, English identifiers.
    |    They never depend on translated names or UI labels.
    |
    */

    public function getActiveTemplates(): Collection
    {
        return ProductTemplate::where('is_active', true)
            ->orderBy('display_order')
            ->with('slots')
            ->get();
    }

    public function getTemplateByKey(string $key): ?ProductTemplate
    {
        return ProductTemplate::with('slots')->where('key', $key)->first();
    }

    public function getTemplateChoices(): array
    {
        return $this->getActiveTemplates()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Create PrintArea records from a template for a product.
     * Records the template_version on the product for future reference.
     */
    public function createPrintAreasFromTemplate(
        Product $product,
        string $templateKey,
        bool $includeOptional = false
    ): Collection {
        $template = $this->getTemplateByKey($templateKey);
        if (! $template) {
            return collect();
        }

        $query = $template->slots()->orderBy('display_order');
        if (! $includeOptional) {
            $query->where('is_required', true);
        }

        $slots = $query->get();
        $areas = collect();

        foreach ($slots as $slot) {
            $areas->push($this->createPrintAreaFromSlot($product, $slot));
        }

        $product->update(['template_version' => $template->version]);

        return $areas;
    }

    /**
     * Enable or disable an optional slot for a product.
     */
    public function toggleOptionalSlot(
        Product $product,
        string $templateKey,
        string $slotKey,
        bool $enable
    ): ?PrintArea {
        $template = $this->getTemplateByKey($templateKey);
        if (! $template) {
            return null;
        }

        $slotDef = $template->slots()->where('slot_key', $slotKey)->first();
        if (! $slotDef) {
            return null;
        }

        if ($enable) {
            $existing = PrintArea::where('product_id', $product->id)
                ->where('slot_key', $slotKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            return $this->createPrintAreaFromSlot($product, $slotDef);
        }

        PrintArea::where('product_id', $product->id)
            ->where('slot_key', $slotKey)
            ->delete();

        return null;
    }

    /**
     * Find a specific slot on a product by slot_key.
     */
    public function getSlotByKey(Product $product, string $slotKey): ?PrintArea
    {
        return PrintArea::where('product_id', $product->id)
            ->where('slot_key', $slotKey)
            ->first();
    }

    /**
     * Get a slot definition from a template by slot_key.
     */
    public function getSlotDefinitionByKey(ProductTemplate $template, string $slotKey): ?ProductTemplateSlot
    {
        return $template->slots()->where('slot_key', $slotKey)->first();
    }

    /**
     * Compare slot_keys between two products.
     * Returns missing, common, and new slot keys.
     */
    public function compareSlots(Product $source, Product $destination): array
    {
        $sourceKeys = $source->printAreas
            ->pluck('slot_key')
            ->filter()
            ->values()
            ->toArray();

        $destKeys = $destination->printAreas
            ->pluck('slot_key')
            ->filter()
            ->values()
            ->toArray();

        $missing = array_values(array_diff($sourceKeys, $destKeys));
        $common = array_values(array_intersect($sourceKeys, $destKeys));
        $new = array_values(array_diff($destKeys, $sourceKeys));

        return [
            'missing' => $missing,
            'common' => $common,
            'new' => $new,
            'can_switch' => empty($missing),
        ];
    }

    public function deactivateTemplate(ProductTemplate $template): bool
    {
        return $template->update(['is_active' => false]);
    }

    public function canDeleteTemplate(ProductTemplate $template): bool
    {
        return ! $template->isReferencedByProducts();
    }

    public function seedFromConfig(): int
    {
        $config = config('product_templates.templates', []);
        $created = 0;

        foreach ($config as $key => $data) {
            $template = ProductTemplate::firstOrCreate(
                ['key' => $key],
                [
                    'name' => $data['name'],
                    'version' => $data['version'] ?? 1,
                    'description' => $data['description'] ?? null,
                    'display_order' => $data['display_order'] ?? 0,
                    'is_active' => $data['is_active'] ?? true,
                ]
            );

            if (! $template->wasRecentlyCreated) {
                continue;
            }

            $created++;

            if (empty($data['slots'])) {
                continue;
            }

            foreach ($data['slots'] as $slotData) {
                ProductTemplateSlot::create([
                    'template_id' => $template->id,
                    'slot_key' => $slotData['slot_key'],
                    'slot_type' => $slotData['slot_type'],
                    'name' => $slotData['name'],
                    'view_name' => $slotData['view_name'],
                    'view_index' => $slotData['view_index'] ?? 0,
                    'is_required' => $slotData['is_required'] ?? true,
                    'display_order' => $slotData['display_order'] ?? 0,
                    'default_x' => $slotData['default_x'] ?? 0,
                    'default_y' => $slotData['default_y'] ?? 0,
                    'default_width' => $slotData['default_width'] ?? 100,
                    'default_height' => $slotData['default_height'] ?? 100,
                ]);
            }
        }

        return $created;
    }

    /**
     * Build and persist a PrintArea record from a template slot definition.
     * Single source of truth for slot → PrintArea data mapping.
     */
    private function createPrintAreaFromSlot(Product $product, ProductTemplateSlot $slot): PrintArea
    {
        return PrintArea::create([
            'product_id' => $product->id,
            'slot_key' => $slot->slot_key,
            'slot_type' => $slot->slot_type,
            'name' => $slot->name,
            'view_name' => $slot->view_name,
            'view_index' => $slot->view_index,
            'area_type' => $slot->slot_type,
            'x' => $slot->default_x,
            'y' => $slot->default_y,
            'width' => $slot->default_width,
            'height' => $slot->default_height,
        ]);
    }
}
