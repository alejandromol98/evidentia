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

class EvidenceControllerTest extends TestCase
{

    /**
     * Tests LIST USERS:
     * Para obtener una lista con los usuarios, es necesario estar logeado con algún usuario
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

    public function testLoginCoordinatorTrue()
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

    // Obtenemos la lista de evidencias, logueandonos como secretario
    public function testListEvidencesOk()
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

        $response = $this->get('20/api/v1/evidence/list');

        $response->assertStatus(200);
    }

    // Obtenemos la lista de evidencias, sin ninguna autenticación
    public function testListEvidencesNotOk()
    {
        $response = $this->get('20/api/v1/evidence/list');

        $response->assertStatus(302);
    }

    //Creamos una evidencia
    public function testCreateEvidenceOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
        ]);

        $request = [
            'comittee'  => 2,
            'title' => 'Ejemplo de otra evidencia',
            'description' => 'Descripción de la evidencia',
            'hours' => '10',

        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/evidence/draft',$request);

        $response->assertStatus(201);
    }

    //Intentamos crear una evidencia sin estar autenticados
    public function testCreateEvidenceNotOk()
    {
        $response = $this->post('20/api/v1/evidence/draft');

        $response->assertStatus(302);
    }

    //Intentamos ver una evidencia que no pertenece al usuario logeado
    public function testViewEvidenceNotOk(){
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario3@secretario3.com',
            'password' => Hash::make('secretario3')
        ]);
        $this->actingAs($user, 'api');

        $response = $this->get('20/api/v1/evidence/view/1');

        $response->assertStatus(403);
    }

    //Intentamos ver una evidencia sin estar autenticado
    public function testViewEvidenceNotOk2(){
        $response = $this->get('20/api/v1/evidence/view/1');

        $response->assertStatus(302);
    }

    //Editamos una evidencia que no pertenece al usuario para ponerla en modo publicada.
    public function testEditEvidenceNotOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario6@secretario6.com',
            'password' => Hash::make('secretario6')
        ]);

        $request = [
            'title' => 'Ejemplo de edición de evidencia',
            'description' => 'Descripción de la evidencia',
            'hours' => '8',
            'files' => [],
        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/evidence/publish/edit/1',$request);

        $response->assertStatus(403);
    }

    //Editamos una evidencia que pertenece al usuario, pero esta vez poniéndola en modo borrador
    public function testEditEvidenceNotOk2()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $user = factory(User::class)->create([
            'email' => 'secretario7@secretario7.com',
            'password' => Hash::make('secretario7')
        ]);

        $request = [
            'title' => 'Ejemplo de edición de evidencia',
            'description' => 'Descripción de la evidencia',
            'hours' => '8',
            'files' => [],
        ];
        $this->actingAs($user, 'api');

        //See Below
        //$token = $user->generateToken();

        //$headers = [ 'Authorization' => 'Bearer ' +$token];

        $response = $this->post('20/api/v1/evidence/draft/edit/1',$request);

        $response->assertStatus(403);
    }

    //Intentamos poner en modo publicada una evidencia sin habernos autenticado
    public function testEditEvidenceNotOk3(){
        $response = $this->post('20/api/v1/evidence/publish/edit/1');

        $response->assertStatus(302);
    }

    //Intentamos poner en modo borrador una evidencia sin habernos autenticado
    public function testEditEvidenceNotOk4(){
        $response = $this->post('20/api/v1/evidence/draft/edit/1');

        $response->assertStatus(302);
    }

    //Intentamos borrar una evidencia que no es nuestra
    public function testRemoveEvidenceNotOk(){

        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario8@secretario8.com',
            'password' => Hash::make('secretario8')
        ]);

        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/evidence/remove/1');

        $response->assertStatus(403);
    }

    //Intentamos borrar una evidencia sin estar autenticados
    public function testRemoveEvidenceNotOk2(){

        $response = $this->post('20/api/v1/evidence/remove/1');

        $response->assertStatus(302);
    }


    //Intentamos volver a poner en modo borrador una evidencia que no es nuestra
    public function testReeditEvidenceNotOk(){

        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario9@secretario9.com',
            'password' => Hash::make('secretario9')
        ]);

        $this->actingAs($user, 'api');

        $response = $this->post('20/api/v1/evidence/reedit/1');

        $response->assertStatus(403);
    }

    //Intentamos volver a poner en modo borrados una evidencia sin estar autenticados
    public function testReeditEvidenceNotOk2(){
        $response = $this->post('20/api/v1/evidence/reedit/1');

        $response->assertStatus(302);
    }



}
