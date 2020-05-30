<?php

namespace Tests\Feature;

use App\Draw;
use App\Participant;
use Arr;
use Faker\Generator as Faker;
use NoCaptcha;
use Tests\TestCase;

class RequestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);
        NoCaptcha::makePartial(); // We don't want to mock the display
    }

    public function rawAjaxPost($url, array $postArgs = [], $headers = [])
    {
        $headers = $headers + [
            'Content-Type' => 'application/json',
        ];

        $postArgs = json_encode($postArgs);

        return $this->ajaxPost($url, $postArgs, $headers);
    }

    public function ajaxPost($url, array $postArgs = [], $headers = [])
    {
        $headers = $headers + [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        $postArgs = $postArgs + [
            'g-recaptcha-response' => 'mocked',
        ];

        return $this->withHeaders($headers)->json('POST', $url, $postArgs);
    }

    public function createNewDraw(int $totalParticipants): array
    {
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $participants = $this->generateParticipants($totalParticipants);

        // Initiate DearSanta
        $response = $this->ajaxPost('/', [
            'participants'    => $participants,
            'title'           => 'test mail title',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        $this->assertEquals(1, Draw::count());
        $this->assertEquals($totalParticipants, Participant::count());

        return $participants;
    }

    public function generateParticipants(int $totalParticipants): array
    {
        $faker = app(Faker::class);

        $participants = [];
        for ($i = 0; $i < $totalParticipants; $i++) {
            $participants[] = [
                'name' => $faker->unique()->name,
                'email' => $faker->unique()->safeEmail,
                'target' => ($i === 0) ? $totalParticipants - 1 : $i - 1
            ];
        }

        return $this->formatParticipants($participants);
    }

    /**
     * $participants = [
     * [
     * 'name'   => 'toto',
     * 'email'  => 'test@test.com',
     * 'target' => 1,
     * ],
     * [
     * 'name'   => 'tata',
     * 'email'  => 'test2@test.com',
     * 'target' => 2,
     * ],
     * [
     * 'name'   => 'tutu',
     * 'email'  => 'test3@test.com',
     * 'target' => 0,
     * ],
     * ];.
     */
    public function formatParticipants($participants): array
    {
        $participants = array_map(function ($id) use ($participants) {
            if (isset($participants[$id]['target'])) {
                $participants[$id] += [
                    'exclusions' => array_values(array_map('strval', array_diff(array_keys($participants), [$id], [$participants[$id]['target']]))),
                ];
            }
            return $participants[$id];
        }, array_keys($participants));

        return $participants;
    }
}
