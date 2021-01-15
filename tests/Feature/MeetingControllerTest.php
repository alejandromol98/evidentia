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

class MeetingControllerTest extends TestCase
{

    /**
     * Tests LIST MEETINGS:
     * Para obtener una lista con las reuniones del usuario registrado, es necesario estar logeado con algún usuario
     */

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

    public function testListMeetingsOK(){

        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user =factory(User::class)->create([
            'email' => 'alumno7@alumno7.com',
            'password' => Hash::make('alumno7')
        ]);

        $this->actingAs($user, 'api');

        $response = $this->get('20/api/v1/meeting/list');

        $response->assertStatus(200);
    }

    public function testListMeetingsNotOK()
    {
        $response = $this->get('20/api/v1/meeting/list');

        $response->assertStatus(302);
    }

}
