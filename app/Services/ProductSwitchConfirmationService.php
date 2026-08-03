<?php

namespace App\Services;

class ProductSwitchConfirmationService
{
    /*
    |--------------------------------------------------------------------------
    | Product Switching Confirmation Layer
    |--------------------------------------------------------------------------
    |
    | Pure presentation layer. Determines whether a confirmation dialog is
    | needed and what to display. Never touches canvas, UI, or business logic.
    |
    | Architecture Rules:
    |   - User must NEVER see internal slot_key identifiers
    |   - Always display human-friendly Display Name (from PrintArea name)
    |   - Business logic operates exclusively on slot_key
    |   - This service only prepares presentation data
    |
    | Future Ready:
    |   - Template changes
    |   - Batch switching
    |   - Import conflicts
    |   - Future print providers
    |
    */

    /**
     * Analyze a matching result and determine if confirmation is needed.
     *
     * @param array $matchingResult Output from SlotMatchingService::matchSlots()
     * @param array $objects        Serialized Fabric objects (from canvas.toJSON())
     * @return array Confirmation response with dialog data
     */
    public function analyzeSwitch(array $matchingResult, array $objects): array
    {
        $missingSlots = $matchingResult['missing'] ?? [];

        $missingSlotNames = array_map(
            fn ($slot) => ! empty($slot['source']['name']) ? $slot['source']['name'] : $slot['slot_key'],
            $missingSlots
        );

        $missingSlotKeys = array_column($missingSlots, 'slot_key');

        $affectedCount = 0;
        $unaffectedCount = 0;

        foreach ($objects as $obj) {
            if (!is_array($obj)) {
                continue;
            }
            if ($this->isExcluded($obj)) {
                $unaffectedCount++;
                continue;
            }

            $slotKey = $obj['_slotKey'] ?? null;

            if ($slotKey !== null && in_array($slotKey, $missingSlotKeys, true)) {
                $affectedCount++;
            } else {
                $unaffectedCount++;
            }
        }

        $needsConfirmation = count($missingSlots) > 0;

        return [
            'needs_confirmation' => $needsConfirmation,
            'missing_slots' => $missingSlots,
            'missing_slot_names' => $missingSlotNames,
            'affected_count' => $affectedCount,
            'unaffected_count' => $unaffectedCount,
            'total_objects' => count($objects),
        ];
    }

    /**
     * Build the user-facing message for the confirmation dialog.
     * Uses Display Names only — never exposes slot_key.
     */
    public function buildMessage(array $confirmationData): string
    {
        $names = $confirmationData['missing_slot_names'];

        if (empty($names)) {
            return '';
        }

        $affected = $confirmationData['affected_count'];
        $unaffected = $confirmationData['unaffected_count'];

        $namesList = implode('، ', $names);

        $message = "المنتج المحدد لا يدعم بعض المناطق المستخدمة في تصميمك الحالي.\n\n";
        $message .= "المناطق غير المدعومة: {$namesList}\n\n";

        if ($affected > 0) {
            $message .= "عدد العناصر المتأثرة: {$affected}\n";
        }

        if ($unaffected > 0) {
            $message .= "عدد العناصر غير المتأثرة: {$unaffected}\n";
        }

        $message .= "\nسيتم إزالة العناصر التابعة للمناطق غير المدعومة فقط. جميع العناصر الأخرى ستبقى كما هي.";

        return $message;
    }

    /**
     * Check if an object should be excluded from affected/unaffected counting.
     */
    private function isExcluded(array $obj): bool
    {
        return ($obj['_isPrintZone'] ?? false) === true
            || ($obj['excludeFromExport'] ?? false) === true;
    }
}
