<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\PrintArea;
use App\Services\CoordinateTransformationService;
use App\Services\SlotMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinateTransformationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CoordinateTransformationService $service;
    private SlotMatchingService $matchingService;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CoordinateTransformationService();
        $this->matchingService = new SlotMatchingService();
        $this->category = Category::create(['name' => 'Test Category']);
    }

    private function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Test Product',
            'price' => 100.00,
            'quantity' => 10,
            'description' => 'Test',
            'imagepath' => 'uploads/test.jpg',
            'category_id' => $this->category->id,
            'type' => 'custom',
            'is_designable' => true,
        ], $overrides));
    }

    private function createArea(Product $product, array $overrides = []): PrintArea
    {
        return PrintArea::create(array_merge([
            'product_id' => $product->id,
            'view_name' => 'front',
            'view_index' => 0,
            'name' => 'Main Area',
            'area_type' => 'main',
            'slot_key' => 'front_main',
            'slot_type' => 'main',
            'x' => 50,
            'y' => 50,
            'width' => 200,
            'height' => 200,
        ], $overrides));
    }

    private function makeObject(array $overrides = []): array
    {
        return array_merge([
            'type' => 'i-text',
            'left' => 150.0,
            'top' => 150.0,
            'width' => 100,
            'height' => 30,
            'scaleX' => 1.0,
            'scaleY' => 1.0,
            'angle' => 0,
            'opacity' => 1.0,
            'originX' => 'center',
            'originY' => 'center',
            'fill' => '#000000',
            'text' => 'Hello',
            '_slotKey' => 'front_main',
        ], $overrides);
    }

    private function matchAndTransform(Product $source, Product $target, array $objects): array
    {
        $matching = $this->matchingService->matchSlots($source, $target);

        return $this->service->transformObjects($objects, $source, $target, $matching);
    }

    // ─────────────────────────────────────────────
    // Scenario 1: Identical print areas (no change)
    // ─────────────────────────────────────────────

    public function test_identical_areas_preserves_position(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);

        $objects = [$this->makeObject(['left' => 150.0, 'top' => 150.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(1, $result);
        $this->assertEqualsWithDelta(150.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(150.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 2: Resized print areas (proportional)
    // ─────────────────────────────────────────────

    public function test_resized_areas_repositions_proportionally(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        // Source: 200x200 area at (100,100), target: 400x400 area at (0,0)
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // Source area center is at (200,200). Object at (150,150) is offset (-50,-50) from center.
        // Normalized: (-50/200, -50/200) = (-0.25, -0.25)
        // Target center is at (200,200). New position: (200 + -0.25*400, 200 + -0.25*400) = (100, 100)
        $objects = [$this->makeObject(['left' => 150.0, 'top' => 150.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(100.0, $result[0]['top'], 0.01);
    }

    public function test_resized_areas_preserves_visual_dimensions(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject(['width' => 100, 'height' => 30, 'scaleX' => 1.5, 'scaleY' => 1.5])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals(100, $result[0]['width']);
        $this->assertEquals(30, $result[0]['height']);
        $this->assertEquals(1.5, $result[0]['scaleX']);
        $this->assertEquals(1.5, $result[0]['scaleY']);
    }

    // ─────────────────────────────────────────────
    // Scenario 3: Moved print areas (offset only)
    // ─────────────────────────────────────────────

    public function test_moved_areas_shifts_position(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        // Same size, different position
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 200, 'y' => 300, 'width' => 200, 'height' => 200]);

        // Object at center of source (200, 200) → should be at center of target (300, 400)
        $objects = [$this->makeObject(['left' => 200.0, 'top' => 200.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEqualsWithDelta(300.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(400.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 4: Different aspect ratios
    // ─────────────────────────────────────────────

    public function test_different_aspect_ratios(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        // Source: square 200x200, Target: wide 400x100
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 100]);

        // Object at (100, 100) = center of source → should map to center of target (200, 50)
        $objects = [$this->makeObject(['left' => 100.0, 'top' => 100.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEqualsWithDelta(200.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(50.0, $result[0]['top'], 0.01);
    }

    public function test_offset_position_in_different_aspect_ratio(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        // Source: 200x200, Target: 400x100
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 100]);

        // Object at (150, 125): relative to source center = (50, 25) = normalized (0.25, 0.125)
        // Target center = (200, 50) + (0.25*400, 0.125*100) = (300, 62.5)
        $objects = [$this->makeObject(['left' => 150.0, 'top' => 125.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEqualsWithDelta(300.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(62.5, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 5: Rotated objects
    // ─────────────────────────────────────────────

    public function test_rotated_object_preserves_angle(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);

        $objects = [$this->makeObject(['angle' => 45.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals(45.0, $result[0]['angle']);
    }

    // ─────────────────────────────────────────────
    // Scenario 6: Scaled objects
    // ─────────────────────────────────────────────

    public function test_scaled_object_preserves_scale(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject(['scaleX' => 2.5, 'scaleY' => 1.8])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals(2.5, $result[0]['scaleX']);
        $this->assertEquals(1.8, $result[0]['scaleY']);
    }

    // ─────────────────────────────────────────────
    // Scenario 7: Text objects
    // ─────────────────────────────────────────────

    public function test_text_object_preserves_content_and_style(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 300, 'height' => 300]);

        $objects = [$this->makeObject([
            'type' => 'i-text',
            'text' => 'Custom Design',
            'fontSize' => 24,
            'fontFamily' => 'Arial',
            'fill' => '#FF0000',
            'fontWeight' => 'bold',
            'fontStyle' => 'italic',
            'textDecoration' => 'underline',
            'left' => 100.0,
            'top' => 100.0,
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals('Custom Design', $result[0]['text']);
        $this->assertEquals(24, $result[0]['fontSize']);
        $this->assertEquals('Arial', $result[0]['fontFamily']);
        $this->assertEquals('#FF0000', $result[0]['fill']);
        $this->assertEquals('bold', $result[0]['fontWeight']);
        $this->assertEquals('italic', $result[0]['fontStyle']);
        $this->assertEquals('underline', $result[0]['textDecoration']);
    }

    // ─────────────────────────────────────────────
    // Scenario 8: SVG / asset objects
    // ─────────────────────────────────────────────

    public function test_svg_asset_preserves_metadata(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);

        $objects = [$this->makeObject([
            'type' => 'path',
            '_isArt' => true,
            '_artKey' => 'design-001',
            '_artColor' => '#333333',
            '_embossLevel' => 0.5,
            '_assetMeta' => ['provider' => 'internal', 'category' => 'shapes'],
            '_slotKey' => 'front_main',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertTrue($result[0]['_isArt']);
        $this->assertEquals('design-001', $result[0]['_artKey']);
        $this->assertEquals('#333333', $result[0]['_artColor']);
        $this->assertEquals(0.5, $result[0]['_embossLevel']);
        $this->assertEquals('internal', $result[0]['_assetMeta']['provider']);
        $this->assertEquals('shapes', $result[0]['_assetMeta']['category']);
    }

    // ─────────────────────────────────────────────
    // Scenario 9: Uploaded images
    // ─────────────────────────────────────────────

    public function test_uploaded_image_preserves_source(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject([
            'type' => 'image',
            '_customSrc' => 'uploads/user-designs/abc123.png',
            'src' => 'uploads/user-designs/abc123.png',
            'width' => 300,
            'height' => 200,
            'scaleX' => 0.8,
            'scaleY' => 0.8,
            'left' => 100.0,
            'top' => 100.0,
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals('uploads/user-designs/abc123.png', $result[0]['_customSrc']);
        $this->assertEquals('uploads/user-designs/abc123.png', $result[0]['src']);
        $this->assertEquals(300, $result[0]['width']);
        $this->assertEquals(200, $result[0]['height']);
        $this->assertEquals(0.8, $result[0]['scaleX']);
        $this->assertEquals(0.8, $result[0]['scaleY']);
    }

    // ─────────────────────────────────────────────
    // Scenario 10: Multiple objects in same slot
    // ─────────────────────────────────────────────

    public function test_multiple_objects_in_same_slot_all_repositioned(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 400, 'height' => 400]);

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 50.0, 'top' => 50.0, 'text' => 'Line 1']),
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0, 'text' => 'Line 2']),
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 150.0, 'top' => 150.0, 'text' => 'Line 3']),
        ];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(3, $result);

        // Object 1: relative (-0.25, -0.25) → target center (300,300) + (-0.25*400, -0.25*400) = (200, 200)
        $this->assertEqualsWithDelta(200.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(200.0, $result[0]['top'], 0.01);

        // Object 2: at center → maps to center of target (300, 300)
        $this->assertEqualsWithDelta(300.0, $result[1]['left'], 0.01);
        $this->assertEqualsWithDelta(300.0, $result[1]['top'], 0.01);

        // Object 3: relative (0.25, 0.25) → (400, 400)
        $this->assertEqualsWithDelta(400.0, $result[2]['left'], 0.01);
        $this->assertEqualsWithDelta(400.0, $result[2]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 11: Missing slot fallback
    // ─────────────────────────────────────────────

    public function test_object_in_missing_slot_unchanged(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($source, ['slot_key' => 'collar_logo', 'x' => 0, 'y' => 0, 'width' => 100, 'height' => 50]);

        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        // collar_logo does NOT exist in target

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
            $this->makeObject(['_slotKey' => 'collar_logo', 'left' => 50.0, 'top' => 25.0]),
        ];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(2, $result);
        // front_main: same area, same position
        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(100.0, $result[0]['top'], 0.01);
        // collar_logo: missing in target, unchanged
        $this->assertEqualsWithDelta(50.0, $result[1]['left'], 0.01);
        $this->assertEqualsWithDelta(25.0, $result[1]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 12: Legacy objects (no slot_key)
    // ─────────────────────────────────────────────

    public function test_legacy_objects_use_index_fallback(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // Legacy object without _slotKey
        $objects = [[
            'type' => 'i-text',
            'left' => 100.0,
            'top' => 100.0,
            'width' => 80,
            'height' => 20,
            'scaleX' => 1.0,
            'scaleY' => 1.0,
            'angle' => 0,
            'opacity' => 1.0,
            'text' => 'Legacy',
        ]];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(1, $result);
        // Index 0 matches front_main → repositioned to target
        // Object center = (100+40, 100+10) = (140,110) with origin left/top
        // Source area center = (100,100). Rel = (40,10). Norm = (0.2, 0.05)
        // Target center (200,200) + (0.2*400, 0.05*400) = (280, 220)
        // With origin left/top: left = 280-40 = 240, top = 220-10 = 210
        $this->assertEqualsWithDelta(240.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(210.0, $result[0]['top'], 0.01);
        $this->assertEquals('Legacy', $result[0]['text']);
    }

    public function test_legacy_objects_fewer_than_areas(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($source, ['slot_key' => 'back_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'back_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);

        // Only 1 legacy object for 2 areas
        $objects = [[
            'type' => 'i-text',
            'left' => 100.0,
            'top' => 100.0,
            'width' => 80,
            'height' => 20,
            'scaleX' => 1.0,
            'scaleY' => 1.0,
            'angle' => 0,
            'opacity' => 1.0,
        ]];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(1, $result);
        // Index 0 matches front_main → repositioned
        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(100.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 13: Print zones excluded
    // ─────────────────────────────────────────────

    public function test_print_zones_are_unchanged(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 400, 'height' => 400]);

        $objects = [
            $this->makeObject(['_isPrintZone' => true, 'left' => 100.0, 'top' => 100.0]),
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
        ];

        $result = $this->matchAndTransform($source, $target, $objects);

        // Print zone unchanged
        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(100.0, $result[0]['top'], 0.01);
        // Regular object repositioned
        $this->assertEqualsWithDelta(300.0, $result[1]['left'], 0.01);
        $this->assertEqualsWithDelta(300.0, $result[1]['top'], 0.01);
    }

    public function test_exclude_from_export_objects_unchanged(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject(['excludeFromExport' => true, 'left' => 100.0, 'top' => 100.0])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(100.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 14: All properties preserved
    // ─────────────────────────────────────────────

    public function test_all_custom_properties_preserved(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject([
            'opacity' => 0.7,
            'angle' => 30,
            'scaleX' => 1.5,
            'scaleY' => 2.0,
            'skewX' => 5,
            'skewY' => -3,
            'flipX' => true,
            'flipY' => false,
            'stroke' => '#FF0000',
            'strokeWidth' => 2,
            'shadow' => ['color' => '#000', 'blur' => 10],
            '_customProp' => 'preserved',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals(0.7, $result[0]['opacity']);
        $this->assertEquals(30, $result[0]['angle']);
        $this->assertEquals(1.5, $result[0]['scaleX']);
        $this->assertEquals(2.0, $result[0]['scaleY']);
        $this->assertEquals(5, $result[0]['skewX']);
        $this->assertEquals(-3, $result[0]['skewY']);
        $this->assertTrue($result[0]['flipX']);
        $this->assertFalse($result[0]['flipY']);
        $this->assertEquals('#FF0000', $result[0]['stroke']);
        $this->assertEquals(2, $result[0]['strokeWidth']);
        $this->assertEquals('#000', $result[0]['shadow']['color']);
        $this->assertEquals(10, $result[0]['shadow']['blur']);
        $this->assertEquals('preserved', $result[0]['_customProp']);
    }

    // ─────────────────────────────────────────────
    // Scenario 15: Empty objects array
    // ─────────────────────────────────────────────

    public function test_empty_objects_returns_empty(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $result = $this->matchAndTransform($source, $target, []);

        $this->assertCount(0, $result);
    }

    // ─────────────────────────────────────────────
    // Scenario 16: Pre-computed matching result
    // ─────────────────────────────────────────────

    public function test_accepts_pre_computed_matching_result(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $matching = $this->matchingService->matchSlots($source, $target);

        $objects = [$this->makeObject(['left' => 100.0, 'top' => 100.0])];
        $result = $this->service->transformObjects($objects, $source, $target, $matching);

        $this->assertEqualsWithDelta(200.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(200.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 17: Mixed object types in same slot
    // ─────────────────────────────────────────────

    public function test_mixed_types_in_same_slot(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [
            $this->makeObject(['type' => 'i-text', '_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
            $this->makeObject(['type' => 'path', '_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
            $this->makeObject(['type' => 'image', '_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
        ];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(3, $result);
        // All at center → all map to target center (200, 200)
        foreach ($result as $obj) {
            $this->assertEqualsWithDelta(200.0, $obj['left'], 0.01);
            $this->assertEqualsWithDelta(200.0, $obj['top'], 0.01);
        }
    }

    // ─────────────────────────────────────────────
    // Scenario 18: Objects across multiple slots
    // ─────────────────────────────────────────────

    public function test_objects_across_multiple_slots(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($source, ['slot_key' => 'back_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 300]);

        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 100, 'y' => 50, 'width' => 400, 'height' => 400]);
        $this->createArea($target, ['slot_key' => 'back_main', 'x' => 50, 'y' => 100, 'width' => 400, 'height' => 600]);

        $objects = [
            $this->makeObject(['_slotKey' => 'front_main', 'left' => 100.0, 'top' => 100.0]),
            $this->makeObject(['_slotKey' => 'back_main', 'left' => 100.0, 'top' => 150.0]),
        ];

        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertCount(2, $result);

        // front_main: source center (100,100) relative to (0,0,200,200) = (0,0)
        // → target center (100+200, 50+200) = (300, 250)
        $this->assertEqualsWithDelta(300.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(250.0, $result[0]['top'], 0.01);

        // back_main: source center (100,150) relative to (0,0,200,300) = (0, 0)
        // → target center (50+200, 100+300) = (250, 400)
        $this->assertEqualsWithDelta(250.0, $result[1]['left'], 0.01);
        $this->assertEqualsWithDelta(400.0, $result[1]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 19: transformSingleObject direct
    // ─────────────────────────────────────────────

    public function test_transform_single_object_directly(): void
    {
        $sourceArea = ['x' => 0, 'y' => 0, 'width' => 200, 'height' => 200];
        $targetArea = ['x' => 100, 'y' => 100, 'width' => 400, 'height' => 400];

        $obj = $this->makeObject(['left' => 100.0, 'top' => 100.0]);
        $result = $this->service->transformSingleObject($obj, $sourceArea, $targetArea);

        // Center of source (100,100) → center of target (100+200, 100+200) = (300, 300)
        $this->assertEqualsWithDelta(300.0, $result['left'], 0.01);
        $this->assertEqualsWithDelta(300.0, $result['top'], 0.01);
        $this->assertEquals('i-text', $result['type']);
    }

    // ─────────────────────────────────────────────
    // Scenario 20: All 9 origin combinations
    // ─────────────────────────────────────────────

    /**
     * For each originX/originY combination, place an object so its CENTER
     * is at (200, 200) = source area center. Transform to a different
     * area; the center must map to the new area center, and the left/top
     * must be adjusted back by the origin offset.
     */
    public function test_origin_left_top(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin left/top: left = centerX - scaledWidth/2, top = centerY - scaledHeight/2
        // Object center at (100, 100) = source area center
        // left = 100 - 50 = 50, top = 100 - 15 = 85
        $objects = [$this->makeObject([
            'left' => 50.0, 'top' => 85.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'left', 'originY' => 'top',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target area center = (200, 200). Object center maps there.
        // left = 200 - 50 = 150, top = 200 - 15 = 185
        $this->assertEqualsWithDelta(150.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(185.0, $result[0]['top'], 0.01);
    }

    public function test_origin_right_top(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin right/top: left = centerX + scaledWidth/2
        // Object center at (100, 100)
        // left = 100 + 50 = 150, top = 100 - 15 = 85
        $objects = [$this->makeObject([
            'left' => 150.0, 'top' => 85.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'right', 'originY' => 'top',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200 + 50 = 250, top = 200 - 15 = 185
        $this->assertEqualsWithDelta(250.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(185.0, $result[0]['top'], 0.01);
    }

    public function test_origin_center_bottom(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin center/bottom: left = centerX, top = centerY + scaledHeight/2
        // Object center at (100, 100)
        // left = 100, top = 100 + 15 = 115
        $objects = [$this->makeObject([
            'left' => 100.0, 'top' => 115.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'center', 'originY' => 'bottom',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200, top = 200 + 15 = 215 (offset is object's scaledHeight/2, not area)
        $this->assertEqualsWithDelta(200.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(215.0, $result[0]['top'], 0.01);
    }

    public function test_origin_right_bottom(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin right/bottom: left = centerX + scaledWidth/2, top = centerY + scaledHeight/2
        // Object center at (100, 100)
        // left = 100 + 50 = 150, top = 100 + 15 = 115
        $objects = [$this->makeObject([
            'left' => 150.0, 'top' => 115.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'right', 'originY' => 'bottom',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200 + 50 = 250, top = 200 + 15 = 215
        $this->assertEqualsWithDelta(250.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(215.0, $result[0]['top'], 0.01);
    }

    public function test_origin_left_bottom(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin left/bottom: left = centerX - scaledWidth/2, top = centerY + scaledHeight/2
        // Object center at (100, 100)
        // left = 100 - 50 = 50, top = 100 + 15 = 115
        $objects = [$this->makeObject([
            'left' => 50.0, 'top' => 115.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'left', 'originY' => 'bottom',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200 - 50 = 150, top = 200 + 15 = 215
        $this->assertEqualsWithDelta(150.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(215.0, $result[0]['top'], 0.01);
    }

    public function test_origin_right_center(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin right/center: left = centerX + scaledWidth/2, top = centerY
        // Object center at (100, 100)
        // left = 100 + 50 = 150, top = 100
        $objects = [$this->makeObject([
            'left' => 150.0, 'top' => 100.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'right', 'originY' => 'center',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200 + 50 = 250, top = 200
        $this->assertEqualsWithDelta(250.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(200.0, $result[0]['top'], 0.01);
    }

    public function test_origin_left_center(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin left/center: left = centerX - scaledWidth/2, top = centerY
        // Object center at (100, 100)
        // left = 100 - 50 = 50, top = 100
        $objects = [$this->makeObject([
            'left' => 50.0, 'top' => 100.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'left', 'originY' => 'center',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200 - 50 = 150, top = 200
        $this->assertEqualsWithDelta(150.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(200.0, $result[0]['top'], 0.01);
    }

    public function test_origin_center_top(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin center/top: left = centerX, top = centerY - scaledHeight/2
        // Object center at (100, 100)
        // left = 100, top = 100 - 15 = 85
        $objects = [$this->makeObject([
            'left' => 100.0, 'top' => 85.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.0, 'scaleY' => 1.0,
            'originX' => 'center', 'originY' => 'top',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target: left = 200, top = 200 - 15 = 185
        $this->assertEqualsWithDelta(200.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(185.0, $result[0]['top'], 0.01);
    }

    // ─────────────────────────────────────────────
    // Scenario 21: Origin preservation + scale
    // ─────────────────────────────────────────────

    public function test_properties_preserved_through_transform(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        $objects = [$this->makeObject([
            'left' => 100.0, 'top' => 100.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 1.5, 'scaleY' => 2.0,
            'angle' => 45.0,
            'opacity' => 0.7,
            'originX' => 'center', 'originY' => 'center',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        $this->assertEquals(45.0, $result[0]['angle']);
        $this->assertEquals(0.7, $result[0]['opacity']);
        $this->assertEquals(1.5, $result[0]['scaleX']);
        $this->assertEquals(2.0, $result[0]['scaleY']);
        $this->assertEquals(100, $result[0]['width']);
        $this->assertEquals(30, $result[0]['height']);
        $this->assertEquals('center', $result[0]['originX']);
        $this->assertEquals('center', $result[0]['originY']);
    }

    public function test_scaled_object_with_left_top_origin(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();
        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 0, 'y' => 0, 'width' => 400, 'height' => 400]);

        // origin left/top with scale 2x: scaledWidth = 200, scaledHeight = 60
        // Object center at source area center (100, 100)
        // left = 100 - 100 = 0, top = 100 - 30 = 70
        $objects = [$this->makeObject([
            'left' => 0.0, 'top' => 70.0,
            'width' => 100, 'height' => 30,
            'scaleX' => 2.0, 'scaleY' => 2.0,
            'originX' => 'left', 'originY' => 'top',
        ])];
        $result = $this->matchAndTransform($source, $target, $objects);

        // Target center = (200, 200). With scale 2x: scaledWidth=200, scaledHeight=60
        // left = 200 - 100 = 100, top = 200 - 30 = 170
        $this->assertEqualsWithDelta(100.0, $result[0]['left'], 0.01);
        $this->assertEqualsWithDelta(170.0, $result[0]['top'], 0.01);
    }
}
