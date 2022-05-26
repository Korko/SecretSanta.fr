<?php

namespace Tests\Browser;

use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\ExclusionPicker;
use Tests\Browser\Pages\RandomFormPage;
use Tests\DuskTestCase;

class RandomFormTest extends DuskTestCase
{
    use withFaker;

    public function testDuplicateName()
    {
        $this->browse(function (Browser $browser) {
            $name = $this->faker->name;

            $browser->visit(new RandomFormPage)
                    ->type('participants[0][name]', $name)
                    ->type('participants[1][name]', $name)
                    ->assertPresent('input[name=\'participants[0][name]\'].is-invalid')
                    ->scrollIntoView('#randomForm button[type=submit]')
                    ->click('#randomForm button[type=submit]')
                    ->assertPresent('input[name=\'participants[1][name]\'].is-invalid');
        });
    }

    public function testNoResult()
    {
        $this->browse(function (Browser $browser) {
            $name1 = $this->faker->name;
            $name2 = $this->faker->name;
            $name3 = $this->faker->name;

            $browser->visit(new RandomFormPage)
                    ->type('participants[0][name]', $name1)
                    ->type('participants[1][name]', $name2)
                    ->type('participants[2][name]', $name3)
                    ->type('participants[0][email]', $this->faker->email)
                    ->type('participants[1][email]', $this->faker->email)
                    ->type('participants[2][email]', $this->faker->email)
                    ->within(new ExclusionPicker('tr[dusk=participant0]'), function ($browser) use ($name2, $name3) {
                        $browser->selectParticipant($name2)
                                ->selectParticipant($name3);
                    })
                    ->within(new ExclusionPicker('tr[dusk=participant2]'), function ($browser) use ($name1) {
                        $browser->selectParticipant($name1);
                    })
                    ->type('title', $this->faker->sentence)
                    ->type('content', $this->faker->paragraph)
                    ->append('content', '{TARGET}')
                    ->scrollIntoView('#randomForm button[type=submit]')
                    ->click('#randomForm button[type=submit]')
                    ->waitForText('Les exclusions que vous avez choisies ne permettent pas d\'attribuer une cible par participant.');
        });
    }
}
