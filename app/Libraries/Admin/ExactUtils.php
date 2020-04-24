<?php

namespace App\Libraries\Admin;

use App\Models\Core\Setting\SettingValue;
use Exception;
use Illuminate\Http\Request;
use Picqer\Financials\Exact\Connection;

class ExactUtils
{

    /**
     * Function to authorize with Exact, this redirects to Exact login prompt and retrieves authorization code
     * to set up requests for oAuth tokens
     */
    protected static function authorize()
    {
        $connection = new Connection();
        $connection->setRedirectUrl(config('exact.redirectUrl'));
        $connection->setExactClientId(config('exact.clientId'));
        $connection->setExactClientSecret(config('exact.clientSecret'));
        $connection->redirectForAuthorization();
    }

    /**
     * Callback function that sets values that expire and are refreshed by Connection.
     *
     * @param Connection $connection
     */
    public static function tokenUpdateCallback(Connection $connection) {
        // Save the new tokens for next connections
        SettingValue::setValue(config('setting.EXACT_ACCESS_TOKEN'), $connection->getAccessToken());
        SettingValue::setValue(config('setting.EXACT_REFRESH_TOKEN'), $connection->getRefreshToken());
    }

    /**
     * Function to connect to Exact, this creates the client and automatically retrieves oAuth tokens if needed
     *
     * @return \Picqer\Financials\Exact\Connection
     * @throws Exception
     */
    protected static function connect()
    {
        $connection = new Connection();
        $connection->setRedirectUrl(config('exact.redirectUrl'));
        $connection->setExactClientId(config('exact.clientId'));
        $connection->setExactClientSecret(config('exact.clientSecret'));

        // Retrieves authorizationcode from database
        $setting_value = SettingValue::getValue(config('setting.EXACT_AUTHORIZATION_CODE'));
        if ($setting_value) {
            $connection->setAuthorizationCode($setting_value);
        }

        // Retrieves accesstoken from database
        $setting_value = SettingValue::getValue(config('setting.EXACT_ACCESS_TOKEN'));
        if ($setting_value) {
            $connection->setAccessToken($setting_value);
        }

        // Retrieves refreshtoken from database
        $setting_value = SettingValue::getValue(config('setting.EXACT_REFRESH_TOKEN'));
        if ($setting_value) {
            $connection->setRefreshToken($setting_value);
        }

        // Retrieves expires timestamp from database
        $setting_value = SettingValue::getValue(config('setting.EXACT_EXPIRES_IN'));
        if ($setting_value) {
            $connection->setTokenExpires($setting_value);
        }

        // Set callback to save newly generated tokens
        $connection->setTokenUpdateCallback('App\Libraries\Admin\ExactUtils::tokenUpdateCallback');

        // Make the client connect and exchange tokens
        try {
            $connection->connect();
        } catch (\Exception $e) {
            throw new Exception('Could not connect to Exact: ' . $e->getMessage());
        }

        return $connection;
    }


    public static function authorizeOrConnect()
    {
        // Retrieves authorizationcode from database
        $setting_value = SettingValue::getValue(config('setting.EXACT_AUTHORIZATION_CODE'));
        if ($setting_value === null || $setting_value === '') {
            self::authorize();
        }

        // Create the Exact client
        return self::connect();
    }

    public static function redirect(Request $request)
    {
        $code = $request->get('code');

        if ($code !== null) {
            SettingValue::setValue(config('setting.EXACT_AUTHORIZATION_CODE'), $code);

            // Redirect naar boekhouding
            return redirect('/admin/accountancy');
        } else {
            return abort(404);
        }
    }

}