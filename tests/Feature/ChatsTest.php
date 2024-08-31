<?php

namespace Tests\Feature;

use App\Models\Chat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testCanSendChat()
    {
        $chat = Chat::factory()->create();

        $response = $this->post(route('sendChat'), [
            'text'=>'hii there',
            'userId'=>'1'
        ]);

        $response->assertValid();
    }
}
