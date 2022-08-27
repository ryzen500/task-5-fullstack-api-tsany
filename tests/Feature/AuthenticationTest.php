<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_berhasil()
    {
       $user = [
        "name" => "test",
        "email" => "test@gmail.com",
        "password" => "password", //password
        "password_confirmation" => "password", //password
       ];
        $this->json('POST', 'api/v1/register', $user, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                "data"
            ]);
    }
    public function test_register_gagal()
    {
        $this->json('POST', 'api/v1/register' , ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            "error"
        ]);
    }
    public function test_login_berhasil()
    {
        $userData = [
            "email" => "test@gmail.com",
            "password" => "password"
        ];
        $this->json('POST','/api/v1/login', $userData,['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "message",
            "data"        
        ]);
        $this->assertAuthenticated();
        User::whereEmail('test@gmail.com')->delete(); //delete user

    }
    public function test_login_gagal()
    {
        $this->json('POST','/api/v1/login',['Accept' => 'application/json'])
        ->assertStatus(401)
        ->assertJsonStructure([
            "status",
            "message",   
        ]);
    }
}
