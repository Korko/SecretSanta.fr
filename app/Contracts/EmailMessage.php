<?php

namespace App\Contracts;

interface EmailMessage
{
    public function getTo() : array;
}