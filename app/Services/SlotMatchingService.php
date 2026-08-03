<?php

namespace App\Services;

use App\Models\Product;

class SlotMatchingService
{
    /*
    |--------------------------------------------------------------------------
    | Slot Matching Engine
    |--------------------------------------------------------------------------
    |
    | Compares two products' print areas by slot_key only.
    | Returns structured matching results for product switching.
    |
    | Architecture Rules:
    |   - Never compare slots by array index
    |   - Never rely on translated names or labels
    |   - slot_key is the only matching identifier
    |   - view_name is display-only (never used for logic)
    |   - view_index is editor-internal (not used for matching)
    |
    | Future-Proofing:
    |   - Supports any number of slot_types
    |   - Supports template versioning (metadata included in result)
    |   - Supports future print providers (extensible slot data)
    |   - Results are data-only (no Fabric/UI dependencies)
    |
    */

    /**
     * Compare two products by slot_key and return a structured matching result.
     *
     * @param Product $source The product being switched FROM (has canvas objects)
     * @param Product $target The product being switched TO (new layout)
     */
    public function matchSlots(Product $source, Product $target): array
    {
        $sourceAreas = $this->getSlotKeyedAreas($source);
        $targetAreas = $this->getSlotKeyedAreas($target);

        $sourceKeys = $sourceAreas->keys()->toArray();
        $targetKeys = $targetAreas->keys()->toArray();

        $commonKeys = array_values(array_intersect($sourceKeys, $targetKeys));
        $missingKeys = array_values(array_diff($sourceKeys, $targetKeys));
        $newKeys = array_values(array_diff($targetKeys, $sourceKeys));

        return [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'source_template_version' => $source->template_version,
            'target_template_version' => $target->template_version,
            'common' => $this->buildCommonSlots($commonKeys, $sourceAreas, $targetAreas),
            'missing' => $this->buildMissingSlots($missingKeys, $sourceAreas),
            'new' => $this->buildNewSlots($newKeys, $targetAreas),
        ];
    }

    /**
     * Get print areas keyed by slot_key, filtering out null/empty keys.
     */
    private function getSlotKeyedAreas(Product $product): \Illuminate\Support\Collection
    {
        return $product->printAreas
            ->filter(fn ($area) => ! empty($area->slot_key))
            ->keyBy('slot_key');
    }

    /**
     * Build matching results for slots present in both products.
     * Objects in these slots can be repositioned to the target's coordinates.
     */
    private function buildCommonSlots(
        array $commonKeys,
        \Illuminate\Support\Collection $sourceAreas,
        \Illuminate\Support\Collection $targetAreas
    ): array {
        $slots = [];

        foreach ($commonKeys as $key) {
            $source = $sourceAreas[$key];
            $target = $targetAreas[$key];

            $slots[] = [
                'slot_key' => $key,
                'source' => $this->extractAreaData($source),
                'target' => $this->extractAreaData($target),
                'coordinates_changed' => $this->coordinatesChanged($source, $target),
            ];
        }

        return $slots;
    }

    /**
     * Build results for slots that exist in source but not in target.
     * Objects in these slots will lose their slot assignment after switching.
     */
    private function buildMissingSlots(
        array $missingKeys,
        \Illuminate\Support\Collection $sourceAreas
    ): array {
        $slots = [];

        foreach ($missingKeys as $key) {
            $slots[] = [
                'slot_key' => $key,
                'source' => $this->extractAreaData($sourceAreas[$key]),
            ];
        }

        return $slots;
    }

    /**
     * Build results for slots that exist in target but not in source.
     * These are new placement opportunities for the target product.
     */
    private function buildNewSlots(
        array $newKeys,
        \Illuminate\Support\Collection $targetAreas
    ): array {
        $slots = [];

        foreach ($newKeys as $key) {
            $slots[] = [
                'slot_key' => $key,
                'target' => $this->extractAreaData($targetAreas[$key]),
            ];
        }

        return $slots;
    }

    /**
     * Extract standardized slot data from a PrintArea record.
     * Includes all fields needed for repositioning and future extensibility.
     */
    private function extractAreaData($area): array
    {
        return [
            'id' => $area->id,
            'name' => $area->name,
            'slot_key' => $area->slot_key,
            'slot_type' => $area->slot_type,
            'view_name' => $area->view_name,
            'view_index' => $area->view_index,
            'x' => (float) $area->x,
            'y' => (float) $area->y,
            'width' => (float) $area->width,
            'height' => (float) $area->height,
        ];
    }

    /**
     * Check if coordinates differ between source and target areas.
     * Used downstream to determine if repositioning is needed.
     */
    private function coordinatesChanged($source, $target): bool
    {
        return (float) $source->x !== (float) $target->x
            || (float) $source->y !== (float) $target->y
            || (float) $source->width !== (float) $target->width
            || (float) $source->height !== (float) $target->height;
    }
}
