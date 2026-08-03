<?php

namespace Tests\Unit;

use App\Services\ProductSwitchConfirmationService;
use PHPUnit\Framework\TestCase;

class ProductSwitchConfirmationServiceTest extends TestCase
{
    private ProductSwitchConfirmationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductSwitchConfirmationService();
    }

    private function makeMatchingResult(array $overrides = []): array
    {
        return array_merge([
            'source_product_id' => 1,
            'target_product_id' => 2,
            'source_template_version' => 1,
            'target_template_version' => 1,
            'common' => [],
            'missing' => [],
            'new' => [],
        ], $overrides);
    }

    private function makeMissingSlot(string $slotKey, string $name): array
    {
        return [
            'slot_key' => $slotKey,
            'source' => [
                'id' => rand(1, 100),
                'name' => $name,
                'slot_key' => $slotKey,
                'slot_type' => 'main',
                'view_name' => 'front',
                'view_index' => 0,
                'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200,
            ],
        ];
    }

    private function makeObject(array $overrides = []): array
    {
        return array_merge([
            'type' => 'i-text',
            'left' => 100.0,
            'top' => 100.0,
            'width' => 80,
            'height' => 20,
            'scaleX' => 1.0,
            'scaleY' => 1.0,
            'angle' => 0,
            'opacity' => 1.0,
            '_slotKey' => 'front_main',
        ], $overrides);
    }

    // ─────────────────────────────────────────────
    // Scenario 1: No missing slots
    // ─────────────────────────────────────────────

    public function test_no_missing_slots_returns_no_confirmation(): void
    {
        $matching = $this->makeMatchingResult([
            'common' => [
                ['slot_key' => 'front_main', 'source' => [], 'target' => [], 'coordinates_changed' => false],
            ],
            'missing' => [],
        ]);

        $objects = [$this->makeObject()];
        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertFalse($result['needs_confirmation']);
        $this->assertCount(0, $result['missing_slot_names']);
        $this->assertEquals(1, $result['unaffected_count']);
        $this->assertEquals(0, $result['affected_count']);
    }

    // ─────────────────────────────────────────────
    // Scenario 2: One missing slot
    // ─────────────────────────────────────────────

    public function test_one_missing_slot_requires_confirmation(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('collar_logo', 'لوجو الصدر الأيسر'),
            ],
        ]);

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'collar_logo']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertTrue($result['needs_confirmation']);
        $this->assertCount(1, $result['missing_slot_names']);
        $this->assertEquals('لوجو الصدر الأيسر', $result['missing_slot_names'][0]);
        $this->assertEquals(1, $result['affected_count']);
        $this->assertEquals(1, $result['unaffected_count']);
    }

    // ─────────────────────────────────────────────
    // Scenario 3: Multiple missing slots
    // ─────────────────────────────────────────────

    public function test_multiple_missing_slots(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('collar_logo', 'لوجو الصدر الأيسر'),
                $this->makeMissingSlot('left_sleeve', 'الكم الأيسر'),
                $this->makeMissingSlot('right_sleeve', 'الكم الأيمن'),
            ],
        ]);

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'collar_logo']),
            $this->makeObject(['_slotKey' => 'left_sleeve']),
            $this->makeObject(['_slotKey' => 'right_sleeve']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertTrue($result['needs_confirmation']);
        $this->assertCount(3, $result['missing_slot_names']);
        $this->assertEquals('لوجو الصدر الأيسر', $result['missing_slot_names'][0]);
        $this->assertEquals('الكم الأيسر', $result['missing_slot_names'][1]);
        $this->assertEquals('الكم الأيمن', $result['missing_slot_names'][2]);
        $this->assertEquals(3, $result['affected_count']);
        $this->assertEquals(1, $result['unaffected_count']);
        $this->assertEquals(4, $result['total_objects']);
    }

    // ─────────────────────────────────────────────
    // Scenario 4: Zero affected objects
    // ─────────────────────────────────────────────

    public function test_zero_affected_objects(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('collar_logo', 'لوجو الصدر'),
            ],
        ]);

        // Objects only in front_main (not in missing collar_logo)
        $objects = [
            $this->makeObject(['_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'front_main']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertTrue($result['needs_confirmation']);
        $this->assertEquals(0, $result['affected_count']);
        $this->assertEquals(2, $result['unaffected_count']);
    }

    // ─────────────────────────────────────────────
    // Scenario 5: Many affected objects
    // ─────────────────────────────────────────────

    public function test_many_affected_objects(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('left_sleeve', 'الكم الأيسر'),
            ],
        ]);

        $objects = [];
        for ($i = 0; $i < 20; $i++) {
            $objects[] = $this->makeObject(['_slotKey' => 'left_sleeve']);
        }
        $objects[] = $this->makeObject(['_slotKey' => 'front_main']);

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertEquals(20, $result['affected_count']);
        $this->assertEquals(1, $result['unaffected_count']);
        $this->assertEquals(21, $result['total_objects']);
    }

    // ─────────────────────────────────────────────
    // Scenario 6: Message generation
    // ─────────────────────────────────────────────

    public function test_message_contains_display_names_not_slot_keys(): void
    {
        $data = [
            'needs_confirmation' => true,
            'missing_slot_names' => ['لوجو الصدر', 'الكم الأيسر'],
            'affected_count' => 5,
            'unaffected_count' => 3,
            'total_objects' => 8,
        ];

        $message = $this->service->buildMessage($data);

        $this->assertStringContainsString('لوجو الصدر', $message);
        $this->assertStringContainsString('الكم الأيسر', $message);
        $this->assertStringContainsString('5', $message);
        $this->assertStringContainsString('3', $message);
        $this->assertStringContainsString('ستبقى كما هي', $message);

        // Must NOT contain internal slot_key
        $this->assertStringNotContainsString('front_main', $message);
        $this->assertStringNotContainsString('collar_logo', $message);
        $this->assertStringNotContainsString('left_sleeve', $message);
    }

    public function test_message_empty_when_no_missing_slots(): void
    {
        $data = [
            'needs_confirmation' => false,
            'missing_slot_names' => [],
            'affected_count' => 0,
            'unaffected_count' => 5,
            'total_objects' => 5,
        ];

        $message = $this->service->buildMessage($data);

        $this->assertEmpty($message);
    }

    // ─────────────────────────────────────────────
    // Scenario 7: Legacy objects (no slot_key)
    // ─────────────────────────────────────────────

    public function test_legacy_objects_counted_as_unaffected(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('collar_logo', 'لوجو'),
            ],
        ]);

        // Legacy object without _slotKey
        $objects = [
            $this->makeObject(['_slotKey' => null]),
            $this->makeObject(['_slotKey' => null]),
            $this->makeObject(['_slotKey' => 'front_main']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertTrue($result['needs_confirmation']);
        $this->assertEquals(0, $result['affected_count']);
        $this->assertEquals(3, $result['unaffected_count']);
    }

    // ─────────────────────────────────────────────
    // Scenario 8: Print zones excluded
    // ─────────────────────────────────────────────

    public function test_print_zones_excluded_from_counting(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('front_main', 'الواجهة'),
            ],
        ]);

        $objects = [
            $this->makeObject(['_isPrintZone' => true, '_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'front_main']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        // Print zone is excluded from counting → 1 affected, 1 unaffected
        $this->assertEquals(1, $result['affected_count']);
        $this->assertEquals(1, $result['unaffected_count']);
    }

    public function test_exclude_from_export_objects_excluded(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('front_main', 'الواجهة'),
            ],
        ]);

        $objects = [
            $this->makeObject(['excludeFromExport' => true, '_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'front_main']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertEquals(1, $result['affected_count']);
        $this->assertEquals(1, $result['unaffected_count']);
    }

    // ─────────────────────────────────────────────
    // Scenario 9: Empty objects array
    // ─────────────────────────────────────────────

    public function test_empty_objects_array(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('front_main', 'الواجهة'),
            ],
        ]);

        $result = $this->service->analyzeSwitch($matching, []);

        $this->assertTrue($result['needs_confirmation']);
        $this->assertEquals(0, $result['affected_count']);
        $this->assertEquals(0, $result['unaffected_count']);
        $this->assertEquals(0, $result['total_objects']);
    }

    // ─────────────────────────────────────────────
    // Scenario 10: Display names never expose slot_key
    // ─────────────────────────────────────────────

    public function test_display_names_never_expose_slot_key(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('front_main', 'الواجهة الأمامية'),
                $this->makeMissingSlot('back_main', 'الخلفية'),
            ],
        ]);

        $objects = [$this->makeObject()];
        $result = $this->service->analyzeSwitch($matching, $objects);

        // display names contain Arabic labels
        $this->assertContains('الواجهة الأمامية', $result['missing_slot_names']);
        $this->assertContains('الخلفية', $result['missing_slot_names']);

        // slot_keys must NOT appear in display names
        foreach ($result['missing_slot_names'] as $name) {
            $this->assertStringNotContainsString('front_main', $name);
            $this->assertStringNotContainsString('back_main', $name);
        }
    }

    // ─────────────────────────────────────────────
    // Scenario 11: Mixed affected and unaffected
    // ─────────────────────────────────────────────

    public function test_mixed_affected_and_unaffected(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                $this->makeMissingSlot('collar_logo', 'لوجو الصدر'),
                $this->makeMissingSlot('left_sleeve', 'الكم الأيسر'),
            ],
        ]);

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'front_main']),
            $this->makeObject(['_slotKey' => 'back_main']),
            $this->makeObject(['_slotKey' => 'collar_logo']),
            $this->makeObject(['_slotKey' => 'left_sleeve']),
            $this->makeObject(['_slotKey' => 'left_sleeve']),
        ];

        $result = $this->service->analyzeSwitch($matching, $objects);

        $this->assertEquals(3, $result['affected_count']);
        $this->assertEquals(3, $result['unaffected_count']);
        $this->assertEquals(6, $result['total_objects']);
    }

    // ─────────────────────────────────────────────
    // Scenario 12: Fallback name when name missing
    // ─────────────────────────────────────────────

    public function test_fallback_to_slot_key_when_name_missing(): void
    {
        $matching = $this->makeMatchingResult([
            'missing' => [
                [
                    'slot_key' => 'unknown_slot',
                    'source' => [
                        'id' => 1,
                        'name' => '',
                        'slot_key' => 'unknown_slot',
                    ],
                ],
            ],
        ]);

        $objects = [$this->makeObject()];
        $result = $this->service->analyzeSwitch($matching, $objects);

        // When name is empty, falls back to slot_key for display
        $this->assertContains('unknown_slot', $result['missing_slot_names']);
    }
}
