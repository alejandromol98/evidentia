<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Meeting;

class MeetingControllerTest extends TestCase
{

    /**
     * Tests LIST MEETINGS:
     * Para obtener una lista con las reuniones del usuario registrado, es necesario estar logeado con algún usuario
     */

    public function testListMeetingsOK(){
        \Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user =factory(User::class)->create([]);
        $this->actingAs($user, 'api');

        $response = $this->get('20/api/v1/meeting/list');

        $response->assertStatus(200);
    }

    public function testListUsersNotOK()
    {
        $response = $this->get('20/api/v1/meeting/list');

        $response->assertStatus(302);
    }

}
