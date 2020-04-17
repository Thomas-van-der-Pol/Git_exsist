<?php

namespace App\Libraries\Shared;

use Illuminate\Support\Facades\Request;

class AppUtils
{
    public static function isUrlActive($urls = [], $notUrls = [])
    {
        $result = false;

        foreach ($notUrls as $url)
        {
            $result = (
                Request::is($url) ||
                Request::is($url . '/*') ||
                Request::is('*/' . $url) ||
                Request::is('*/' . $url . '/*')
            );

            if ($result) {
                $result = false;
                return $result;
                break;
            }
        }

        foreach ($urls as $url)
        {
            $result = (
                Request::is($url) ||
                Request::is($url . '/*') ||
                Request::is('*/' . $url) ||
                Request::is('*/' . $url . '/*')
            );

            if ($result) {
                return $result;
                break;
            }
        }

        return $result;
    }
}