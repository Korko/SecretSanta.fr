<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class DearSantaDraw extends Model
{
    public static function cleanup()
    {
        self::where('expiration', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }
}
