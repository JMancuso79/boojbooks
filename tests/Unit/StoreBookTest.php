<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreBookTest extends TestCase
{
	use RefreshDatabase;

    public function testStoreBook()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->post('/web-api/post/book', [
        	'google_id' => 'oeENEAAAQBAJ',
        	'user_id' => 1,
        	'title' => 'Test Title',
        	'author' => 'Jane Doe'
        ]);

        $this->assertCount(1, User::all());
    }
}
