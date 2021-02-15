<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetBookDetailsViewTest extends TestCase
{
	use RefreshDatabase;

    public function testGetBookDetailsView()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->get('/book-details/123ABC')
        	->assertOk();
    }
}
