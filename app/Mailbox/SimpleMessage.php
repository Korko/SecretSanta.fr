<?php

namespace App\Mailbox;

use Illuminate\Support\Collection;

class SimpleMessage
{
    protected $delegate;
    protected $flags;

    public function __construct($delegate)
    {
        $this->delegate = $delegate;
        $this->flags = new Collection();
    }

    public function isUnseen()
    {
        return !$this->isSeen();
    }

    public function isSeen()
    {
        return ($this->getFlags()->get('seen') != null);
    }

    public function markUnseen()
    {
        $this->unsetFlag('seen');
    }

    public function markSeen()
    {
        $this->setFlag('seen');
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function setFlag($flag)
    {
        $this->getFlags()->put($flag, true);
    }

    public function unsetFlag($flag)
    {
        $this->getFlags()->forget($flag);
    }

    public function getDelegate()
    {
        return $this->delegate;
    }

    /**
     * Proxies all methods to the delegate.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->delegate, $method], $args);
    }
}