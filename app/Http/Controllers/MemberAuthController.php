<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;
use Illuminate\Http\Request;
use MakroSdk\Exceptions\SDKException;

class MemberAuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb('members.index', trans('frontend.member'), route('members.index'));
    }

    public function register(Request $request)
    {
        $slug = 'terms-conditions';
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->content()->findBySlug($slug, ['status' => 'published']);
            $data['terms_conditions'] = $response['data']['description'];
        } catch (\Exception $e) {
            $data['terms_conditions'] = '';
        }

        try {
            $response = $makroSdk->store()->getAvailableRegister(['limit' => 10000]);
            $data['available_register_stores'] = collect($response['data']);
            $data['available_register_stores'] = $data['available_register_stores']->map(function ($item) {
                return array_except($item, ['makro_store_id']);
            });
        } catch (\Exception $e) {
            $data['available_register_stores'] = [];
        }

        $data['facebook_error'] = $request->session()->get('facebook_error');
        $this->addBreadcrumb('members.register', trans('frontend.register'), route('members.register'));

        return view('member.register', $data);
    }

    public function registerPost(Request $request)
    {
        $rule = [
            'username' => 'required',
            'password' => 'required|same:confirm_password',
            'makro_card_id' => 'required_without:register_store_id',
            'register_store_id' => 'required_without:makro_card_id'
        ];

        if (!$request->session()->has('by_pass_recaptcha')) {
            $rule['g-recaptcha-response'] = 'required|captcha';
        }

        $validator = \Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $request->session()->flash('by_pass_recaptcha', true);

        return $this->sendRegister($request);
    }

    protected function sendRegister(Request $request)
    {
        $response = null;

        $parameters = $request->only(['username', 'password', 'register_store_id', 'makro_card_id', 'facebook_id']);

        if (! empty($parameters['username']) && str_is('*@*', $parameters['username'])) {
            try {
                $parameters['username'] = MakroHelper::validateEmail($parameters['username']);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.please_enter_valid_email_format'),
                    'errors' => [
                        __('frontend.please_enter_valid_email_format')
                    ]
                ]);
            }
        }

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->create($parameters);
        } catch (\Exception $e) {
            $errors = [$e->getMessage()];

            if (preg_match('/members_facebook_id_unique/', $e->getMessage())) {
                $errors['facebook_id'] = [
                    __('frontend.this_facebook_account_has_been_used')
                ];
            }


            if ($e instanceof SDKException) {
                $errorMessage = $this->getRegisterErrorMessage($e);
                if (!empty($errorMessage)) {
                    $message = $errorMessage;
                }

                $errors = $e->getErrors();
            }

            return response()->json([
                'status' => 'error',
                'message' => empty($message) ? '' : $message,
                'errors' => $errors,
            ]);
        }

        \Event::fire(new \App\Events\MemberWasCreated($response['data']['attributes']));

        // Check activation
        if (array_get($response, 'data.attributes.is_activate')) {
            $request->session()->flash('register_success', true);
        } else {
            if (array_get($response, 'data.attributes.activation.type') == 'otp') {
                $request->session()->put('member_activate_info', [
                    'otp_ref' => array_get($response, 'data.attributes.activation.ref'),
                    'member_id' => array_get($response, 'data.attributes.id'),
                    'username' => array_get($response, 'data.attributes.username')
                ]);
            } else {
                $request->session()->flash('register_waiting_activate_email', true);
            }
        }

        if ($request->has('facebook_id')) {
            try {
                $response = $makroSdk->member()->facebookLogin($request->input('facebook_id'));
                AuthHelper::login($response['data']);

                $this->reCreateCart();
                $request->session()->flash('register_facebook_success', 1);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $errors = [$message];

                if ($e instanceof SDKException) {
                    $errorMessage = $this->getRegisterErrorMessage($e);
                    if (!empty($errorMessage)) {
                        $message = $errorMessage;
                    }

                    $errors = $e->getErrors();
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'errors' => $errors,
                ]);
            }
        }

        if (!empty($response['data']['attributes']['activation_code'])) {
            $response['data']['attributes']['activation_code'] = null;
            unset($response['data']['attributes']['activation_code']);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Successful',
            'data' => $response['data']
        ]);
    }

    protected function getRegisterErrorMessage(\Exception $e)
    {
        $errorMessage = '';
        $errorCode = $e->getErrorCode();

        switch ($errorCode) {
            case 201003:
                $errorMessage = trans('frontend.login_error_member_not_activate_message', ['resend_url' => route('members.register.activate.resend.get')]);
                break;
            case 201004:
                $errorMessage = trans('frontend.login_error_member_was_locked');
                break;
            case 200001:
                $errorMessage = trans('frontend.register_error_create_database_error');
                break;
            case 200002:
                $errorMessage = trans('frontend.register_error_member_create_ms_error');
                break;
            case 200003:
                $errorMessage = trans('frontend.register_error_member_create_validate_error');
                break;
            case 219404:
                $errorMessage = trans('frontend.register_error_member_makro_card_not_found');
                break;
            case 219405:
                $errorMessage = trans('frontend.register_error_member_makro_card_not_active');
                break;
            default:
                $errorMessage = trans('frontend.please_contact_call_center_sorry_for_your_inconvenience');
                break;
        }

        if($errorCode) {
            return $errorMessage . " ({$errorCode})";
        }

        return $errorMessage;

    }

    public function reCreateCart()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $parameter = [
                'store_id' => StoreHelper::getCurrentStore(),
                'session_id' => AuthHelper::tempMemberId()
            ];
            $response = $makroSdk->cart()->reCreateCart($parameter);

        } catch (\Exception $e) {
            //It's background process
            $message = $e->getMessage();

            if ($e instanceof SDKException) {
                $message = $e->getUserMessage();
            }
        }

        $user = AuthHelper::user();
        return $user;
    }

    public function registerFacebookPost(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'makro_card_id' => 'required_without:register_store_id',
            'register_store_id' => 'required_without:makro_card_id'
        ]);

        return $this->sendRegister($request);
    }

    public function registerFacebook(Request $request)
    {
        if (!$request->session()->has('facebook_user_data')) {
            //abort(400, __('frontend.error_invalid_access_facebook_register'));
            return redirect(route('members.register'))->with('facebook_error', __('frontend.error_invalid_access_facebook_register'));
        }

        $user = $request->session()->get('facebook_user_data');

//        $user = new \stdClass();
//        $user->id = 1234567895333;
//        $user->email = null;

        $data['facebook_user_id'] = $user->id;
        $data['facebook_user_email'] = $user->email;
        $data['rtt'] = urlencode(isset($user->rtt) ? $user->rtt : route('home.index'));
        $data['available'] = false;
        $data['username_not_available'] = false;

        if (!empty($data['facebook_user_email'])) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->member()->isUsernameExists($user->email);

                if (!$response['data']['attributes']['exists']) {
                    $data['available'] = true;
                } else {
                    $data['username_not_available'] = true;
                }
            } catch (\Exception $e) {

            }
        }

        $slug = 'terms-conditions';

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->content()->findBySlug($slug, ['status' => 'published']);
            $data['terms_conditions'] = $response['data']['description'];
        } catch (\Exception $e) {
            $data['terms_conditions'] = '';
        }

        try {
            $response = $makroSdk->store()->getAvailableRegister(['limit' => 10000]);
            $data['available_register_stores'] = collect($response['data']);
            $data['available_register_stores'] = $data['available_register_stores']->map(function ($item) {
                return array_except($item, ['makro_store_id']);
            });
        } catch (\Exception $e) {
            $data['available_register_stores'] = [];
        }

        $this->addBreadcrumb('members.register.facebook', trans('frontend.register'), route('members.register.facebook'));

        return view('member.register-facebook', $data);
    }

    public function registerSuccess(Request $request)
    {
        // if (! $request->session()->has('register_success')) {
        //     return redirect()->route('home.index');
        // }

        $this->addBreadcrumb('members.register.success', trans('frontend.register_success'), route('members.register.success'));
        return view('member.register-success', []);
    }

    public function activateEmailWaiting(Request $request)
    {
        // if (! $request->session()->has('register_waiting_activate_email')) {
        //      return redirect()->route('home.index');
        // }

        $this->addBreadcrumb('members.register.email.activate.waiting', trans('frontend.register_activate_email_waiting'), route('members.register.email.activate.waiting'));
        return view('member.activate-email-waiting', []);
    }

    public function activateEmail(Request $request)
    {
        $this->addBreadcrumb('members.register.email.activate', trans('frontend.register_activate_email'), route('members.register.email.activate'));

        $rules = [
            'activation_id' => [
                'required'
            ],
            'activation_code' => [
                'required'
            ]
        ];

        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails()) {
            return view('member.activate-email-fail', []);
        }

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->activateEmail($request->get('activation_id'), $request->get('activation_code'), true);
        } catch (\Exception $e) {

            if ($e instanceof SDKException) {
                return view('member.activate-email-fail', ['errorCode' => $e->getErrorCode()]);
            }

            return view('member.activate-email-fail', []);
        }

        if (array_get($response, 'data.is_activate') != true) {
            return view('member.activate-email-fail', []);
        }

        // Force login
        if (!empty($response['data']['token_id'])) {
            AuthHelper::login($response['data']);
            $this->reCreateCart();
        }

        $request->session()->flash('register_activate_success', true);
        return redirect()->route('members.register.activate-email.success');
    }

    public function activateOtpGet(Request $request)
    {
        $memberActivateInfo = $request->session()->get('member_activate_info');

        if (empty($memberActivateInfo)) {
            return redirect()->route('home.index');
        }

        $this->addBreadcrumb('members.register.activate.otp.get', trans('frontend.register_activate_phone'), route('members.register.activate.otp.get'));

        return view('member.activate-otp', $memberActivateInfo);
    }

    public function activateOtpPost(Request $request)
    {
        $rules = [
            'otp' => [
                'required'
            ]
        ];

        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $memberActivateInfo = $request->session()->get('member_activate_info');
        if (empty($memberActivateInfo)) {
            return redirect()->back()->withErrors([trans('frontend.not_found_member_otp_activate_info')]);
        }

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->activatePhone($memberActivateInfo['member_id'], $memberActivateInfo['otp_ref'], $request->get('otp'), true);
        } catch (\Exception $e) {
            $errors = [$e->getMessage()];

            if ($e instanceof SDKException) {
                if ($e->getErrorCode() == 200004) { // Validator
                    $errors = $e->getErrors();
                } elseif ($e->getErrorCode() == 200404) { // Member not found
                    $errors = [__('frontend.member_not_found')];
                } elseif ($e->getErrorCode() == 212000) { // NOTIFICATION_OTP_ERROR
                    $errors = [__('frontend.otp_service_error')];
                } elseif ($e->getErrorCode() == 212004) { // OTP is invalid
                    $errors = [__('frontend.otp_is_invalid')];
                } elseif ($e->getErrorCode() == 212002) { // OTP was verified
                    $errors = [__('frontend.otp_was_verified')];
                } elseif ($e->getErrorCode() == 212003) { // OTP was expired
                    $errors = [__('frontend.otp_was_expired')];
                } elseif ($e->getErrorCode() == 212001) { // OTP Ref. not found
                    $errors = [__('frontend.otp_ref_is_invalid')];
                } else {
                    $errors = [__('frontend.otp_service_error')];
                }
            }

            return redirect()->back()->withErrors($errors);
        }

        if (array_get($response, 'data.is_activate') != true) {
            return redirect()->back()->withErrors([trans('frontend.can_not_activate_member')]);
        }

        // Force login
        if (!empty($response['data']['token_id'])) {
            AuthHelper::login($response['data']);
            $this->reCreateCart();
        }

        $request->session()->forget('member_activate_info');
        $request->session()->flash('register_activate_success', true);
        return redirect()->route('members.register.activate.success');
    }

    public function activateResendGet(Request $request)
    {
        $this->addBreadcrumb('members.register.activate.resend.get', trans('frontend.register_activate_resend'), route('members.register.activate.resend.get'));

        return view('member.activate-resent', []);
    }

    public function activateResendPost(Request $request)
    {
        $rules = [
            'username' => [
                'required'
            ]
        ];

        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->activateResend($request->get('username'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getErrors())->withInput();
        }

        if (array_get($response, 'is_activate') == true) {
            $request->session()->flash('register_activate_success', true);
            return redirect()->route('members.register.success');
        }

        if (array_get($response, 'data.attributes.activation.type') == 'otp') {
            $request->session()->put('member_activate_info', [
                'otp_ref' => array_get($response, 'data.attributes.activation.ref'),
                'member_id' => array_get($response, 'data.attributes.id'),
                'username' => array_get($response, 'data.attributes.username')
            ]);

            return redirect()->route('members.register.activate.otp.get');
        } else {
            $request->session()->flash('register_waiting_activate_email', true);
            return redirect()->route('members.register.email.activate.waiting');
        }
    }

    public function activateSuccess(Request $request)
    {
        // if (! $request->session()->has('register_activate_success')) {
        //     return redirect()->route('home.index');
        // }

        $this->addBreadcrumb('members.register.activate.success', trans('frontend.register_activate_success'), route('members.register.activate.success'));
        return view('member.activate-success', []);
    }

    public function verifyUsername(Request $request)
    {
        $username = $request->input('username');

        if (str_is('*@*', $username)) {
            try {
                MakroHelper::validateEmail($username);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.please_enter_valid_email_format'),
                    'show_inline' => 1,
                    'errors' => []
                ]);
            }
        }

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->isUsernameExists($username);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.cloud_not_verify_username') . '<br />' . __('frontend.please_try_again'),
                'errors' => $e->getErrors()
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'ok',
            'data' => [
                $response['data']['attributes']
            ]
        ]);
    }

    public function facebookLogin(Request $request)
    {
        $returnTo = urldecode($request->get('rtt', route('home.index')));
        $request->session()->put('facebookLoginReturnUrl', $returnTo);

        if (\Route::has('members.facebook-callback')) {
            config(['services.facebook.redirect' => preg_replace('/en\/|th\//', '', route('members.facebook-callback'))]);
        }

        return \Socialite::with('facebook')->scopes([
            'email'
        ])->redirect();
    }

    public function facebookCallback(Request $request)
    {
        $redirectUrl = urldecode($request->session()->get('facebookLoginReturnUrl', route('home.index')));

        if (\Route::has('members.facebook-callback')) {
            config(['services.facebook.redirect' => preg_replace('/en\/|th\//', '', route('members.facebook-callback'))]);
        }

        $user = null;
        try {
            $makroSdk = app()->make('makroSdk');
            $user = \Socialite::driver('facebook')->user();

            //Check facebook id is already exist
            try {
                $response = $makroSdk->member()->isFacebookUserIdAlreadyExists($user->id);
                if ($response['data']['attributes']['exists'] == true) {
                    //Auto Login
                    try {
                        $current_cart['data'] = [];
                        try {
                            $current_cart = $makroSdk->cart()->get(StoreHelper::getCurrentStore(), AuthHelper::tempMemberId(), [], 'content.price');
                        } catch (\Exception $e) {
                            //Do nothing
                        }
                        $response = $makroSdk->member()->facebookLogin($user->id);
                        AuthHelper::login($response['data']);

                        $request->session()->forget('facebookLoginReturnUrl');

                        $this->manageAfterLogin($current_cart, $response['data']);

                        if (isset($response['data'])) {
                            return redirect($redirectUrl)->with('login_success', __('frontend.login_success'));
                        } else {
                            return redirect(route('members.register'))->with('facebook_error', __('frontend.could_not_get_facebook_user_data'));
                        }
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $errors = [$message];

                        if ($e instanceof SDKException) {
                            if ($e->getErrorCode() == 201003) {
                                $message = trans('frontend.login_error_member_not_activate_message', ['resend_url' => route('members.register.activate.resend.get')]);
                            } else if ($e->getErrorCode() == 201004) {
                                $message = trans('frontend.login_error_member_was_locked');
                            } else {
                                $message = $e->getUserMessage();
                            }

                            $errors = $e->getErrors();
                        }

                        return redirect(route('members.register'))->with('facebook_error', $message)->withErrors($errors);

                    }
                }
            } catch (\Exception $e) {
                return redirect(route('members.register'))->with('facebook_error', __('frontend.could_not_get_facebook_user_data'));
            }
        } catch (\Exception $e) {
            return redirect(route('members.register'))->with('facebook_error', __('frontend.could_not_get_facebook_user_data'));
        }

        $user->rtt = $redirectUrl;

        //Go to facebook register form
        return redirect(route('members.register.facebook'))->with('facebook_user_data', $user);
    }

    protected function manageAfterLogin($current_cart, $memberData)
    {
        if (!empty($current_cart['data'])) {
            $this->reCreateCart();
        }

        // Update accept_delivery_service to member
        if (MakroHelper::isAcceptedDelivery()) {
            $makroSdk = app()->make('makroSdk');
            $makroSdk->member()->updateProfile([
                'accept_delivery_service' => 'Y'
            ]);
        }
    }

    public function postLogin()
    {
// FWG 
     $username = request()->get('username');
     $password = request()->get('password');
     if($username=='admin'&&$password=='admin'){
            return redirect()->route('home.site-map')->with('message', 'The Message');
     }else
     {
// End 
        $current_cart['data'] = [];
        try {
            $username = request()->get('username');
            $password = request()->get('password');

        

            if (empty($username) or empty($password)) {
                throw new \Exception(__('frontend.required_username_and_password', ['username' => $username, 'password' => $password]));
            }

            $makroSdk = app()->make('makroSdk');

            try {
                $current_cart = $makroSdk->cart()->get(StoreHelper::getCurrentStore(), AuthHelper::tempMemberId(), [], 'content.price');
            } catch (\Exception $e) {
                //Do nothing
            }
            $params = [
                'username' => $username,
                'password' => $password
            ];
            $member = $makroSdk->member()->login($params);

            // Set Session Data
            $rememberMe = false;
            if (request()->get('remember_me', 0) == 1) {
                $rememberMe = true;
            }

            AuthHelper::login($member['data'], $rememberMe);

            //session()->put('makroclickMember', $member['data']);
            session()->flash('login_success', true);

        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e instanceof SDKException) {
                if ($e->getErrorCode() == 201003) {
                    $message = trans('frontend.login_error_member_not_activate_message', ['resend_url' => route('members.register.activate.resend.get')]);
                } else if ($e->getErrorCode() == 201004) {
                    $message = trans('frontend.login_error_member_was_locked');
                } else {
                    $message = $e->getDeveloperMessage();
                }
            }

            // if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'errors' => $e->getErrors(),
                ]);
            // }

            // Return with error
            // return redirect()->route('home.index')->withErrors([$message]);

        }
        $this->manageAfterLogin($current_cart, $member['data']);

        // if request is ajax, return json
        // if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $member['data'],
                'redirect_url' => urldecode(request()->input('redirect_url')),
                'user_menu' => view('partials.user_menu')->render(),
                'user_menu_mobile' => view('partials.user_menu_mobile')->render()
            ]);
        // }

        // Return with success
        // return redirect()->route('home.index');
// FWG
     }
// End
    }

    public function anyLogout()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $makroSdk->member()->logout();
            AuthHelper::logout();
        } catch (\Exception $e) {
            // if request is ajax, return json (error)
            $message = $e->getMessage();

            if ($e instanceof SDKException) {
                $message = $e->getUserMessage();
            }

            if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }

            // s($e->getMessage());
            // die();

            // Return with error
            return redirect()->route('home.index');
        }

        // if request is ajax, return json
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        // Return with success
        return redirect()->route('home.index');
    }

    public function registerFacebookSuccess(Request $request)
    {
        // if (! $request->session()->has('register_facebook_success')) {
        //      abort(400, __('frontend.invalid_access'));
        // }

        $this->addBreadcrumb('members.register.success', trans('frontend.register_success'), route('members.register.success'));
        return view('member.register-facebook-success');
    }

    // public function logout($memberId)
    // {
    //     $response = null;
    //     try {
    //         $makroSdk = app()->make('makroSdk');
    //         $response = $makroSdk->member()->logout($memberId);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ]);
    //     }

    //     return redirect()->back()->with('message', 'Logout completed!');
    // }

    // public function isLogined($memberId)
    // {
    //     $response = null;
    //     try {
    //         $makroSdk = app()->make('makroSdk');
    //         $response = $makroSdk->member()->isLogined($memberId);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ]);
    //     }

    //     if($response['message'] == 'true')
    //     {
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
}