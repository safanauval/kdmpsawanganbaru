<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    public static function get($key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}