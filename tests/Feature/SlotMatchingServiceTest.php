<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\PrintArea;
use App\Services\SlotMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotMatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlotMatchingService $service;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SlotMatchingService();
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

    // ─────────────────────────────────────────────
    // Scenario 1: Identical templates (same slots)
    // ─────────────────────────────────────────────

    public function test_identical_products_returns_all_common_slots(): void
    {
        $source = $this->createProduct(['name' => 'T-Shirt A']);
        $target = $this->createProduct(['name' => 'T-Shirt B']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'back_main', 'view_index' => 1]);
        $this->createArea($source, ['slot_key' => 'left_sleeve', 'view_index' => 2]);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);
        $this->createArea($target, ['slot_key' => 'left_sleeve', 'view_index' => 2]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(3, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);

        $keys = array_column($result['common'], 'slot_key');
        $this->assertContains('front_main', $keys);
        $this->assertContains('back_main', $keys);
        $this->assertContains('left_sleeve', $keys);
    }

    // ─────────────────────────────────────────────
    // Scenario 2: Different templates (no overlap)
    // ─────────────────────────────────────────────

    public function test_completely_different_templates_returns_all_missing_and_new(): void
    {
        $source = $this->createProduct(['name' => 'T-Shirt']);
        $target = $this->createProduct(['name' => 'Hoodie']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'back_main', 'view_index' => 1]);

        $this->createArea($target, ['slot_key' => 'hood_front', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'hood_back', 'view_index' => 1]);
        $this->createArea($target, ['slot_key' => 'pocket', 'view_index' => 2]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(0, $result['common']);
        $this->assertCount(2, $result['missing']);
        $this->assertCount(3, $result['new']);

        $missingKeys = array_column($result['missing'], 'slot_key');
        $this->assertContains('front_main', $missingKeys);
        $this->assertContains('back_main', $missingKeys);

        $newKeys = array_column($result['new'], 'slot_key');
        $this->assertContains('hood_front', $newKeys);
        $this->assertContains('hood_back', $newKeys);
        $this->assertContains('pocket', $newKeys);
    }

    // ─────────────────────────────────────────────
    // Scenario 3: Partial overlap (common + missing + new)
    // ─────────────────────────────────────────────

    public function test_partial_overlap_returns_correct_split(): void
    {
        $source = $this->createProduct(['name' => 'Polo']);
        $target = $this->createProduct(['name' => 'T-Shirt']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0, 'x' => 50, 'y' => 50, 'width' => 200, 'height' => 200]);
        $this->createArea($source, ['slot_key' => 'collar_logo', 'view_index' => 1]);
        $this->createArea($source, ['slot_key' => 'left_sleeve', 'view_index' => 2]);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0, 'x' => 100, 'y' => 80, 'width' => 300, 'height' => 250]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);
        $this->createArea($target, ['slot_key' => 'right_sleeve', 'view_index' => 2]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertCount(2, $result['missing']);
        $this->assertCount(2, $result['new']);

        $this->assertEquals('front_main', $result['common'][0]['slot_key']);
        $this->assertTrue($result['common'][0]['coordinates_changed']);
    }

    // ─────────────────────────────────────────────
    // Scenario 4: Products with optional slots
    // ─────────────────────────────────────────────

    public function test_products_with_different_optional_slots(): void
    {
        $source = $this->createProduct(['name' => 'T-Shirt with logo']);
        $target = $this->createProduct(['name' => 'T-Shirt without logo']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'front_logo', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'back_main', 'view_index' => 1]);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(2, $result['common']);
        $this->assertCount(1, $result['missing']);
        $this->assertCount(0, $result['new']);

        $this->assertEquals('front_logo', $result['missing'][0]['slot_key']);
    }

    // ─────────────────────────────────────────────
    // Scenario 5: Target has extra optional slots
    // ─────────────────────────────────────────────

    public function test_target_with_additional_optional_slots(): void
    {
        $source = $this->createProduct(['name' => 'Basic']);
        $target = $this->createProduct(['name' => 'Premium']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'back_main', 'view_index' => 1]);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);
        $this->createArea($target, ['slot_key' => 'front_logo', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'left_sleeve', 'view_index' => 2]);
        $this->createArea($target, ['slot_key' => 'right_sleeve', 'view_index' => 3]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(2, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(3, $result['new']);
    }

    // ─────────────────────────────────────────────
    // Scenario 6: Old products without slot_key
    // ─────────────────────────────────────────────

    public function test_old_products_without_slot_key_are_excluded(): void
    {
        $source = $this->createProduct(['name' => 'Legacy']);
        $target = $this->createProduct(['name' => 'Modern']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => null, 'view_index' => 1, 'name' => 'Old Area']);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(1, $result['new']);

        $this->assertEquals('front_main', $result['common'][0]['slot_key']);
        $this->assertEquals('back_main', $result['new'][0]['slot_key']);
    }

    public function test_both_products_without_slot_key(): void
    {
        $source = $this->createProduct(['name' => 'Legacy A']);
        $target = $this->createProduct(['name' => 'Legacy B']);

        $this->createArea($source, ['slot_key' => null, 'view_index' => 0, 'name' => 'Area 1']);
        $this->createArea($target, ['slot_key' => null, 'view_index' => 0, 'name' => 'Area 2']);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(0, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);
    }

    // ─────────────────────────────────────────────
    // Scenario 7: Products with no print areas
    // ─────────────────────────────────────────────

    public function test_products_with_no_print_areas(): void
    {
        $source = $this->createProduct(['name' => 'Empty Source', 'type' => 'normal', 'is_designable' => false]);
        $target = $this->createProduct(['name' => 'Empty Target', 'type' => 'normal', 'is_designable' => false]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(0, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);
    }

    // ─────────────────────────────────────────────
    // Scenario 8: Coordinates detection
    // ─────────────────────────────────────────────

    public function test_coordinates_changed_detection(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, [
            'slot_key' => 'front_main',
            'x' => 50, 'y' => 50, 'width' => 200, 'height' => 200,
        ]);

        $this->createArea($target, [
            'slot_key' => 'front_main',
            'x' => 100, 'y' => 100, 'width' => 300, 'height' => 300,
        ]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertTrue($result['common'][0]['coordinates_changed']);
    }

    public function test_coordinates_unchanged_detection(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, [
            'slot_key' => 'front_main',
            'x' => 50, 'y' => 50, 'width' => 200, 'height' => 200,
        ]);

        $this->createArea($target, [
            'slot_key' => 'front_main',
            'x' => 50, 'y' => 50, 'width' => 200, 'height' => 200,
        ]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertFalse($result['common'][0]['coordinates_changed']);
    }

    // ─────────────────────────────────────────────
    // Scenario 9: Metadata integrity
    // ─────────────────────────────────────────────

    public function test_result_contains_template_versions(): void
    {
        $source = $this->createProduct(['name' => 'Source', 'template_version' => 1]);
        $target = $this->createProduct(['name' => 'Target', 'template_version' => 3]);

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $result = $this->service->matchSlots($source, $target);

        $this->assertEquals($source->id, $result['source_product_id']);
        $this->assertEquals($target->id, $result['target_product_id']);
        $this->assertEquals(1, $result['source_template_version']);
        $this->assertEquals(3, $result['target_template_version']);
    }

    public function test_extracted_area_data_contains_all_fields(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, [
            'slot_key' => 'front_main',
            'slot_type' => 'main',
            'name' => 'Front Main Area',
            'view_name' => 'front',
            'view_index' => 0,
            'x' => 10, 'y' => 20, 'width' => 150, 'height' => 250,
        ]);

        $this->createArea($target, [
            'slot_key' => 'front_main',
            'slot_type' => 'main',
            'name' => 'Front Main Area',
            'view_name' => 'front',
            'view_index' => 0,
            'x' => 30, 'y' => 40, 'width' => 180, 'height' => 280,
        ]);

        $result = $this->service->matchSlots($source, $target);

        $common = $result['common'][0];
        $this->assertEquals('front_main', $common['slot_key']);

        $sourceData = $common['source'];
        $this->assertArrayHasKey('id', $sourceData);
        $this->assertArrayHasKey('name', $sourceData);
        $this->assertArrayHasKey('slot_key', $sourceData);
        $this->assertArrayHasKey('slot_type', $sourceData);
        $this->assertArrayHasKey('view_name', $sourceData);
        $this->assertArrayHasKey('view_index', $sourceData);
        $this->assertArrayHasKey('x', $sourceData);
        $this->assertArrayHasKey('y', $sourceData);
        $this->assertArrayHasKey('width', $sourceData);
        $this->assertArrayHasKey('height', $sourceData);

        $targetData = $common['target'];
        $this->assertArrayHasKey('id', $targetData);
        $this->assertArrayHasKey('slot_key', $targetData);
        $this->assertEquals(30.0, $targetData['x']);
        $this->assertEquals(40.0, $targetData['y']);
    }

    // ─────────────────────────────────────────────
    // Scenario 10: Missing slots contain source data
    // ─────────────────────────────────────────────

    public function test_missing_slots_contain_source_area_data(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, [
            'slot_key' => 'front_logo',
            'slot_type' => 'logo',
            'name' => 'Logo Area',
            'view_name' => 'front',
            'view_index' => 0,
            'x' => 100, 'y' => 100, 'width' => 50, 'height' => 50,
        ]);

        $this->createArea($target, ['slot_key' => 'front_main']);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['missing']);
        $this->assertEquals('front_logo', $result['missing'][0]['slot_key']);
        $this->assertEquals('logo', $result['missing'][0]['source']['slot_type']);
        $this->assertEquals(100.0, $result['missing'][0]['source']['x']);
    }

    // ─────────────────────────────────────────────
    // Scenario 11: New slots contain target data
    // ─────────────────────────────────────────────

    public function test_new_slots_contain_target_area_data(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main']);

        $this->createArea($target, [
            'slot_key' => 'back_secondary',
            'slot_type' => 'secondary',
            'name' => 'Back Secondary',
            'view_name' => 'back',
            'view_index' => 1,
            'x' => 200, 'y' => 200, 'width' => 100, 'height' => 100,
        ]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['new']);
        $this->assertEquals('back_secondary', $result['new'][0]['slot_key']);
        $this->assertEquals('secondary', $result['new'][0]['target']['slot_type']);
        $this->assertEquals(200.0, $result['new'][0]['target']['x']);
    }

    // ─────────────────────────────────────────────
    // Scenario 12: Same slot_key across different view_names
    // ─────────────────────────────────────────────

    public function test_slot_key_matches_regardless_of_view_name(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, [
            'slot_key' => 'front_main',
            'view_name' => 'أمامي',
            'view_index' => 0,
        ]);

        $this->createArea($target, [
            'slot_key' => 'front_main',
            'view_name' => 'Front',
            'view_index' => 0,
        ]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);
        $this->assertEquals('front_main', $result['common'][0]['slot_key']);
    }

    // ─────────────────────────────────────────────
    // Scenario 13: Multiple views with shared view_index
    // ─────────────────────────────────────────────

    public function test_multiple_slots_share_view_index(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'front_logo', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => 'back_main', 'view_index' => 1]);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'front_logo', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(3, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);
    }

    // ─────────────────────────────────────────────
    // Scenario 14: Different slot_types in same key
    // ─────────────────────────────────────────────

    public function test_different_slot_types_dont_affect_matching(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'slot_type' => 'main']);
        $this->createArea($target, ['slot_key' => 'front_main', 'slot_type' => 'accessory']);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertEquals('main', $result['common'][0]['source']['slot_type']);
        $this->assertEquals('accessory', $result['common'][0]['target']['slot_type']);
    }

    // ─────────────────────────────────────────────
    // Scenario 15: Backward compatibility - old + new products
    // ─────────────────────────────────────────────

    public function test_backward_compatibility_mixed_old_and_new(): void
    {
        $source = $this->createProduct(['name' => 'Legacy Product']);
        $target = $this->createProduct(['name' => 'Modern Product', 'template_version' => 2]);

        $this->createArea($source, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($source, ['slot_key' => null, 'view_index' => 1, 'name' => 'Unnamed']);
        $this->createArea($source, ['slot_key' => null, 'view_index' => 2, 'name' => 'Unnamed 2']);

        $this->createArea($target, ['slot_key' => 'front_main', 'view_index' => 0]);
        $this->createArea($target, ['slot_key' => 'back_main', 'view_index' => 1]);
        $this->createArea($target, ['slot_key' => 'left_sleeve', 'view_index' => 2]);
        $this->createArea($target, ['slot_key' => 'right_sleeve', 'view_index' => 3]);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(3, $result['new']);
    }

    // ─────────────────────────────────────────────
    // Scenario 16: Empty string slot_key treated as null
    // ─────────────────────────────────────────────

    public function test_empty_string_slot_key_excluded(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($source, ['slot_key' => '', 'name' => 'Bad Area']);

        $this->createArea($target, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => '', 'name' => 'Bad Area']);

        $result = $this->service->matchSlots($source, $target);

        $this->assertCount(1, $result['common']);
        $this->assertCount(0, $result['missing']);
        $this->assertCount(0, $result['new']);
    }
}
