<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Models\Participant;
use Arr;
use DrawCrypt;
use Faker\Generator as Faker;
use NoCaptcha;
use Tests\TestCase;

class RequestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function ajaxPost($url, array $postArgs = [], $headers = [])
    {
        $headers = $headers + [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-HASH-KEY'       => base64_encode(DrawCrypt::getKey())
        ];

        return $this->withHeaders($headers)->json('POST', $url, $postArgs);
    }

    public function createAjaxDraw(int $totalParticipants): array
    {
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $participants = $this->generateParticipants($totalParticipants);

        // Initiate DearSanta
        $this->ajaxPost('/', [
                'participants'    => $participants,
                'title'           => 'test mail title',
                'content-email'   => 'test mail {SANTA} => {TARGET}',
                'data-expiration' => date('Y-m-d', strtotime('+2 days')),
            ])
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ])
            ->assertStatus(200);

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
     * Expected $participants array format:
     *
     * $participants = [
     *  [
     *      'name'   => 'foo',
     *      'email'  => 'test@test.com',
     *      'target' => 1,
     *  ],
     *  [
     *      'name'   => 'bar',
     *      'email'  => 'test2@test.com',
     *      'target' => 2,
     *  ],
     *  [
     *      'name'   => 'foobar',
     *      'email'  => 'test3@test.com',
     *      'target' => 0,
     *  ],
     * ];
     */
    public function formatParticipants($participants): array
    {
        $participants = array_map(function ($idx) use ($participants) {
            if (isset($participants[$idx]['target'])) {
                $participants[$idx] += [
                    // Remove the keys and cast as string to simulate an html form submission
                    'exclusions' => array_values(
                        array_map('strval',
                            // Get all the participants idx but the current one and the target
                            // (this participant will only draw their target and nobody else)
                            array_diff(array_keys($participants), [$idx], [$participants[$idx]['target']])
                        )
                    ),
                ];
            }
            return $participants[$idx];
        }, array_keys($participants));

        return $participants;
    }
}
