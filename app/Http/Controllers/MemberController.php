<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\CartHelper;
use App\Bootstrap\Helpers\MakroHelper;
use Breadcrumbs;
use Illuminate\Http\Request;
use MakroSdk\Exceptions\SDKException;

class MemberController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb('members.index', trans('frontend.member'), route('members.index'));
    }

    public function index()
    {
        return redirect(route('members.profile'));
    }

    public function profile()
    {
        $profileData = [];
        $response = [];
        try {
            $user = AuthHelper::user();
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->profile();
            AuthHelper::updateUserFromProfile($response);

            $data = [
                'address_type' => 'member'
            ];
            $memberAddress = $makroSdk->memberAddress()->getMemberAddresses($data);
            $provinces = $makroSdk->address()->getProvinces();

            if (!empty($memberAddress['data'])) {
                $provine_id = $this->getProvinceId($provinces['data'], $memberAddress['data'][0]['province']);
                $districts = $makroSdk->address()->getDistricts(['province_id' => $provine_id]);
                $district_id = $this->getDistrictId($districts['data'], $memberAddress['data'][0]['district']);
                $sub_districts = $makroSdk->address()->getSubDistricts(['district_id' => $district_id]);
            }

        } catch (\Exception $e) {
            $provinces = $makroSdk->address()->getProvinces();
        }
        $provinces = collect(array_values(collect($provinces['data'])->sortBy('name')->toArray()));
        $filter = $provinces->filter(function ($item, $key) {
            return $item['province_id'] == 1;
        });
        $key = $filter->keys()->first();
        $bankkok = $provinces->splice($key, 1);
        $provinces->prepend($bankkok->first());
        $profileData = [
            'profile' => $response,
            'address' => empty($memberAddress['data']) ? [] : $memberAddress['data'][0],
            'provinces' => empty($provinces) ? [] : $provinces->toArray(),
            'districts' => empty($districts['data']) ? [] : collect($districts['data'])->sortBy('name')->toArray(),
            'subdistricts' => empty($sub_districts['data']) ? [] : collect($sub_districts['data'])->sortBy('name')->toArray()
        ];

        $this->addBreadcrumb('members.profile', trans('frontend.my_account'), route('members.profile'));
        return view('member.profile', $profileData);
    }

    public function removeChar($data)
    {
        $str = preg_replace('/\'/', '', json_encode($data, true));
        $str = preg_replace('/&quot;/', '', $str);

        return json_decode($str);
    }

    function getProvinceId($provinces, $name)
    {
        if (!empty($provinces)) {
            foreach ($provinces as $province) {
                if ($province['name'] == $name) {
                    return $province['province_id'];
                }
            }
        }
    }

    function getDistrictId($districts, $name)
    {
        if (!empty($districts)) {
            foreach ($districts as $district) {
                if ($district['name'] == $name) {
                    return $district['district_id'];
                }
            }
        }

    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|max:10|min:10',
            'birth_day' => 'required',
            'email' => 'nullable|email',

            'address' => 'required|max:45',
            'subdistrict_id' => 'required',
            'district_id' => 'required',
            'province_id' => 'required',
            'postcode' => 'required',
        ], [], [
            'first_name' => trans('frontend.first_name'),
            'last_name' => trans('frontend.last_name'),
            'phone' => trans('frontend.phone'),
            'birth_day' => trans('frontend.birth_day'),
            'email' => trans('frontend.email'),

            'address' => trans('frontend.address'),
            'subdistrict_id' => trans('frontend.subdistrict'),
            'district_id' => trans('frontend.district'),
            'province_id' => trans('frontend.province'),
            'postcode' => trans('frontend.postcode'),
        ]);

        $makroSdk = app()->make('makroSdk');

        //Update member profile
        $member = [
            'birth_day' => $request->get('birth_day'),
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'phone' => $request->get('phone'),
            'email' => empty($request->get('email')) ? '' : $request->get('email'),
        ];

        if (! empty($member['email'])) {
            try {
                $member['email'] = MakroHelper::validateEmail($member['email']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors([
                    __('frontend.this_email_address_is_invalid', ['email' => $member['email']])
                ]);
            }
        }

        try {
            $response = $makroSdk->member()->updateProfile($member);
        } catch (\Exception $e) {
            $errors = [$e->getMessage()];

            if ($e instanceof SDKException) {
                $errors = [$e->getUserMessage()];

                if (!empty($e->getErrors())) {
                    $errors = array_merge($errors, $e->getErrors());
                }
            }

            return redirect()->back()->withInput()->withErrors($errors);
        }

        //Update member address (type = member)
        try {
            $data = [
                'address_name' => empty($request->get('shop_name', '')) ? '' : $request->get('shop_name', ''),
                // 'first_name'     => $request->get('first_name'),
                // 'last_name'      => $request->get('last_name'),
                //                'tax_id'         => $request->get('tax_id'),
                // 'contact_phone'  => $request->get('phone'),
                // 'contact_email'  => empty($request->get('email'))?'':$request->get('email'),
                'address_type' => 'member', // (member|business|bill|shipping)
                'address' => $request->get('address'),
                'address2' => $request->get('address2', '-'),
                'address3' => $request->get('address3', '-'),
                'subdistrict_id' => $request->get('subdistrict_id'),
                'district_id' => $request->get('district_id'),
                'province_id' => $request->get('province_id'),
                'country_code' => 'TH',
                'postcode' => $request->get('postcode'),
            ];
            $memberAddress = $makroSdk->memberAddress()->getMemberAddresses(['address_type' => 'member']);
            if (empty($memberAddress['data'])) {
                $rs = $makroSdk->memberAddress()->createMemberAddress($data);
            } else {
                $memberAddress = head($memberAddress['data']);
                $rs = $makroSdk->memberAddress()->updateMemberAddress($memberAddress['id'], $data);
            }
        } catch (\Exception $e) {
            $errors = [$e->getMessage()];
            if (!empty($e->getErrors())) {
                $errors = array_merge($errors, $e->getErrors());
            }

            return redirect()->back()->withInput()->withErrors($errors);
        }

        //Update makro card id
        if ($request->has('makro_card_id') && !empty($request->get('makro_card_id')) && $request->get('makro_card_id') != $request->get('old_makro_card_id')) {
            try {
                $response = $makroSdk->member()->updateMakroCardId($request->get('makro_card_id'));
            } catch (\Exception $e) {
                $errors = [$e->getMessage()];

                if ($e instanceof SDKException) {
                    $errors = [$e->getUserMessage()];

                    if (!empty($e->getErrors())) {
                        $errors = array_merge($errors, $e->getErrors());
                    }
                }

                return redirect()->back()->withInput()->withErrors($errors);
            }
        }
        if (!empty($rs['status']['code'])) {
            if ($rs['status'] != 200) {
                return redirect()->back()->withInput()->withErrors($rs['errors']['message']);
            }
        }

        $alerts = [
            'success' => [
                'messages' => [
                    trans('frontend.update_profile_success')
                ]
            ]
        ];

        return redirect()->route('members.profile')->withAlerts($alerts);

    }


    public function wishList()
    {
        $response = [];
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->favorite()->get([
                'includes' => 'detail',
                'page' => request()->get('page', 1),
                'per_page' => env('PRODUCT_LIST_DISPLAY_PER_PAGE')
            ]);
            $wishlists = collect($response['data'])->pluck('content');
        } catch (\Exception $e) {
            $wishlists = collect([]);
        }
        $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
        $total = array_get($response, 'meta.pagination.total', 0);
        $perPage = env('PRODUCT_LIST_DISPLAY_PER_PAGE');
        $currentPage = request()->get('page', 1);
        $currentItems = $wishlists;
        $data['wishlists'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage,
            $currentPage, $options);

        $this->addBreadcrumb('members.profile', trans('frontend.my_wishlist'), route('members.wishlist'));
        return view('member.wish-list', $data);
    }

    public function shipping()
    {
        $shippingAddresses = [];

        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'address_type' => 'shipping'
            ];
            $rs = $makroSdk->memberAddress()->getMemberAddresses($data);

            $data = [
                'address_type' => 'shipping'
            ];
            $shippingAddresses = $makroSdk->memberAddress()->getMemberAddresses($data);

        } catch (\Exception $e) {
            $rs['data'] = [];
        }

        $response['shipping_addresses'] = isset($shippingAddresses['data']) ? $shippingAddresses['data'] : [];
        $response['shipping'] = $rs['data'];
        $this->addBreadcrumb('members.shipping', trans('frontend.my_shipping'), route('members.shipping'));

        return view('member.shipping', $response);
    }

    public function postShippingAddress(Request $request)
    {
        try {
            $this->validate($request, [
                // 'shop_name' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                // 'tax_id_number' => 'required',
                'phone' => 'required|max:10|min:10',
                'email' => empty($request['requiredEmail']) ? 'required|email' : 'nullable|email',
                'address' => 'required|max:45',
//                'address2' => 'required',
//                'address3' => 'required',
                'subdistrict_id' => 'required',
                'district_id' => 'required',
                'province_id' => 'required',
                'postcode' => 'required',
            ]);

            $data = [
                'address_name' => $request->get('address_name'),
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                // 'tax_id_number'  => $request['tax_id_number'],
                'contact_phone' => $request['phone'],
                'contact_email' => empty($request['email']) ? '' : $request['email'],
                'address_type' => $request->input('address_type', 'shipping'), // (member|business|bill|shipping)
                'address' => $request['address'],
                'address2' => $request['address2'],
                'address3' => '-',//$request['address3'],
                'subdistrict_id' => $request['subdistrict_id'],
                'district_id' => $request['district_id'],
                'province_id' => $request['province_id'],
                'country_code' => 'TH',
                'postcode' => $request['postcode'],
            ];
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

        try {
            if (! empty($data['contact_email'])) {
                $data['contact_email'] = MakroHelper::validateEmail($data['contact_email']);
            }

            $makroSdk = app()->make('makroSdk');
            if (isset($request['id']) && !empty($request['id'])) {
                if (empty($data['address_name'])) {
                    $data['address_name'] = ''; // Fixed bug ลบชื่อร้านค้าไม่ได้
                }

                $rs = $makroSdk->memberAddress()->updateMemberAddress($request['id'], $data);
            } else {
                $rs = $makroSdk->memberAddress()->createMemberAddress($data);
            }
            $params = [
                'address_type' => $request->input('address_type', 'shipping')
            ];

            $shipping = $makroSdk->memberAddress()->getMemberAddresses($params);
            if ($request->input('address_type') == 'member') {
                $member = [
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'phone' => $request->get('phone'),
                    'email' => empty($request->get('email')) ? '' : $request->get('email'),
                ];
                $makroSdk->member()->updateProfile($member);
                $profile = $makroSdk->member()->profile();
                if (!empty($profile)) {
                    AuthHelper::updateUserFromProfile($profile);
                    $data['profile'] = $profile;
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                $message = $e->getUserMessage();
            } else {
                $message = $e->getMessage();
            }

            $response = [
                'message' => $message,
                'error_code' => $e->getCode()
            ];

            if ($e->getCode() == 100003) {
                $response['message'] = __('frontend.please_enter_valid_email_format');
            }

            return response()->json($response);
        }

        $data['shipping'] = $shipping['data'];
        $data['data'] = $rs;
        $data['status'] = 'ok';
        $data['message'] = 'Successful';

        return response()->json($data);
    }

    public function getShippingAddresses()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'address_type' => 'shipping',
                'per_page' => 99999
            ];
            $rs = $makroSdk->memberAddress()->getMemberAddresses($data);

        } catch (\Exception $e) {

        }

        return response()->json([
            'items' => $rs['data']
        ]);
    }

    public function getMemberAddresses()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'address_type' => 'shipping'
            ];
            $rs = $makroSdk->memberAddress()->getMemberAddresses($data);

        } catch (\Exception $e) {

        }

        return response()->json([$rs['data']]);
    }

    public function removeShipping(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');

            $data = [
                'address_type' => 'shipping'
            ];
            $rs['data'] = $makroSdk->memberAddress()->deleteMemberAddress($request['id']);
            $shipping = $makroSdk->memberAddress()->getMemberAddresses($data);
            $rs['data']['shipping'] = $shipping['data'];

        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'error_code' => $e->getCode(),
                    'message' => $e->getMessage()
                ]
            ]);
        }

        return response()->json($rs['data']);
    }

    protected function getMakroCardInfo($makroCardId)
    {
        $makroSdk = app()->make('makroSdk');

        try {
            $response = $makroSdk->member()->getMakroCardInfo($makroCardId, ['includes' => 'member']);
        } catch (\Exception $e) {
            throw $e;
        }

        $makroCardInfo = null;
        $hasMakroCard = false;


        if ((isset($response['data']) && !empty($response['data']))
        ) {
            $hasMakroCard = true;
            if ((isset($response['data']['tax_address']) && !empty($response['data']['tax_address']))
                && (isset($response['data']['billing_address']) && !empty($response['data']['billing_address']))) {
                $makroCardInfo = $response['data'];
            }
        }

        return [
            'has_makro_card' => $hasMakroCard,
            'makro_card_info' => $makroCardInfo
        ];
    }

    public function taxAddress()
    {
        $profileData = [];
        $response = [];
        $is_use_tax_adddress = false;
        $profileData['is_use_tax'] = $is_use_tax_adddress;

        try {
            $makroSdk = app()->make('makroSdk');
            $provinces = $makroSdk->address()->getProvinces();
            $profileData['provinces'] = collect($provinces['data'])->sortBy('name');
        } catch (\Exception $e) {
            $profileData['provinces'] = [];
        }

        try {
            $user = AuthHelper::user();
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->profile();
            AuthHelper::updateUserFromProfile($response);
            $data = [
                'address_type' => 'tax'
            ];
            $taxAddress = [];
            $billAddress = [];

            $makroCardId = $response['makro_card_id'];

            $makroCardInfo = $this->getMakroCardInfo($makroCardId);
            if (!empty($makroCardInfo['makro_card_info']['tax_address'])) {
                $taxAddress['data'][0] = $makroCardInfo['makro_card_info']['tax_address'];
            }

            if (!empty($makroCardInfo['makro_card_info']['billing_address'])) {
                $billAddress['data'][0] = $makroCardInfo['makro_card_info']['billing_address'];
            }

            $provinces = $makroSdk->address()->getProvinces();
            if (!empty($taxAddress['data'])) {
                $provine_id = $this->getProvinceId($provinces['data'], $taxAddress['data'][0]['province']);
                $taxdistricts = $makroSdk->address()->getDistricts(['province_id' => $provine_id]);
                $district_id = $this->getDistrictId($taxdistricts['data'], $taxAddress['data'][0]['district']);
                $taxsub_districts = $makroSdk->address()->getSubDistricts(['district_id' => $district_id]);
            }

            if (!empty($billAddress['data'])) {
                $provine_id = $this->getProvinceId($provinces['data'], $billAddress['data'][0]['province']);
                $billdistricts = $makroSdk->address()->getDistricts(['province_id' => $provine_id]);
                $district_id = $this->getDistrictId($billdistricts['data'], $billAddress['data'][0]['district']);
                $billsub_districts = $makroSdk->address()->getSubDistricts(['district_id' => $district_id]);
            }

            if (!empty($billAddress['data']) && !empty($taxAddress['data'])) {
                $fields = ['address', 'address2', 'province', 'district', 'subdistrict', 'postcode'];
                $is_use_tax_adddress = $this->compareAddress($taxAddress['data'][0], $billAddress['data'][0], $fields);
            }

            $provinces = collect(array_values(collect($provinces['data'])->sortBy('name')->toArray()));
            $filter = $provinces->filter(function ($item, $key) {
                return $item['province_id'] == 1;
            });

            $key = $filter->keys()->first();
            $bankkok = $provinces->splice($key, 1);
            $provinces->prepend($bankkok->first());
            $profileData = [
                'makroCardInfo' => array_get($makroCardInfo, 'makro_card_info', []),
                'profile' => $response,
                'billAddress' => empty($billAddress['data'][0]) ? [] : $billAddress['data'][0],
                'taxAddress' => empty($taxAddress['data'][0]) ? [] : $taxAddress['data'][0],
                'provinces' => empty($provinces) ? [] : $provinces->toArray(),
                'tax_districts' => empty($taxdistricts['data']) ? [] : collect($taxdistricts['data'])->sortBy('name'),
                'tax_subdistricts' => empty($taxsub_districts['data']) ? [] : collect($taxsub_districts['data'])->sortBy('name'),
                'bill_districts' => empty($billdistricts['data']) ? [] : collect($billdistricts['data'])->sortBy('name'),
                'bill_subdistricts' => empty($billsub_districts['data']) ? [] : collect($billsub_districts['data'])->sortBy('name'),
                'is_use_tax' => $is_use_tax_adddress
            ];
        } catch (\Exception $e) {
            $profileData['makroCard'] = [];
            $profileData['profile'] = [];
            $profileData['billAddress'] = [];
            $profileData['taxAddress'] = [];
            $profileData['tax_districts'] = empty($taxdistricts['data']) ? [] : collect($taxdistricts['data'])->sortBy('name');
            $profileData['tax_subdistricts'] = empty($taxsub_districts['data']) ? [] : collect($taxsub_districts['data'])->sortBy('name');
            $profileData['bill_districts'] = empty($billdistricts['data']) ? [] : collect($billdistricts['data'])->sortBy('name');
            $profileData['bill_subdistricts'] = empty($billsub_districts['data']) ? [] : collect($billsub_districts['data'])->sortBy('name');
        }

        $this->addBreadcrumb('members.profile', trans('frontend.tax_address'), route('members.taxAddress'));
        return view('member.tax-address', $profileData);
    }

    public function compareAddress($address1, $address2, $fields)
    {
        $is_same = false;
        if (!empty($address1) && !empty($address2) && !empty($fields)) {
            foreach ($fields as $field) {
                if ($address1[$field] != $address2[$field]) {
                    return false;
                }

                return true;
            }
        } else {
            return $is_same;
        }
    }

    public function taxUpdate(Request $request)
    {
        //    dd($request);
        $this->validate($request, [
            'tax_shop_name' => 'required',
            'tax_branch' => 'required',
            'tax_tax_id' => 'required|max:13|min:13',
            'tax_mobile_phone' => 'required|max:10|min:10',
            'tax_email' => 'nullable|email',
            'tax_address' => 'required',
            'tax_sub_district_id' => 'required',
            'tax_district_id' => 'required',
            'tax_province_id' => 'required',
            'tax_postcode' => 'required',
        ], [], [
            'tax_shop_name' => trans('frontend.company_name'),
            'tax_branch' => trans('frontend.branch'),
            'tax_tax_id' => trans('frontend.tax_id'),
            'tax_mobile_phone' => trans('frontend.mobile_phone'),
            'tax_contact_email' => trans('frontend.email'),
            'tax_address' => trans('frontend.address'),
            'tax_sub_district_id' => trans('frontend.subdistrict'),
            'tax_district_id' => trans('frontend.district'),
            'tax_province_id' => trans('frontend.province'),
            'tax_postcode' => trans('frontend.postcode'),
        ]);

        try {
            $makroSdk = app()->make('makroSdk');
            $profile = $makroSdk->member()->profile();
            AuthHelper::updateUserFromProfile($profile);
            $member = [
                'shop_name' => $request['tax_shop_name'],
                'branch' => $request['tax_branch'],
                'mobile_phone' => $request['tax_mobile_phone'],
                'email' => $request['tax_email']
            ];
            $response = $makroSdk->member()->updateBusiness($member);

            $member2 = [
                // 'first_name' => $profile['first_name'],
                // 'last_name' => $profile['last_name'],
                // 'email' => $profile['email'],
                'birth_day' => $profile['birth_day'],
                'tax_id' => $request['tax_tax_id']
            ];

            $updateProfile = $makroSdk->member()->updateProfile($member2);

            $data = [
                'address_name' => $request['tax_shop_name'],
//                'branch'            => $request['tax_branch'],
//                'tax_id'            => $request['tax_tax_id'],
                'contact_phone' => $request['tax_mobile_phone'],
                'contact_email' => empty($request['tax_email']) ? '' : $request['tax_email'],
                'address_type' => 'tax', // (member|business|bill|shipping)
                'address' => $request['tax_address'],
                'address2' => $request['tax_address2'],
                'address3' => '-',
                'subdistrict_id' => $request['tax_sub_district_id'],
                'district_id' => $request['tax_district_id'],
                'province_id' => $request['tax_province_id'],
                'country_code' => 'TH',
                'postcode' => $request['tax_postcode']
            ];
            if (empty($request['tax_address_id'])) {
                $rs = $makroSdk->memberAddress()->createMemberAddress($data);
            } else {
                $rs = $makroSdk->memberAddress()->updateMemberAddress($request['tax_address_id'], $data);
            }

            if (empty($request['use_billing_address'])) {
                $bill = [
                    'address_name' => empty($request['bill_shop_name']) ? '' : $request['bill_shop_name'],
                    'first_name' => empty($request['bill_first_name']) ? '' : $request['bill_first_name'],
                    'last_name' => empty($request['bill_last_name']) ? '' : $request['bill_last_name'],
                    'contact_phone' => empty($request['bill_mobile_phone']) ? '' : $request['bill_mobile_phone'],
                    'contact_email' => empty($request['bill_contact_email']) ? '' : $request['bill_contact_email'],
                    'address' => $request['bill_address'],
                    'address2' => $request['bill_address2'],
                    'address3' => '-',
                    'subdistrict_id' => $request['bill_sub_district_id'],
                    'district_id' => $request['bill_district_id'],
                    'province_id' => $request['bill_province_id'],
                    'country_code' => 'TH',
                    'postcode' => $request['bill_postcode'],
                    'address_type' => 'bill',

                ];
            } else {
                $bill = [
                    // 'first_name' => '',
                    // 'last_name' => '',
                    'address_name' => $request['tax_shop_name'] . ' ' . $request['tax_branch'],
                    'contact_phone' => $request['tax_mobile_phone'],
                    'contact_email' => empty($request['tax_email']) ? '' : $request['tax_email'],
                    'address' => $request['tax_address'],
                    'address2' => $request['tax_address2'],
                    'address3' => '-',
                    'subdistrict_id' => $request['tax_sub_district_id'],
                    'district_id' => $request['tax_district_id'],
                    'province_id' => $request['tax_province_id'],
                    'country_code' => 'TH',
                    'postcode' => $request['tax_postcode'],
                    'address_type' => 'bill',

                ];
            }

            if (empty($request['bill_address_id'])) {
                $bill_rs = $makroSdk->memberAddress()->createMemberAddress($bill);
            } else {
                $bill_rs = $makroSdk->memberAddress()->updateMemberAddress($request['bill_address_id'], $bill);
            }
        } catch (\Exception $e) {
            $errors = [$e->getMessage()];

            if ($e instanceof SDKException) {
                $errors = [$e->getUserMessage()];

                if (!empty($e->getErrors())) {
                    $errors = array_merge($errors, $e->getErrors());
                }
            }

            return redirect()->back()->withInput()->withErrors($errors);
        }

        $alerts = [
            'success' => [
                'messages' => [
                    trans('frontend.update_tax_address_success')
                ]
            ]
        ];

        return redirect()->route('members.taxAddress')->withAlerts($alerts);
    }

    public function taxRemove(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            if (!empty($request['tax_address_id'])) {
                $rs['tax'] = $makroSdk->memberAddress()->deleteMemberAddress($request['tax_address_id']);
            }
            if (!empty($request['bill_address_id'])) {
                $rs['bill'] = $makroSdk->memberAddress()->deleteMemberAddress($request['bill_address_id']);
            }


        } catch (\Exception $e) {
            // It's background process
            $errors = [$e->getMessage()];
            if (!empty($e->getErrors())) {
                $errors = array_merge($errors, $e->getErrors());
            }

            return redirect()->back()->withInput()->withErrors($errors);
        }

        $alerts = [
            'success' => [
                'messages' => [
                    trans('frontend.remove_tax_address_success')
                ]
            ]
        ];
        return redirect()->route('members.taxAddress')->withAlerts($alerts);
    }

    public function orders()
    {
        $response = [];
        try {
            $makroSdk = app()->make('makroSdk');

            $parameters = [
                'page' => request()->get('page', 1),
            ];

            if (!empty(request()->get('sort_by'))) {
                $sort_type = request()->get('sort_by');

                if ($sort_type == 'limit') {
                    $parameters['limit'] = 5;
                } elseif ($sort_type == 'one_month') {
                    $dateAgo = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
                    $parameters['created_at'] = [
                        'start' => $dateAgo . ' 00:00:00',
                        'end' => date('Y-m-d') . ' 23:59:59'
                    ];
                } elseif ($sort_type == 'six_month') {
                    $dateAgo = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-6 month"));
                    $parameters['created_at'] = [
                        'start' => $dateAgo . ' 00:00:00',
                        'end' => date('Y-m-d') . ' 23:59:59'
                    ];
                }
            }

            $response = $makroSdk->order()->get($parameters);

//            $response['data'][0]['items'][0]['refund']['summary']['quantity'] = 0;
//            $response['data'][0]['items'][0]['item_status'] = 'completed';
//            $response['data'][0]['items'][1]['item_status'] = 'shipping';
//            $response['data'][1]['items'][0]['item_status'] = 'completed';
//            dd($response['data']);

            foreach ($response['data'] as $index => $currentItem) {
                foreach ($currentItem['items'] as $index2 => $item) {
                    $response['data'][$index]['items'][$index2]['est_delivery_date']['delivery_date'] = CartHelper::getEstimateDeliveryDate($item);
                    $response['data'][$index]['items'][$index2]['item_status_text'] = CartHelper::getOrderItemStatus($item);
                    $response['data'][$index]['items'][$index2]['item_status_icon'] = CartHelper::getOrderItemStatusIcon($item);
                    $response['data'][$index]['items'][$index2]['is_cancelled'] = CartHelper::isOrderCancelled($item);
                }
            }

            $orders = collect($response['data']);
        } catch (\Exception $e) {
            $orders = collect([]);
        }

        $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
        $total = array_get($response, 'meta.pagination.total', 0);
        $perPage = array_get($response, 'meta.pagination.per_page', 5);
        $currentPage = array_get($response, 'meta.pagination.current_page', request()->get('page', 1));
        $currentItems = $orders;

        $data['orderLists'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage,
            $currentPage, $options);

        $this->addBreadcrumb('members.orders', trans('frontend.my_order'), route('members.orders'));

        return view('member.order-list', $data);
    }

    public function orderDetail($orderId = null)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $parameters = [
                'includes' => 'detail'
            ];

            $response = $makroSdk->order()->getOrderDetail($orderId, $parameters);
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                if ($e->getCode() == 203404) {
                    $message = __('frontend.not_found_order');
                } else {
                    $message = $e->getUserMessage();
                }
            } else {
                $message = $e->getMessage();
            }

            $errors = [$e->getMessage()];
            if (!empty($e->getErrors())) {
                $errors = array_merge($errors, $e->getErrors());
            }

            return redirect()->route('members.orders')->withInput()->withErrors($errors);
        }

        $member_id = AuthHelper::getMemberId();
        if ($response['member_id'] != $member_id) {
            return redirect()->route('members.orders')->withErrors([trans('frontend.order_not_found')]);
        }
        $response['convert_type'] = 'detail';
        $cart = CartHelper::convertOrderDetailDataToCartData($response);


        //จำลอง case ต่างๆ
//        $cart['data'][0]['content']['data']['cancel']['history'] = [
//            [
//                'quantity' => 1,
//                'date' => '2018-03-03',
//                'amount' => 100,
//                'simple_discount' => 1,
//                'complex_discount' =>1,
//                'total_discount' => 1,
//            ]
//        ];
//        $cart['data'][0]['content']['data']['item_status'] = 'completed';
        //จำลอง case ต่างๆ


        $cart = CartHelper::generateOrderItem($cart['data']);
        $orderData['cart'] = CartHelper::getCartGroup($cart);
        $orderData['summary'] = $response['detail']['summary'];
        $orderData['promotions'] = $response['detail']['promotions'];
        $orderData['orders'] = $response;
        $orderData['orders']['detail']['barcode'] = CartHelper::generateOrderBarcode($response['detail']['order_no'],
            $response['barcode_no']);

        $this->addBreadcrumb('members.orders', trans('frontend.my_order'), route('members.orders'));
        $this->addBreadcrumb('members.orders-detail', trans('frontend.order_detail'),
            route('members.order-detail', ['order_id' => $orderId]));

        return view('member.order-detail', $orderData);
    }

    public function listWishList()
    {
        $response = [];
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->favorite()->get([
                'includes' => 'detail',
                'page' => request()->get('page', 1),
                'per_page' => 999999
            ]);
        } catch (\Exception $e) {
            $wishlists = collect([]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => array_get($response, 'data', [])
            ]
        ]);
    }
}