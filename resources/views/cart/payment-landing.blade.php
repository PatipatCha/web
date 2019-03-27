@extends('layouts.main')

@section('content')
    <div class="container margin-bottom">
        <div class="text-center text-normal">กรุณารอสักครู่.....</div>
    </div>
@endsection

@section('script')
    <script>
      $(function () {

        var transaction = {
            'id': '{{ array_get($orderDetail, 'ms_order_no') }}',
            'store_id': '{{ array_get($orderDetail, 'detail.store.id') }}',
            'store_area': '{{ array_get($orderDetail, 'detail.store.name') }}',
            'payment_type': '{{ array_get($orderDetail, 'detail.payment.payment_type') }}',
            'shipping_type': '',
            'cost': '',
            'makro_member_id': '{{ array_get($orderDetail, 'member_id') }}',
            'makro_customer_group_id': '',
            'revenue': '{{ array_get($summary, 'grand_total') }}',
            'tax': '{{ $vat_total }}',
            'shipping': 0,
          }

        gaPurchase(transaction, '{!! json_encode(array_get($orderDetail, 'detail.items')) !!}', function () {
          window.location = '{{ $redirectUrl }}'
        })

      });

    </script>
@endsection