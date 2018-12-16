<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class DearSantaDraw extends Model
{
    public static function cleanup()
    {
        self::where('expiration', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }
}
