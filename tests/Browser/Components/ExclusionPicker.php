<?php

namespace Tests\Browser\Components;

class ExclusionPicker extends BaseComponent
{
    protected $root;

    public function __construct($root = '')
    {
        $this->root = $root;
    }

    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return $this->root.' .multiselect';
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
