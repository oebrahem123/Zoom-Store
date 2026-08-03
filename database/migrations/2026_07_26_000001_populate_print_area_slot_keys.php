<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $printAreas = DB::table('print_areas')
            ->join('products', 'print_areas.product_id', '=', 'products.id')
            ->select(
                'print_areas.id as area_id',
                'print_areas.name as area_name',
                'print_areas.view_name as area_view_name',
                'print_areas.area_type',
                'products.product_template_id as template_id'
            )
            ->whereNull('print_areas.slot_key')
            ->whereNotNull('products.product_template_id')
            ->get();

        $slots = DB::table('product_template_slots')->get()->keyBy(function ($s) {
            return $s->template_id . '|' . $s->view_name . '|' . $s->name;
        });

        $fallback = DB::table('product_template_slots')->get()->keyBy(function ($s) {
            return $s->template_id . '|' . $s->view_name . '|' . $s->slot_type;
        });

        foreach ($printAreas as $area) {
            $key = $area->template_id . '|' . $area->area_view_name . '|' . $area->area_name;
            $slot = $slots->get($key);

            if (! $slot) {
                $fallbackKey = $area->template_id . '|' . $area->area_view_name . '|' . $this->areaTypeToSlotType($area->area_type);
                $slot = $fallback->get($fallbackKey);
            }

            if ($slot) {
                DB::table('print_areas')
                    ->where('id', $area->area_id)
                    ->update([
                        'slot_key' => $slot->slot_key,
                        'slot_type' => $slot->slot_type,
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('print_areas')
            ->whereNotNull('slot_key')
            ->update([
                'slot_key' => null,
                'slot_type' => null,
            ]);
    }

    private function areaTypeToSlotType(?string $areaType): ?string
    {
        return match ($areaType) {
            'main' => 'main',
            'logo' => 'logo',
            'sleeve' => 'secondary',
            default => $areaType,
        };
    }
};
