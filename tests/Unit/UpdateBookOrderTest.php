<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateBookOrderTest extends TestCase
{
	use RefreshDatabase;

    public function testUpdateBookOrder()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->post('/web-api/update/book/1', [
        	'order' => 1
        ]);

        $response->assertStatus(200);
    }
}
