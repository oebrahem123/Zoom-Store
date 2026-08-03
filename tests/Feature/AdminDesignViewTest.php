<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CustomDesign;
use App\Models\DesignElement;
use App\Models\Order;
use App\Models\orderdetails;
use App\Permissions\Permission as PermissionKey;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDesignViewTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private CustomDesign $design;

    protected function setUp(): void
    {
        parent::setUp();

        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer']);
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);

        collect([PermissionKey::ORDERS_VIEW, PermissionKey::DASHBOARD_VIEW])
            ->each(fn($key) => $adminRole->permissions()->attach(
                \App\Models\Permission::create(['key' => $key, 'display_name' => $key])
            ));

        $this->customer = User::factory()->create(['name' => 'Test Customer', 'role_id' => $customerRole->id]);
        $this->admin = User::factory()->create(['name' => 'Different Admin', 'role_id' => $adminRole->id]);

        $category = Category::create(['name' => 'Test Category']);

        $product = Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'is_designable' => true,
            'price' => 100,
            'quantity' => 10,
            'type' => 'custom',
            'imagepath' => 'assets/img/pic09.jpg',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => 'M',
            'color' => 'Black',
            'quantity' => 10,
            'price' => 100,
        ]);

        $order = new Order();
        $order->user_id = $this->customer->id;
        $order->name = 'Customer Name';
        $order->email = 'customer@test.com';
        $order->phone = '0123456789';
        $order->address = 'Test Address';
        $order->save();

        $this->design = new CustomDesign();
        $this->design->user_id = $this->customer->id;
        $this->design->product_id = $product->id;
        $this->design->variant_id = $variant->id;
        $this->design->view = '0';
        $this->design->save();

        $element = new DesignElement();
        $element->design_id = $this->design->id;
        $element->type = 'text';
        $element->content = 'Hello World';
        $element->view = 0;
        $element->position_x = 0;
        $element->position_y = 0;
        $element->rotation = 0;
        $element->z_index = 0;
        $element->save();

        $detail = new orderdetails();
        $detail->order_id = $order->id;
        $detail->product_id = $product->id;
        $detail->variant_id = $variant->id;
        $detail->design_id = $this->design->id;
        $detail->quantity = 1;
        $detail->price = 100;
        $detail->size = 'M';
        $detail->color = 'Black';
        $detail->save();
    }

    public function test_admin_can_save_design(): void
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('design.store'), [
            'design_id' => $this->design->id,
            'product_id' => $this->design->product_id,
            'variant_id' => $this->design->variant_id,
            'view' => '0',
            'admin_mode' => true,
            'designs' => [
                [
                    'view_index' => 0,
                    'print_area_id' => null,
                    'elements' => [
                        [
                            'type' => 'text',
                            'content' => 'Updated Text',
                            'position_x' => 100,
                            'position_y' => 100,
                            'rotation' => 0,
                            'color' => '#000000',
                            'font_family' => 'Cairo',
                            'font_size' => 24,
                            'font_weight' => 400,
                            'z_index' => 0,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Design saved successfully']);
        $this->assertEquals('Updated Text', $this->design->fresh()->elements->first()->content);
    }

    public function test_customer_can_save_own_design(): void
    {
        $this->actingAs($this->customer);

        $response = $this->postJson(route('design.store'), [
            'design_id' => $this->design->id,
            'product_id' => $this->design->product_id,
            'variant_id' => $this->design->variant_id,
            'view' => '0',
            'designs' => [
                [
                    'view_index' => 0,
                    'print_area_id' => null,
                    'elements' => [
                        [
                            'type' => 'text',
                            'content' => 'Customer Update',
                            'position_x' => 50,
                            'position_y' => 50,
                            'rotation' => 0,
                            'color' => '#000000',
                            'font_family' => 'Arial',
                            'font_size' => 18,
                            'font_weight' => 400,
                            'z_index' => 0,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Design saved successfully']);
        $this->assertEquals('Customer Update', $this->design->fresh()->elements->first()->content);
    }

    public function test_unauthenticated_cannot_save_design(): void
    {
        $response = $this->postJson(route('design.store'), [
            'design_id' => $this->design->id,
            'designs' => [],
        ]);

        $response->assertStatus(401);
    }
}
