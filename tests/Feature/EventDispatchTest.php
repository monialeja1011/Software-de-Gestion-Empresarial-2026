<?php

namespace Tests\Feature;

use App\Events\SaleRegistered;
use App\Models\Sale;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EventDispatchTest extends TestCase
{
    public function test_evento_de_venta_se_puede_despachar(): void
    {
        Event::fake();

        SaleRegistered::dispatch(new Sale);

        Event::assertDispatched(SaleRegistered::class);
    }
}
