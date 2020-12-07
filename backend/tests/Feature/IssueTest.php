<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssueTest extends TestCase
{
    public $user;

    public $token;

    public $issue;

    public function setUp(): void
    {   
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'test',
            'email'=>'test@gmail.com',
            'password' => '$2y$10$bBdoVPDWKfBKTsUsNP4RA.lYXheQ2LH8EJU3UE2SlYbz3a/uqX95C' // secret1234
        ]);

        // Simulated landing
        $response = $this->json('POST',route('api.authenticate'),[
            'email' => 'test@gmail.com',
            'password' => 'secret1234',
        ]);
        
        $this->token = $response->json()['token'];

        $this->issue = 123;
    }

    public function testIssueJoin()
    {
        $response = $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.join',$this->issue) ,[]);

        $response->assertStatus(200);

    }

    public function testIssueGet()
    {
        $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.join',$this->issue) ,[]);

        $response = $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('GET', route('api.show',$this->issue) ,[]);

        $response->assertStatus(200);

    }

    public function testIssueVote()
    {
        $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.join',$this->issue) ,[]);

        $response = $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.vote',$this->issue) ,[ 'vote'=>10 ]);

        $response->assertStatus(201)->assertExactJson(['vote cast correctly']);

    }

    public function testIssueEndVote()
    {
        $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.join',$this->issue) ,[]);

        $response = $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('POST', route('api.end.vote',$this->issue) ,[ ]);

        $response->assertStatus(200)->assertExactJson(['the vote is over']);

        $response = $this->withHeaders([
            
            'Authorization'=>'Bearer '.$this->token

        ])->json('GET', route('api.show',$this->issue) ,[]);

    }

    public function tearDown(): void
    {
        User::where('email',$this->user->email)->delete();

        Redis::del('issue:'.$this->issue);

        parent::tearDown();
    }
}
