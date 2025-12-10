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
     */
    public function selector(): string
    {
        return 'tr[dusk=participant'.$this->idx.']';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     */
    public function elements(): array
    {
        return [
            '@select-button' => '.multiselect__select',
            '@participant-field' => '.multiselect__option span',
        ];
    }

    /**
     * Select the given exclusion.
     */
    public function selectParticipant(Browser $browser, string $name): void
    {
        $browser->click('@select-button')
            ->clickLink($name, '@participant-field')
            ->click('@select-button');
    }
}
