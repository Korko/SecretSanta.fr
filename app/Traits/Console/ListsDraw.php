<?php

namespace App\Traits\Console;

use App\Models\Draw;
use Arr;

trait ListsDraw
{
    public function displayDraw(Draw $draw): void
    {
        $this->table(
            ['ULID', 'Name', 'Email', 'Status'],
            $draw->participants()
                ->with('mail')
                ->get()
                ->map(function ($col) {
                    return
                        Arr::only($col->toArray(), ['ulid', 'name', 'email']) +
                        ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')];
                })
                ->toArray()
        );
    }
}
