<?php


namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Faker\Generator as Faker;
use Laravel\Passport\Passport;
use App\User;

class DefaultListControllerTest extends TestCase
{

    public function testListDefaultListsOK()
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

        $response = $this->get('20/api/v1/secretary/defaultlist/list');

        $response->assertStatus(200);
    }

    public function testListEvidencesNotOk()
    {
        $response = $this->get('20/api/v1/secretary/defaultlist/list');

        $response->assertStatus(302);
    }

    public function testCreateDefaultListOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
        ]);

        $request = [
            'name'  => 'Ejemplo de otra default list',
            'users' => [
                '1'
            ],
        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/secretary/defaultlist/new',$request);

        $response->assertStatus(201);
    }

    public function testEditDefaultListOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
        ]);

        $request = [
            'title' => 'Ejemplo de edición de default list',
            'users' => [
                '1'
            ],
        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/secretary/defaultlist/edit/1',$request);

        $response->assertStatus(201);
    }

    public function testEditDefaultListNotOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
        ]);

        $request = [
            'title' => 'Ejemplo de edición de default list',
            'users' => [
                '1'
            ],
        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/secretary/defaultlist/edit/3',$request);

        $response->assertStatus(403);
    }


}
