<?php

namespace App\Http\Controllers\Consumer\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use KJ\Core\controllers\BaseController;
use KJLocalization;

class ProfileController extends BaseController {

    protected $model = 'App\Models\Admin\CRM\Contact';
    protected $mainViewName = 'consumer.profile.main';

    protected function beforeIndex()
    {
        $item = Auth::guard('web')->user();

        $bindings = array(
            ['item', $item]
        );

        return $bindings;
    }

    public function save(Request $request)
    {
        $this->saveValidationMessages = [
            'USER_PASSWORD_NEW.regex' => KJLocalization::translate('Consumer - Profile', 'Error password format', 'Password must consist of 8 characters and at least 1 special character.')
        ];

        // Passwords doesn't match
        if (!(Hash::check($request->get('USER_PASSWORD'), Auth::guard('web')->user()->PASSWORD))) {
            return response()->json([
                'message' => KJLocalization::translate('Portal - My profile', 'Error password does not match', 'Your current password does not match with the specified password. Try again.'),
                'success'=> false
            ], 200);
        }

        // Current password and new password are same
        if(strcmp($request->get('USER_PASSWORD'), $request->get('USER_PASSWORD_NEW')) == 0){
            return response()->json([
                'message' => KJLocalization::translate('Consumer - Profile', 'Error password equal', 'Your new password can not be the same. Choose another password.'),
                'success'=> false
            ], 200);
        }

        $this->saveValidation = [
            'USER_PASSWORD' => 'required',
            'USER_PASSWORD_NEW' => 'required|string|min:8|regex:/(^(?=.*[#?!@$%^&*-])[0-9a-zA-Z#?!@$%^&*-]{8,})/u',
        ];

        // Validation
        if (!$this->validateSaveRequest($request, $response)) {
            return $response;
        }

        $this->saveValidation = [
            'USER_PASSWORD_NEW_CONFIRM' => 'required|same:USER_PASSWORD_NEW'
        ];

        $this->saveValidationMessages = [
            'USER_PASSWORD_NEW_CONFIRM.same' => KJLocalization::translate('Consumer - Profile', 'Error password confirmation', 'Your new password can not be confirmed. Try again.')
        ];

        // Validation
        if (!$this->validateSaveRequest($request, $response)) {
            return $response;
        }

        //Change Password
        $user = Auth::guard('web')->user();
        $user->PASSWORD = bcrypt($request->get('USER_PASSWORD_NEW'));
        $user->save();

        return response()->json([
            'success' => true,
            'new' => false
        ], 200);
    }

    public function setSwipeShowed()
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            $user->SWIPE_SHOWED = true;
            $user->save();
        }
    }
}