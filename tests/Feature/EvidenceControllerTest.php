<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\User;

class EvidenceControllerTest extends TestCase
{

    /**
     * Tests LIST USERS:
     * Para obtener una lista con los usuarios, es necesario estar logeado con algún usuario


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

    // Obtenemos la lista de evidencias, sin ninguna autenticación
    public function testCreateEvidenceNotOk()
    {
        $response = $this->post('20/api/v1/evidence/draft');

        $response->assertStatus(302);
    }

    //Editamos una evidencia que pertenece al usuario.
    public function testEditEvidenceOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();


        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
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

        $response->assertStatus(201);
    }

    //Editamos una evidencia que no pertenece al usuario.
    public function testEditEvidenceNotOk()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'secretario2@secretario2.com',
            'password' => Hash::make('secretario2')
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

        $response = $this->post('20/api/v1/evidence/publish/edit/4',$request);

        $response->assertStatus(403);
    } */


}
