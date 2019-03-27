<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\MakroHelper;
use Illuminate\Http\Request;
use MakroSdk\Exceptions\SDKException;

class ForgetPasswordController extends BaseController
{

    public function forgetPassword()
    {
        $this->addBreadcrumb('members.forget.password', trans('frontend.forget_password'), route('members.forget-password.index'));
        return view('member.forget-password', []);
    }

    public function checkUsername(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        try {
            $makroSdk = app()->make('makroSdk');
            $param = [
                'username' => $request->input('username'),
                'confirm_url' => route('members.forget-password.reset-password')
            ];
            $response = $makroSdk->resetPassword()->requestResetPassword($param);
        } catch (\Exception $e) {
            $errors = [trans('frontend.could_not_send_password_reset_code')];

            if ($e instanceof SDKException) {
                if ($e->getErrorCode() == 200018) {
                    $errors = [
                        trans('frontend.username_is_required')
                    ];
                } else if ($e->getErrorCode() == 200404) {
                    $errors = [
                        trans('frontend.member_not_found')
                    ];
                } else if ($e->getErrorCode() == 200019) {
                    $errors = [
                        trans('frontend.could_not_send_password_reset_code')
                    ];
                }
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }


        if (!isset($response['data']['attributes']['send_via'])) {
            return redirect()->back()->withErrors([
                trans('frontend.could_not_send_password_reset_code')
            ])->withInput();
        }

        $successMessage = '';
        switch ($response['data']['attributes']['send_via']) {
            case 'email':
                $successMessage = trans('frontend.request_reset_password_via_email_success', ['username' => $request->input('username')]);
                break;
            case 'sms':
                $successMessage = trans('frontend.request_reset_password_via_sms_success', ['username' => $request->input('username')]);
                break;
        }


        return redirect(route('members.forget-password.reset-password', ['username' => $request->input('username')]))->with([
            'success_message' => $successMessage
        ]);
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'username' => 'required'
        ];

        $validator = app('validator')->make($request->input(), $rules);
        if ($validator->fails()) {
            abort(400, trans('frontend.username_is_required'));
        }

        //Check reset_code if exists
        if ($request->has('reset_code')) {
            try {
                $makroSdk = app()->make('makroSdk');
                $param = [
                    'username' => $request->input('username'),
                    'reset_code' => $request->input('reset_code'),
                ];
                $response = $makroSdk->resetPassword()->checkResetCode($param);
            } catch (\Exception $e) {
                $errors = [trans('frontend.reset_password_code_is_invalid')];

                if ($e instanceof SDKException) {
                    if ($e->getErrorCode() == 200020) {
                        $errors = [
                            trans('frontend.require_username_and_password_reset_code')
                        ];
                    } else if ($e->getErrorCode() == 200404) {
                        $errors = [
                            trans('frontend.member_not_found')
                        ];
                    } else if ($e->getErrorCode() == 200023) {
                        $errors = [
                            trans('frontend.reset_password_code_is_invalid')
                        ];
                    } else if ($e->getErrorCode() == 200024) {
                        $errors = [
                            trans('frontend.reset_password_code_was_expire')
                        ];
                    }
                }

                abort(400, head($errors));
            }
        }

        $this->addBreadcrumb('members.forget.password', trans('frontend.forget_password'), route('members.forget-password.index'));
        $this->addBreadcrumb('members.forget.reset-password', trans('frontend.reset_password'), route('members.forget-password.reset-password'));

        $data['success_message'] = $request->session()->get('success_message', null);
        $data['username'] = $request->input('username');

        $data['reset_code'] = $request->input('reset_code', '');

        return view('member.reset-password', $data);

    }

    public function resetPasswordPost(Request $request)
    {
        $rules = [
            'username' => 'required',
            'reset_code' => 'required',
            'password' => 'required|same:confirm_password|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_password' => 'required'
        ];

        $messages = [
            'password.regex' => __('frontend.password_requirement_2')
        ];

        $this->validate($request, $rules, $messages);

        try {
            $makroSdk = app()->make('makroSdk');
            $param = [
                'username' => $request->input('username'),
                'reset_code' => $request->input('reset_code'),
                'password' => MakroHelper::encryptPassword($request->input('password')),
            ];
            $response = $makroSdk->resetPassword()->resetPassword($param);
        } catch (\Exception $e) {
            $errors = [trans('frontend.could_not_reset_password')];

            if ($e instanceof SDKException) {
                if ($e->getErrorCode() == 200020) {
                    $errors = [
                        trans('frontend.require_username_and_password_reset_code')
                    ];
                } else if ($e->getErrorCode() == 200404) {
                    $errors = [
                        trans('frontend.member_not_found')
                    ];
                } else if ($e->getErrorCode() == 200023) {
                    $errors = [
                        trans('frontend.reset_password_code_is_invalid')
                    ];
                } else if ($e->getErrorCode() == 200024) {
                    $errors = [
                        trans('frontend.reset_password_code_was_expire')
                    ];
                }
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        if (!isset($response['data']['id'])) {
            return redirect()->back()->withErrors([
                trans('frontend.could_not_reset_password')
            ])->withInput();
        }


        $request->session()->flash('required_login', 1);
        $request->session()->flash('login_notify_success', trans('frontend.reset_password_success_please_login'));

        return redirect(route('home.index'));
    }

}