<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;
use App\Meeting;

class MeetingSecretaryControllerTest extends TestCase
{

    public function testSettingUp() :void {

        DB::connection()->getPdo()->exec("DROP DATABASE IF EXISTS `homestead`;");
        DB::connection()->getPdo()->exec("DROP DATABASE IF EXISTS `basetest`;");
        DB::connection()->getPdo()->exec("CREATE DATABASE IF NOT EXISTS `homestead`");
        DB::connection()->getPdo()->exec("ALTER SCHEMA `homestead`  DEFAULT CHARACTER SET utf8mb4  DEFAULT COLLATE utf8mb4_unicode_ci");
        exec("php artisan migrate");
        exec("php artisan db:seed");
        exec('php artisan db:seed --class=InstancesTableSeeder');

        $this->assertTrue(true);

    }

    public function testLoginSecretaryTrue()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'secretario1@secretario1.com',
            'password' => 'secretario1'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }

    public function testLoginAlumnTrue()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'alumno1@alumno1.com',
            'password' => 'alumno1'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }

    public function testListMeetingsSecretaryOk(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('20/api/v1/secretary/meeting/list');
        $response->assertStatus(200);
    }


    public function testListMeetingsSecretaryFalse(){
        \Artisan::call('passport:install');
        $this->testLoginAlumnTrue();

        $response = $this->get('20/api/v1/secretary/meeting/list');
        $response->assertStatus(403);
    }

    public function testCreateMeetingOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario3@secretario3.com',
            'password' => Hash::make('secretario3')
        ]);

        $request = [
            'tittle'  => 'Ejemplo de una reunion',
            'hours' => 2,
            'type' => 1,
            'place' => 'Ejemplo de lugar',
            'date' => '20-12-21',
            'time' => '10:30',
            'users' => [3,4,5]

        ];
        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/secretary/meeting/new',$request);

        $response->assertStatus(201);
    }

    public function testCreateMeetingNoOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario4@secretario4.com',
            'password' => Hash::make('secretario4')
        ]);

        $request = [
            'tittle'  => 'Ejemplo de una reunion',
            'hours' => 2,
            'type' => 1,
            'place' => '',
            'date' => '20-12-21',
            'time' => '10:30',
            'users' => [3,4,5]

        ];
        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/secretary/meeting/new',$request);

        $response->assertStatus(201);
    }

    public function testCreateMeetingNotOk2()
    {
        $response = $this->post('20/api/v1/secretary/meeting/new');

        $response->assertStatus(302);
    }


    public function testEditMeetingNotOk2()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario8@secretario8.com',
            'password' => Hash::make('secretario8')
        ]);

        $request = [
            'tittle'  => 'Ejemplo de una reunion editada',
            'hours' => 2,
            'type' => 1,
            'place' => 'Ejemplo de lugar',
            'date' => '20-12-21',
            'time' => '10:30',
            'users' => [3,4,5]
        ];
        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/secretary/meeting/edit/6',$request);

        $response->assertStatus(403);
    }

    public function testRemoveMeetingNoOk(){

        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $user = factory(User::class)->create([
            'email' => 'secretario9@secretario9.com',
            'password' => Hash::make('secretario9')
        ]);

        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/secretary/meeting/remove/1');

        $response->assertStatus(403);

    }

    public function testRemoveMeetingNoOk2(){

        $response = $this->post('20/api/v1/secretary/meeting/remove/1');

        $response->assertStatus(302);

    }





}
