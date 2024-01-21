<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class RandomFormPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
            ->waitFor('#randomForm');
    }

    /**
     * Get the element shortcuts for the page.
     */
    public function elements(): array
    {
        return [
        ];
    }
}
