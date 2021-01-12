<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;

class UserControllerTest extends TestCase
{

    /**
     * Tests LIST USERS:
     * Para obtener una lista con los usuarios, es necesario estar logeado con algún usuario
     */

    // Obtenemos la lista de usuarios habiendonos logeado
    public function testListUsersOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([]);
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->get('20/api/v1/user/all');

        $response->assertStatus(200);
    }

    // Intentamos obtener la lista de usuarios sin habernos logeado
    public function testListUsersNotOK()
    {
        $response = $this->get('20/api/v1/user/all');

        $response->assertStatus(302);
    }

    public function testShowUserOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario1@secretario1.com',
            'password' => Hash::make('secretario1')
        ]);
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->get('20/api/v1/user/view/3');

        $response->assertStatus(200);
    }

    public function testShowUserNotOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
        ]);
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->get('20/api/v1/user/view/3');

        $response->assertStatus(401);
    }

}
