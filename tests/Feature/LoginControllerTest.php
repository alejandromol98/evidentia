<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    public function testLoginApiTrue()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'admin@admin.com',
            'password' => 'admin'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }

    public function testLoginApiFalse()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'kdfkjs@admin.com',
            'password' => 'admin'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(401);
    }
}
