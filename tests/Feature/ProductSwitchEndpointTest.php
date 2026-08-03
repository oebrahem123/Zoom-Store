<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\PrintArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSwitchEndpointTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
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

    // ─────────────────────────────────────────────────
    // Full Pipeline: Same Template → No Confirmation
    // ─────────────────────────────────────────────────

    public function test_same_template_returns_no_confirmation_and_transforms(): void
    {
        $source = $this->createProduct(['name' => 'T-Shirt A']);
        $target = $this->createProduct(['name' => 'T-Shirt B']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 50, 'y' => 50, 'width' => 300, 'height' => 300]);

        $objects = [
            [
                '_zoomObjectId' => 'obj-1',
                '_slotKey' => 'front_main',
                'type' => 'i-text',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'confirmation' => ['needs_confirmation', 'affected_count', 'unaffected_count', 'missing_slots'],
            'transformed',
        ]);

        $data = $response->json();
        $this->assertFalse($data['confirmation']['needs_confirmation']);
        $this->assertCount(1, $data['transformed']);
        $this->assertEquals('obj-1', $data['transformed'][0]['_zoomObjectId']);
    }

    // ─────────────────────────────────────────────────
    // Full Pipeline: Different Template → Confirmation
    // ─────────────────────────────────────────────────

    public function test_different_template_returns_confirmation_with_missing_slots(): void
    {
        $source = $this->createProduct(['name' => 'T-Shirt']);
        $target = $this->createProduct(['name' => 'Hoodie']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($source, ['slot_key' => 'front_logo', 'view_name' => 'front', 'name' => 'Logo', 'area_type' => 'logo', 'slot_type' => 'logo', 'x' => 150, 'y' => 80, 'width' => 100, 'height' => 100]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 50, 'y' => 50, 'width' => 300, 'height' => 300]);
        $this->createArea($target, ['slot_key' => 'sleeve_left', 'view_name' => 'left sleeve', 'name' => 'Left Sleeve', 'area_type' => 'secondary', 'slot_type' => 'secondary', 'x' => 0, 'y' => 100, 'width' => 80, 'height' => 200]);

        $objects = [
            [
                '_zoomObjectId' => 'obj-1',
                '_slotKey' => 'front_main',
                'type' => 'i-text',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
            [
                '_zoomObjectId' => 'obj-2',
                '_slotKey' => 'front_logo',
                'type' => 'i-text',
                'left' => 200.0,
                'top' => 130.0,
                'width' => 50,
                'height' => 20,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();

        $data = $response->json();
        $this->assertTrue($data['confirmation']['needs_confirmation']);
        $this->assertGreaterThan(0, $data['confirmation']['affected_count']);

        $missingSlotKeys = array_column($data['confirmation']['missing_slots'], 'slot_key');
        $this->assertContains('front_logo', $missingSlotKeys);

        $this->assertCount(2, $data['transformed']);
    }

    // ─────────────────────────────────────────────────
    // Coordinate Transformation Correctness
    // ─────────────────────────────────────────────────

    public function test_transformed_coordinates_are_correct(): void
    {
        $source = $this->createProduct(['name' => 'Source']);
        $target = $this->createProduct(['name' => 'Target']);

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 50, 'y' => 50, 'width' => 400, 'height' => 400]);

        $objects = [
            [
                '_zoomObjectId' => 'obj-center',
                '_slotKey' => 'front_main',
                'type' => 'i-text',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                'originX' => 'center',
                'originY' => 'center',
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();

        $transformed = $response->json('transformed')[0];

        $this->assertEquals('obj-center', $transformed['_zoomObjectId']);
        $this->assertEqualsWithDelta(250.0, $transformed['left'], 0.01);
        $this->assertEqualsWithDelta(250.0, $transformed['top'], 0.01);
    }

    // ─────────────────────────────────────────────────
    // _zoomObjectId Preservation
    // ─────────────────────────────────────────────────

    public function test_fabric_id_is_preserved_through_transformation(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $objects = [
            [
                '_zoomObjectId' => 'custom-uuid-abc',
                '_slotKey' => 'front_main',
                'type' => 'image',
                'left' => 150.0,
                'top' => 150.0,
                'width' => 100,
                'height' => 100,
                'scaleX' => 2.0,
                'scaleY' => 2.0,
                'angle' => 45.0,
                'opacity' => 0.8,
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();

        $t = $response->json('transformed')[0];
        $this->assertEquals('custom-uuid-abc', $t['_zoomObjectId']);
        $this->assertEquals('image', $t['type']);
        $this->assertEquals(2.0, $t['scaleX']);
        $this->assertEquals(45.0, $t['angle']);
        $this->assertEquals(0.8, $t['opacity']);
    }

    // ─────────────────────────────────────────────────
    // Validation
    // ─────────────────────────────────────────────────

    public function test_validates_required_fields(): void
    {
        $response = $this->postJson('/api/editor/switch-product', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['source_product_id', 'target_product_id', 'objects']);
    }

    public function test_validates_product_exists(): void
    {
        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => 99999,
            'target_product_id' => 99999,
            'objects' => [],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['source_product_id', 'target_product_id']);
    }

    public function test_validates_objects_is_array(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => 'not-an-array',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['objects']);
    }

    // ─────────────────────────────────────────────────
    // Edge Cases
    // ─────────────────────────────────────────────────

    public function test_empty_objects_array_is_accepted(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => [],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'confirmation' => ['needs_confirmation'],
            'transformed',
        ]);
    }

    public function test_objects_without_slot_key_are_still_transformed_via_legacy(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main', 'x' => 100, 'y' => 100, 'width' => 200, 'height' => 200]);
        $this->createArea($target, ['slot_key' => 'front_main', 'x' => 50, 'y' => 50, 'width' => 200, 'height' => 200]);

        $objects = [
            [
                '_zoomObjectId' => 'legacy-obj',
                '_slotKey' => null,
                'type' => 'i-text',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();
        $this->assertCount(1, $response->json('transformed'));
        $this->assertEquals('legacy-obj', $response->json('transformed')[0]['_zoomObjectId']);
    }

    public function test_print_zone_objects_are_passed_through_unchanged(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $objects = [
            [
                '_zoomObjectId' => 'zone-1',
                '_slotKey' => 'front_main',
                'type' => 'rect',
                'left' => 100.0,
                'top' => 100.0,
                'width' => 200,
                'height' => 200,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 0.3,
                '_isPrintZone' => true,
                'excludeFromExport' => false,
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();

        $t = $response->json('transformed')[0];
        $this->assertEquals(100.0, $t['left']);
        $this->assertEquals(100.0, $t['top']);
    }

    public function test_multi_view_transforms_all_stored_views(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main', 'left' => 50, 'top' => 50, 'width' => 200, 'height' => 300]);
        $this->createArea($source, ['slot_key' => 'back_main', 'left' => 50, 'top' => 50, 'width' => 200, 'height' => 300]);
        $this->createArea($target, ['slot_key' => 'front_main', 'left' => 100, 'top' => 100, 'width' => 400, 'height' => 600]);
        $this->createArea($target, ['slot_key' => 'back_main', 'left' => 100, 'top' => 100, 'width' => 400, 'height' => 600]);

        $objects = [
            [
                '_zoomObjectId' => 'obj-1',
                '_slotKey' => 'front_main',
                'type' => 'textbox',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                'originX' => 'left',
                'originY' => 'top',
                '_isPrintZone' => false,
                'excludeFromExport' => false,
            ],
        ];

        $views = [
            '0' => [
                'objects' => [
                    [
                        '_zoomObjectId' => 'front-obj',
                        '_slotKey' => 'front_main',
                        'type' => 'textbox',
                        'left' => 200.0,
                        'top' => 200.0,
                        'width' => 100,
                        'height' => 30,
                        'scaleX' => 1.0,
                        'scaleY' => 1.0,
                        'angle' => 0,
                        'opacity' => 1.0,
                        'originX' => 'left',
                        'originY' => 'top',
                    ],
                ],
            ],
            '1' => [
                'objects' => [
                    [
                        '_zoomObjectId' => 'back-obj',
                        '_slotKey' => 'back_main',
                        'type' => 'textbox',
                        'left' => 150.0,
                        'top' => 150.0,
                        'width' => 80,
                        'height' => 24,
                        'scaleX' => 1.0,
                        'scaleY' => 1.0,
                        'angle' => 45,
                        'opacity' => 0.8,
                        'originX' => 'left',
                        'originY' => 'top',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
            'views' => $views,
        ]);

        $response->assertOk();

        $transformedViews = $response->json('transformed_views');
        $this->assertNotNull($transformedViews);
        $this->assertArrayHasKey('0', $transformedViews);
        $this->assertArrayHasKey('1', $transformedViews);

        $this->assertCount(1, $transformedViews['0']);
        $this->assertEquals('front-obj', $transformedViews['0'][0]['_zoomObjectId']);
        $this->assertNotEquals(200.0, $transformedViews['0'][0]['left'], 'Front view should be transformed');

        $this->assertCount(1, $transformedViews['1']);
        $this->assertEquals('back-obj', $transformedViews['1'][0]['_zoomObjectId']);
        $this->assertNotEquals(150.0, $transformedViews['1'][0]['left'], 'Back view should be transformed');
        $this->assertEquals(45, $transformedViews['1'][0]['angle']);
        $this->assertEquals(0.8, $transformedViews['1'][0]['opacity']);
    }

    public function test_multi_view_backward_compatible_without_views(): void
    {
        $source = $this->createProduct();
        $target = $this->createProduct();

        $this->createArea($source, ['slot_key' => 'front_main']);
        $this->createArea($target, ['slot_key' => 'front_main']);

        $objects = [
            [
                '_zoomObjectId' => 'obj-1',
                '_slotKey' => 'front_main',
                'type' => 'textbox',
                'left' => 200.0,
                'top' => 200.0,
                'width' => 100,
                'height' => 30,
                'scaleX' => 1.0,
                'scaleY' => 1.0,
                'angle' => 0,
                'opacity' => 1.0,
                'originX' => 'left',
                'originY' => 'top',
            ],
        ];

        $response = $this->postJson('/api/editor/switch-product', [
            'source_product_id' => $source->id,
            'target_product_id' => $target->id,
            'objects' => $objects,
        ]);

        $response->assertOk();
        $this->assertNull($response->json('transformed_views'));
        $this->assertCount(1, $response->json('transformed'));
    }
}
