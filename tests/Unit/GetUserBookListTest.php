<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUserBookListTest extends TestCase
{
	use RefreshDatabase;

    public function testGetUserBookList()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->get('/web-api/get/user-books')
        	->assertOk();
    }
}
