<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EvidenceCoordinatorControllerTest extends TestCase
{
    //Nos logueamos como coordinador2
    public function testLoginCoordinatorTrue()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'coordinador2@coordinador2.com',
            'password' => 'coordinador2'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }

    //Nos logueamos como coordinador 1
    public function testLoginCoordinatorTrue2()
    {
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $request = [
            'email' => 'coordinador1@coordinador1.com',
            'password' => 'coordinador1'
        ];

        $response = $this->post('20/api/v1/login',$request);
        $response->assertStatus(200);
    }

    //Nos logueamos como secretario1
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

    //Listamos todas las evidencias que pertenecen al comité del coordinador2
    public function testAllEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/all');
        $response->assertStatus(200);
    }

    //Intentamos listar todas las evidencias de un comité logueándonos como secretario
    public function testAllEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/all');
        $response->assertStatus(403);
    }

    //Listamos todas las evidencias en estado PENDING que pertenecen al comité del coordinador2
    public function testPendingEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/pending');
        $response->assertStatus(200);
    }

    //Intentamos listar todas las evidencias en estado PENDING habiéndonos logueado como secretario
    public function testPendingEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/pending');
        $response->assertStatus(403);
    }

    //Listamos todas las evidencias en estado ACCEPTED que pertencen al comité del coordinador 2
    public function testAcceptedEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/accepted');
        $response->assertStatus(200);
    }

    //Intentamos listar todas las evidencias en estado ACCEPTED habiéndonos logueado como secretario
    public function testAcceptedEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/accepted');
        $response->assertStatus(403);
    }

    //Listamos todas las evidencias en estado REJECTED que pertencen al comité del coordinador 2
    public function testRejectedEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/rejected');
        $response->assertStatus(200);
    }

    //Intentamos listar todas las evidencias en estado REJECTED habiéndonos logueado como secretario
    public function testRejectedEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/rejected');
        $response->assertStatus(403);
    }

    //Aceptamos una evidencia siendo coordinador
    public function testAcceptEvidenceTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/14');
        $response->assertStatus(200);
    }

    //Intentamos aceptar una evidencia de un comité distinto al del coordinador con el que nos hemos logueado
    public function testAcceptEvidenceFalse(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/8');
        $response->assertStatus(403);
    }

    //Intentamos aceptar una evidencia habiéndonos logueado como secretario
    public function testAcceptEvidenceFalse2(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/14');
        $response->assertStatus(403);
    }

    //Rechazamos una evidencia siendo coordinador
    public function testRejectEvidenceTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/14');
        $response->assertStatus(200);
    }

    //Intentamos rechazar una evidencia de un comité distinto al del coordinador con el que nos hemos logueado
    public function testRejectEvidenceFalse(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/8');
        $response->assertStatus(403);
    }

    //Intentamos rechazar una evidencia habiéndonos logueado como secretario
    public function testRejectEvidenceFalse2(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/14');
        $response->assertStatus(403);
    }
}
