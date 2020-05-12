<?php

namespace Tests\Feature;

use BeInMedia\Models\Expert;
use BeInMedia\Models\User;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    /*
     * @return void
     */
    public function testAppointmentBookView()
    {
        $user=factory(User::class)->create();
        $this->actingAs($user);
        $expert=Expert::create([
            'country'=>'Syria',
            'timezone'=>'Asia/Damascus',
            'start_time'=>'01:00:00',
            'end_time'=>'07:00:00',
            'user_id'=>$user->id,
            'expert'=>'Software engineer'
        ]);
        $response = $this->get(route('expert.book',$expert->id));

        $response->assertStatus(200);
        $user->delete();
    }

}
