<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Generator as Faker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;


class UserControllerTest extends TestCase
{
    use RefreshDatabase;


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

    /*
     * Test Show User
     * Solo puede acceder a sus datos el propio usuario
     * Si un usuario intenta acceder a la informacion de otro usuario, devolverá un código de error 401: Unauthorized
     */
    public function testShowUserOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario1@secretario1.com',
            'password' => Hash::make('secretario1')
        ]);
        $this->actingAs($user, 'api');

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

        $response = $this->get('20/api/v1/user/view/3');

        $response->assertStatus(401);
    }

    /*
     * Test Create New User
     * Un usuario no podrá ser creado si ya existe otro con el mismo "username", "email" o "dni
     */


    public function testCreateUserOK(){
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $request = [
            "name" => "Miguel12",
            "surname" => "Saavedra12",
            "username" => "test123",
            "password" => "test12356789",
            "email" => "test@test.com",
            "dni" => "483902198",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario es de ejemplo"
        ];

        $response = $this->post('20/api/v1/user/new', $request);

        $response->assertStatus(200);
    }

    public function testCreateUserNotOK(){
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            "name" => "Ejemplo",
            "surname" => "Ejemplo",
            "username" => "secretario1",
            "password" => "ejemplo123",
            "email" => "secretario1@secretario1.com",
            "dni" => "12312312A",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario es de ejemplo"
        ];

        $response = $this->post('20/api/v1/user/new', $request);

        $response->assertStatus(401);
    }

    /*
     * EDIT USER: un usuario no puede editar ni su dni ni su username
     */

    // Test Edit User 1: un usuario intenta editar sus datos personales correctamente
    public function testEditUserOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'coordinador1@coordinador1.com',
            'password' => Hash::make('coordinador1')
        ]);
        $this->actingAs($user, 'api');

        $request = [
            "name" => "Margaret",
            "surname" => "Hendricks",
            "password" => "coordinador1",
            "email" => "coordinador1@coordinador1.com",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario se ha editado correctamente"
        ];

        $response = $this->post('20/api/v1/user/edit/5', $request);

        $response->assertStatus(200);
    }

    // Test Edit User 2: un usuario intenta editar a otro usuario. Devuelve error 401 Unauthorized
    public function testEditUserNotOKNotLogged()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'coordinador2@coordinador2.com',
            'password' => Hash::make('coordinador2')
        ]);
        $this->actingAs($user, 'api');

        $request = [
            "name" => "Ejemplo",
            "surname" => "Ejemplo",
            "password" => "coordinador1",
            "email" => "coordinador1@coordinador1.com",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario se ha editado"
        ];

        $response = $this->post('20/api/v1/user/edit/5', $request);

        $response->assertStatus(401);
    }

    // Test Edit User 3: Se intenta editar a un usuario sin haberse logeado. Devuelve error 401 Unauthorized


    public function testEditUserNotOKNotLogged2()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            "name" => "Ejemplo",
            "surname" => "Ejemplo",
            "password" => "coordinador1",
            "email" => "coordinador1@coordinador1.com",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario se ha editado"
        ];

        $response = $this->post('20/api/v1/user/edit/5', $request);

        $response->assertStatus(401);
    }

    // Test Edit User 3: un usuario intenta editar su email y poner otro que ya existe. Devuelve error 401 Unauthorized
    public function testEditUserNotOKEmailNotUnique()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'coordinadorregistro1@coordinadorregistro1.com',
            'password' => Hash::make('coordinadorregistro1')
        ]);
        $this->actingAs($user, 'api');

        $request = [
            "name" => "Ejemplo",
            "surname" => "Ejemplo",
            "email" => "coordinador1@coordinador1.com",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario se ha editado"
        ];

        $response = $this->post('20/api/v1/user/edit/7', $request);

        $response->assertStatus(401);
    }

    // Test Edit User 4: un usuario intenta editar sus datos sin enviar los atributos requeridos (name,surname, biography o assistance). Devuelve error 401 Unauthorized
    public function testEditUserNotOKDataNotProvided()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'coordinadorregistro2@coordinadorregistro2.com',
            'password' => Hash::make('coordinadorregistro2')
        ]);
        $this->actingAs($user, 'api');

        $request = [
            "email" => "coordinador1@coordinador1.com",
            "participation" => "ASSISTANCE",
            "biography" => "Este usuario se ha editado"
        ];

        $response = $this->post('20/api/v1/user/edit/8', $request);

        $response->assertStatus(401);
    }

    /*
    * REMOVE USER: un usuario no puede borrar a otro usuario que no sea el
    */

    // Test Remove User: un usuario intenta eliminar una cuenta que no es la suya

    public function testRemoveUserNotOK()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'test@test.com',
            'password' => Hash::make('Miguel12')
        ]);
        $this->actingAs($user, 'api');


        $response = $this->post('20/api/v1/user/remove/6');

        $response->assertStatus(401);
    }


    // Test Remove User: Intentar eliminar usuarios sin haberse autentificado

    public function testRemoveUserNotOk2()
    {
        $response = $this->post('20/api/v1/user/remove/6');

        $response->assertStatus(401);
    }
    public function testLoginApiTrue()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'profesor1@profesor1.com',
            'password' => 'profesor1'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }




}
