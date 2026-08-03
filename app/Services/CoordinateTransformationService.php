<?php

namespace App\Services;

class CoordinateTransformationService
{
    /*
    |--------------------------------------------------------------------------
    | Coordinate Transformation Engine
    |--------------------------------------------------------------------------
    |
    | Converts object positions from one product to another using slot_key
    | matching. Preserves all Fabric object properties except coordinates.
    |
    | Architecture Rules:
    |   - Never compare areas by array index, translated names, or labels
    |   - Only use slot_key for matching
    |   - Preserve ALL object properties (scale, rotation, opacity, metadata)
    |   - Legacy objects (no slot_key) use index-based fallback
    |   - Output is identical to input except for transformed coordinates
    |
    | Data Format:
    |   - Input: serialized Fabric object arrays (from canvas.toJSON())
    |   - Output: transformed arrays with updated left/top
    |   - No Fabric.js dependency — pure PHP math
    |
    | Reusability:
    |   - Product Switching
    |   - Batch transformations
    |   - Future APIs
    |   - Import / Export
    |
    */

    private SlotMatchingService $matchingService;

    public function __construct()
    {
        $this->matchingService = new SlotMatchingService();
    }

    /**
     * Transform a collection of serialized Fabric objects from source to target product.
     *
     * @param array $objects       Serialized Fabric objects (arrays from canvas.toJSON())
     * @param \App\Models\Product $source The source product
     * @param \App\Models\Product $target The target product
     * @param array $matchingResult Optional pre-computed result from SlotMatchingService::matchSlots()
     * @return array Transformed objects with updated coordinates
     */
    public function transformObjects(
        array $objects,
        $source,
        $target,
        ?array $matchingResult = null
    ): array {
        if ($matchingResult === null) {
            $matchingResult = $this->matchingService->matchSlots($source, $target);
        }

        $sourceAreas = $this->buildAreaMap($source);
        $targetAreas = $this->buildAreaMap($target);

        $commonSlots = $this->buildSlotLookup($matchingResult['common']);

        $transformed = [];
        $legacyIndex = 0;

        foreach ($objects as $obj) {
            if (!is_array($obj)) {
                continue;
            }
            if ($this->isExcluded($obj)) {
                $transformed[] = $obj;
                continue;
            }

            $slotKey = $obj['_slotKey'] ?? null;

            if ($slotKey !== null && isset($commonSlots[$slotKey])) {
                $sourceArea = $sourceAreas[$slotKey] ?? null;
                $targetArea = $targetAreas[$slotKey] ?? null;

                if ($sourceArea && $targetArea) {
                    $transformed[] = $this->repositionObject($obj, $sourceArea, $targetArea);
                    continue;
                }
            }

            $transformed[] = $this->legacyFallback($obj, $sourceAreas, $targetAreas, $legacyIndex);
            $legacyIndex++;
        }

        return $transformed;
    }

    /**
     * Transform a single object between two areas.
     * Public for downstream use (batch APIs, single object operations).
     *
     * @param array $object      Serialized Fabric object
     * @param array $sourceArea  Source print area data
     * @param array $targetArea  Target print area data
     * @return array Transformed object
     */
    public function transformSingleObject(array $object, array $sourceArea, array $targetArea): array
    {
        return $this->repositionObject($object, $sourceArea, $targetArea);
    }

    /**
     * Reposition a single object from source area to target area.
     * Preserves all properties except left/top.
     */
    private function repositionObject(array $obj, array $sourceArea, array $targetArea): array
    {
        $normalized = $this->normalizeToArea($obj, $sourceArea);
        $newPosition = $this->denormalizeFromArea($normalized, $targetArea, $obj);

        $result = $obj;
        $result['left'] = $newPosition['left'];
        $result['top'] = $newPosition['top'];

        return $result;
    }

    /**
     * Normalize an object's position to 0–1 relative coordinates within a source area.
     *
     * Fabric.js origin convention: originX/originY determines which point of the
     * object is anchored at left/top. Default is 'left'/'top' (top-left corner).
     * We compute the actual center regardless of origin, then normalize relative
     * to the area center.
     */
    private function normalizeToArea(array $obj, array $sourceArea): array
    {
        $left = (float) ($obj['left'] ?? 0);
        $top = (float) ($obj['top'] ?? 0);
        $originX = $obj['originX'] ?? 'left';
        $originY = $obj['originY'] ?? 'top';
        $scaledWidth = (float) ($obj['width'] ?? 0) * (float) ($obj['scaleX'] ?? 1);
        $scaledHeight = (float) ($obj['height'] ?? 0) * (float) ($obj['scaleY'] ?? 1);

        switch ($originX) {
            case 'center':
                $objCenterX = $left;
                break;
            case 'right':
                $objCenterX = $left - $scaledWidth / 2;
                break;
            default:
                $objCenterX = $left + $scaledWidth / 2;
                break;
        }

        switch ($originY) {
            case 'center':
                $objCenterY = $top;
                break;
            case 'bottom':
                $objCenterY = $top - $scaledHeight / 2;
                break;
            default:
                $objCenterY = $top + $scaledHeight / 2;
                break;
        }

        $areaCenterX = $sourceArea['x'] + $sourceArea['width'] / 2;
        $areaCenterY = $sourceArea['y'] + $sourceArea['height'] / 2;

        $relX = $objCenterX - $areaCenterX;
        $relY = $objCenterY - $areaCenterY;

        return [
            'normX' => $sourceArea['width'] > 0 ? $relX / $sourceArea['width'] : 0,
            'normY' => $sourceArea['height'] > 0 ? $relY / $sourceArea['height'] : 0,
        ];
    }

    /**
     * Convert normalized 0–1 coordinates back to absolute canvas coordinates
     * within a target area, accounting for the object's origin point.
     */
    private function denormalizeFromArea(array $normalized, array $targetArea, array $obj = []): array
    {
        $areaCenterX = $targetArea['x'] + $targetArea['width'] / 2;
        $areaCenterY = $targetArea['y'] + $targetArea['height'] / 2;

        $centerX = $areaCenterX + $normalized['normX'] * $targetArea['width'];
        $centerY = $areaCenterY + $normalized['normY'] * $targetArea['height'];

        if (empty($obj)) {
            return ['left' => $centerX, 'top' => $centerY];
        }

        $originX = $obj['originX'] ?? 'left';
        $originY = $obj['originY'] ?? 'top';
        $scaledWidth = (float) ($obj['width'] ?? 0) * (float) ($obj['scaleX'] ?? 1);
        $scaledHeight = (float) ($obj['height'] ?? 0) * (float) ($obj['scaleY'] ?? 1);

        switch ($originX) {
            case 'center':
                $left = $centerX;
                break;
            case 'right':
                $left = $centerX + $scaledWidth / 2;
                break;
            default:
                $left = $centerX - $scaledWidth / 2;
                break;
        }

        switch ($originY) {
            case 'center':
                $top = $centerY;
                break;
            case 'bottom':
                $top = $centerY + $scaledHeight / 2;
                break;
            default:
                $top = $centerY - $scaledHeight / 2;
                break;
        }

        return ['left' => $left, 'top' => $top];
    }

    /**
     * Legacy fallback for objects without slot_key.
     * Uses index-based matching as last resort.
     * Preserves all properties — only position changes.
     */
    private function legacyFallback(
        array $obj,
        array $sourceAreas,
        array $targetAreas,
        int $index
    ): array {
        $sourceKeys = array_keys($sourceAreas);
        $targetKeys = array_keys($targetAreas);

        if (! isset($sourceKeys[$index]) || ! isset($targetKeys[$index])) {
            return $obj;
        }

        $sourceKey = $sourceKeys[$index];
        $targetKey = $targetKeys[$index];

        if (! isset($sourceAreas[$sourceKey], $targetAreas[$targetKey])) {
            return $obj;
        }

        return $this->repositionObject(
            $obj,
            $sourceAreas[$sourceKey],
            $targetAreas[$targetKey]
        );
    }

    /**
     * Build a slot_key => area data map for a product.
     */
    private function buildAreaMap($product): array
    {
        $map = [];

        foreach ($product->printAreas as $area) {
            if (! empty($area->slot_key)) {
                $map[$area->slot_key] = [
                    'x' => (float) $area->x,
                    'y' => (float) $area->y,
                    'width' => (float) $area->width,
                    'height' => (float) $area->height,
                ];
            }
        }

        return $map;
    }

    /**
     * Build a lookup from common slots array for O(1) access by slot_key.
     */
    private function buildSlotLookup(array $commonSlots): array
    {
        $lookup = [];

        foreach ($commonSlots as $slot) {
            $lookup[$slot['slot_key']] = true;
        }

        return $lookup;
    }

    /**
     * Check if an object should be excluded from transformation.
     * Print zones and objects marked for export exclusion are passed through unchanged.
     */
    private function isExcluded(array $obj): bool
    {
        return ($obj['_isPrintZone'] ?? false) === true
            || ($obj['excludeFromExport'] ?? false) === true;
    }
}
