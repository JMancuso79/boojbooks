<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveBookFromUserListTest extends TestCase
{
	use RefreshDatabase;

    public function testRemoveBookFromUserList()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->get('/web-api/destroy/book/1')
        	->assertOk();
    }
}
