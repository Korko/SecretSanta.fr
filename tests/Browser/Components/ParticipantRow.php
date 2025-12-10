<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class ParticipantRow extends BaseComponent
{
    protected $idx;

    public function __construct($idx)
    {
        $this->idx = $idx;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'tr[dusk=participant'.$this->idx.']';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@select-button' => '.multiselect__select',
            '@participant-field' => '.multiselect__option span',
        ];
    }

    /**
     * Select the given exclusion.
     *
     * @param  Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function selectParticipant($browser, $name)
    {
        $browser->click('@select-button')
            ->clickLink($name, '@participant-field')
            ->click('@select-button');
    }
}
