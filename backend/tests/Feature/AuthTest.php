<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function testRegister()
    {
        $data = [
            'email'=>'test@gmail.com',
            'name'=>'Test',
            'password'=> 'secret1234'
        ];

        $response = $this->json('POST',route('api.register'),$data);

        $response->assertStatus(201);

        $this->assertArrayHasKey('token',$response->json());

        User::where('email',$data['email'])->delete();
    }

    public function testLogin()
    {
        User::create([
            'name' => 'test',
            'email'=>'test@gmail.com',
            'password' => bcrypt('secret1234')
        ]);

        // Simulated landing
        $response = $this->json('POST',route('api.authenticate'),[
            'email' => 'test@gmail.com',
            'password' => 'secret1234',
        ]);

        // Determine whether the login is successful and receive token 
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('token',$response->json());
        
        // Delete users
        User::where('email','test@gmail.com')->delete();
    }
}
