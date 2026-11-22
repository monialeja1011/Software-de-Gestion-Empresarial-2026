<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentasReglasTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private int $sellerRoleId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);

        $this->seller = User::factory()->create();
        $this->seller->assignRole('vendedor');
        $this->sellerRoleId = (int) $this->seller->roles()->first()->id;
    }

    private function submitSale(Product $product, int $quantity, ?Client $client = null)
    {
        $client ??= Client::factory()->create();
        $method = PaymentMethod::query()->firstOrCreate(['name' => 'Efectivo']);

        return $this->actingAs($this->seller)
            ->withSession(['active_role_id' => $this->sellerRoleId])
            ->post(route('sales.store'), [
                'client_id' => $client->id,
                'payment_method_id' => $method->id,
                'sale_date' => now()->toDateString(),
                'items' => [
                    ['product_id' => $product->id, 'quantity' => $quantity],
                ],
            ]);
    }

    public function test_venta_descuenta_stock_y_separa_el_iva_incluido(): void
    {
        $product = Product::factory()->create([
            'price' => 119000,
            'iva_percentage' => 19,
            'stock' => 10,
        ]);

        $this->submitSale($product, 1)->assertRedirect();

        $sale = Sale::firstOrFail();

        $this->assertSame(9, (int) $product->fresh()->stock);
        $this->assertSame(100000.0, (float) $sale->subtotal);
        $this->assertSame(19000.0, (float) $sale->tax);
        $this->assertSame(119000.0, (float) $sale->total);
    }

    public function test_no_permite_vender_mas_unidades_que_el_stock(): void
    {
        $product = Product::factory()->create(['stock' => 2]);

        $this->submitSale($product, 5)->assertSessionHasErrors('items');

        $this->assertSame(2, (int) $product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_no_permite_vender_a_un_cliente_inactivo(): void
    {
        $product = Product::factory()->create();
        $client = Client::factory()->inactive()->create();

        $this->submitSale($product, 1, $client)->assertSessionHasErrors('client_id');

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_no_permite_vender_un_producto_inactivo(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->submitSale($product, 1)->assertSessionHasErrors();

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_anular_venta_devuelve_el_stock(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $this->submitSale($product, 4)->assertRedirect();
        $sale = Sale::firstOrFail();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $adminRoleId = (int) $admin->roles()->first()->id;

        $this->actingAs($admin)
            ->withSession(['active_role_id' => $adminRoleId])
            ->delete(route('sales.destroy', $sale))
            ->assertRedirect();

        $this->assertSame(10, (int) $product->fresh()->stock);
        $this->assertSoftDeleted('sales', ['id' => $sale->id]);
    }

    public function test_vendedor_no_puede_anular_ventas(): void
    {
        $product = Product::factory()->create();
        $this->submitSale($product, 1);
        $sale = Sale::firstOrFail();

        $this->actingAs($this->seller)
            ->withSession(['active_role_id' => $this->sellerRoleId])
            ->delete(route('sales.destroy', $sale))
            ->assertForbidden();

        $this->assertSame(9, (int) $product->fresh()->stock);
        $this->assertDatabaseHas('sales', ['id' => $sale->id]);
    }
}
