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

    public function testAllEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/all');
        $response->assertStatus(200);
    }

    public function testAllEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/all');
        $response->assertStatus(403);
    }

    public function testPendingEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/pending');
        $response->assertStatus(200);
    }

    public function testPendingEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/pending');
        $response->assertStatus(403);
    }

    public function testAcceptedEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/accepted');
        $response->assertStatus(200);
    }

    public function testAcceptedEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/accepted');
        $response->assertStatus(403);
    }

    public function testRejectedEvidencesTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/rejected');
        $response->assertStatus(200);
    }

    public function testRejectedEvidencesFalse(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/list/rejected');
        $response->assertStatus(403);
    }

    public function testAcceptEvidenceTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/14');
        $response->assertStatus(200);
    }

    public function testAcceptEvidenceFalse(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/8');
        $response->assertStatus(403);
    }

    public function testAcceptEvidenceFalse2(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/accept/14');
        $response->assertStatus(403);
    }

    public function testRejectEvidenceTrue(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/14');
        $response->assertStatus(200);
    }

    public function testRejectEvidenceFalse(){
        \Artisan::call('passport:install');
        $this->testLoginCoordinatorTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/8');
        $response->assertStatus(403);
    }

    public function testRejectEvidenceFalse2(){
        \Artisan::call('passport:install');
        $this->testLoginSecretaryTrue();

        $response = $this->get('{instance}/api/v1/coordinator/evidence/reject/14');
        $response->assertStatus(403);
    }
}
