<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testUserCanRegister(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'arian',
            'email' => 'arian@arian.com',
            'username' => 'aria',
            'password' => '123456'
        ]);

        $response->assertSuccessful();
        $response->assertJsonIsObject('authorisation');
    }

    public function testUserCanNotRegisterWithSameEmailAndUsername()
    {
        $user=User::factory()->create();

        $response = $this->post(route('register'), $user->toArray());

        $response->assertInvalid();
    }

    public function testUserCanNotRegisterWithoutPassword()
    {
        $response = $this->post(route('register'), [
            'name' => 'arian',
            'email' => 'arian@arian.com',
            'username' => 'aria',
        ]);

        $response->assertInvalid();
    }

    public function testUserCanLogin()
    {
        $user=User::factory()->create();

        $response=$this->post(route('login'),[
            'email'=>$user->email,
            'password'=>'password'
        ]);

        $response->assertSuccessful();
    }

    public function testUserCanNotLoginWithWrongEmail()
    {
        $user=User::factory()->create();

        $response=$this->post(route('login'),[
            'email'=>'arianari',
            'password'=>'password'
        ]);

        $response->assertInvalid();
    }
}
