@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
@include('cart.step', ['current' => 3])

<div class="container margin-bottom step-2 step-pickup-slot">
    <div class="col-lg-12 cart-payment-bg in-cart-box ">
        <div class="row">

            <div class="col-md-8 col-left">
                <div class="box">
                    <div class="section-head">
                        <h4 class="pay-pickup-text text-bold"><b>ข้อมูลการรับสินค้า</b></h4>
                        <div class="row">
                            <div class="col-xs-6 delivery-title">
                                <div class="title text-pd-red text-bold">รับสินค้าด้วยตัวเองที่สาขา</div>
                            </div>
                            <div class="col-xs-6 text-right padding-right-15">
                                <a href="" id="btn_step2_change_delivery_option" class="change-shipping"><i class="far fa-sync"></i>
                                    เปลี่ยนวิธีรับสินค้า
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 panel-option">
                                <div class="panel panel-default active">
                                    <div class="panel-body">
                                        <div class="media">
                                            <div class="media-left"><i class="far fa-hand-holding-box"></i></div>
                                            <div class="media-body media-top">
                                                <h4 class="media-heading">บมจ.สยามแม็คโคร สาขามุกดาหาร-test</h4>
                                                <p><span>
                                                        4/9 ถ.ชยางกูร ก ต.มุกดาหาร อ.เมือง มุกดาหาร 49000
                                                    </span></p>
                                            </div>
                                        </div>
                                    </div> <input type="hidden" name="shipping_address_id" value="58">
                                </div>
                                <!---->
                            </div>
                        </div>
                    </div>

                    <!------>

                    <div class="pd-assortmrnt">
                        <div class="pay-pickup">
                            <div class="group-items">
                                <div class="pd-category">
                                    <div class="topic-table4">
                                        <div class="row">
                                            <div class="col-xs-7 col-sm-8">
                                                <span class="icon">
                                                    <i class="far fa-hand-holding-box"></i>
                                                </span>
                                                <b class="text-bold">สินค้าพร้อมรับ</b>
                                            </div>
                                            <div class="col-xs-5 col-sm-4 text-right">
                                                <span class="item-qty">
                                                    <span class="item-count">3</span> รายการ
                                                </span>
                                                <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus hover"
                                                    data-content="
														<div id=&quot;lbl_information_ready_to_pickup_product&quot;>สินค้าที่มีสต๊อก และสามารถรับสินค้าที่สาขาภายใน 2-4 วัน หลังชําระเงิน</div>"
                                                    data-placement="right auto" id="tip_ready_to_ship_product" lang="[object Object]"
                                                    data-original-title="" title="">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="row row-eq-height">
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="table-eq-height">
                                                            <div class="text-day text-bold align-middle">จันทร์ 3
                                                                กันยายน เวลา 10:00 - 12:00</div>
                                                            </b>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="box-select-date table-eq-height ">
                                                            <div class="align-middle">
                                                                <ul class="nav nav-pills btn-group">
                                                                    <li class="active"><a data-toggle="pill" href="#tab1"
                                                                            class="btn btn-xs">เลือกวันรับ</a></li>
                                                                    <li><a data-toggle="pill" href="#tab2" class="btn btn-xs">ดูสินค้า</a></li>
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!------->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div id="tab1" class="tab-pane fade in active">
                                        <div class="slot hidden-xs">
                                            <table class="slot-table col-sm-12">
                                                <thead>
                                                    <tr>
                                                        <th class="slot-time-filter"></th>
                                                        <th class="button-previous">
                                                            <a href="" class="btn-prev"><i class="fal fa-angle-left"></i></a>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">ศุกร์</div>
                                                            <div class="slot-date">1</div>
                                                        </th>
                                                        <th class="slot-day text-pd-red" title="">
                                                            <div class="slot-weekday">เสาร์</div>
                                                            <div class="slot-date">2</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">อาทิตย์</div>
                                                            <div class="slot-date">3</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">จันทร์</div>
                                                            <div class="slot-date">4</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">อังคาร</div>
                                                            <div class="slot-date">5</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">พุธ</div>
                                                            <div class="slot-date">6</div>
                                                        </th>
                                                        <th class="button-next">
                                                            <a href="" class="btn-prev"><i class="fal fa-angle-right"></i></a>
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr>
                                                        <th class="slot-time text-pd-red">
                                                            <div class="text-bold">10:00 - 12:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default active">
                                                                        <i class="fas fa-check-square"></i>
                                                                        เลือก
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">12:00 - 14:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">14:00 - 16:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">16:00 - 18:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">18:00 - 20:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                            <div class="clearfix">
                                            </div>
                                        </div>
                                        <div class="slot slot-mobile  visible-xs">
                                            <div class="slot-time">
                                                <div class="button-previous">
                                                    <a href="" class="btn-prev"><i class="fal fa-angle-left"></i></a>
                                                </div>
                                                <ul class="list-inline slot-time-filter">
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">ศุกร์</div>
                                                        <div class="slot-date">1</div>
                                                    </li>
                                                    <li class="slot-day text-pd-red" title="">
                                                        <div class="slot-weekday">เสาร์</div>
                                                        <div class="slot-date">2</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">อาทิตย์</div>
                                                        <div class="slot-date">3</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">จันทร์</div>
                                                        <div class="slot-date">4</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">อังคาร</div>
                                                        <div class="slot-date">5</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">พุธ</div>
                                                        <div class="slot-date">6</div>
                                                    </li>
                                                </ul>
                                                <div class="botton-next">
                                                    <a href="" class="btn-prev"><i class="fal fa-angle-right"></i></a>
                                                </div>
                                            </div>

                                            <div class="container">
                                                <div class="row">
                                                    <table class="slot-table col-xs-12">
                                                        <tr>
                                                            <th class="slot-time text-pd-red">
                                                                <div class="text-bold">10:00 - 12:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default active"> <i
                                                                                class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">12:00 - 14:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">14:00 - 16:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">16:00 - 18:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">18:00 - 20:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tab2" class="tab-pane fade shipping-list">
                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!--group-items-->
                            <!---->

                        </div>
                    </div>

                    <div class="pd-assortmrnt">
                        <div class="pay-pickup">
                            <div class="group-items mgb-0">
                                <div class="pd-category">
                                    <div class="topic-table4">
                                        <div class="row">
                                            <div class="col-xs-7 col-sm-8">
                                                <span class="icon">
                                                    <i class="far fa-hand-holding-box"></i>
                                                </span>
                                                <b class="text-bold">สินค้าสั่งจอง</b>
                                            </div>
                                            <div class="col-xs-5 col-sm-4 text-right">
                                                <span class="item-qty">
                                                    <span class="item-count">3</span> รายการ
                                                </span>
                                                <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus hover"
                                                    data-content="
														<div id=&quot;lbl_information_ready_to_pickup_product&quot;>สินค้าที่มีสต๊อก และสามารถรับสินค้าที่สาขาภายใน 2-4 วัน หลังชําระเงิน</div>"
                                                    data-placement="right auto" id="tip_ready_to_ship_product" lang="[object Object]"
                                                    data-original-title="" title="">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="row row-eq-height">
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="table-eq-height">
                                                            <div class="text-day text-bold align-middle">จันทร์ 3
                                                                กันยายน เวลา 10:00 - 12:00</div>
                                                            </b>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="box-select-date table-eq-height ">
                                                            <div class="align-middle">
                                                                <ul class="nav nav-pills btn-group">
                                                                    <li class="active"><a data-toggle="pill" href="#tab3"
                                                                            class="btn btn-xs">เลือกวันรับ</a></li>
                                                                    <li><a data-toggle="pill" href="#tab4" class="btn btn-xs">ดูสินค้า</a></li>
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!------->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div id="tab3" class="tab-pane fade in active">
                                        <div class="slot hidden-xs">
                                            <table class="slot-table col-sm-12">
                                                <thead>
                                                    <tr>
                                                        <th class="slot-time-filter"></th>
                                                        <th class="button-previous">
                                                            <a href="" class="btn-prev"><i class="fal fa-angle-left"></i></a>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">ศุกร์</div>
                                                            <div class="slot-date">1</div>
                                                        </th>
                                                        <th class="slot-day text-pd-red" title="">
                                                            <div class="slot-weekday">เสาร์</div>
                                                            <div class="slot-date">2</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">อาทิตย์</div>
                                                            <div class="slot-date">3</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">จันทร์</div>
                                                            <div class="slot-date">4</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">อังคาร</div>
                                                            <div class="slot-date">5</div>
                                                        </th>
                                                        <th class="slot-day" title="">
                                                            <div class="slot-weekday">พุธ</div>
                                                            <div class="slot-date">6</div>
                                                        </th>
                                                        <th class="button-next">
                                                            <a href="" class="btn-prev"><i class="fal fa-angle-right"></i></a>
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr>
                                                        <th class="slot-time text-pd-red">
                                                            <div class="text-bold">10:00 - 12:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default active">
                                                                        <i class="fas fa-check-square"></i>
                                                                        เลือก
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">12:00 - 14:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">14:00 - 16:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">16:00 - 18:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="slot-time">
                                                            <div class="text-bold">18:00 - 20:00</div>
                                                        </th>
                                                        <td></td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            <div class="slot-item">
                                                                <span class="slot-select">
                                                                    <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                        เลือก </button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                            <div class="clearfix">
                                            </div>
                                        </div>
                                        <div class="slot slot-mobile  visible-xs">
                                            <div class="slot-time">
                                                <div class="button-previous">
                                                    <a href="" class="btn-prev"><i class="fal fa-angle-left"></i></a>
                                                </div>
                                                <ul class="list-inline slot-time-filter">
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">ศุกร์</div>
                                                        <div class="slot-date">1</div>
                                                    </li>
                                                    <li class="slot-day text-pd-red" title="">
                                                        <div class="slot-weekday">เสาร์</div>
                                                        <div class="slot-date">2</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">อาทิตย์</div>
                                                        <div class="slot-date">3</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">จันทร์</div>
                                                        <div class="slot-date">4</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">อังคาร</div>
                                                        <div class="slot-date">5</div>
                                                    </li>
                                                    <li class="slot-day" title="">
                                                        <div class="slot-weekday">พุธ</div>
                                                        <div class="slot-date">6</div>
                                                    </li>
                                                </ul>
                                                <div class="botton-next">
                                                    <a href="" class="btn-prev"><i class="fal fa-angle-right"></i></a>
                                                </div>
                                            </div>

                                            <div class="container">
                                                <div class="row">
                                                    <table class="slot-table col-xs-12">
                                                        <tr>
                                                            <th class="slot-time text-pd-red">
                                                                <div class="text-bold">10:00 - 12:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default active"> <i
                                                                                class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">12:00 - 14:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">14:00 - 16:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">16:00 - 18:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="slot-time">
                                                                <div class="text-bold">18:00 - 20:00</div>
                                                            </th>
                                                            <td class="">
                                                                <div class="slot-item">
                                                                    <span class="slot-select">
                                                                        <button class="btn btn-default"> <i class="fas fa-check-square"></i>
                                                                            เลือก </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tab4" class="tab-pane fade shipping-list">

                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="cart-pd-box">
                                            <div class="col-sm-7 col-xs-12 no-padding">
                                                <div class="pd-img">
                                                    <div class="cart-pd-detail-box"><a title="Defect_20262_001"><img
                                                                src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                                alt="Defect_20262_001" class="img-responsive"></a></div>
                                                </div>
                                                <div class="col-xs-9 col-sm-8">
                                                    <div class="cart-pd-detail-box2 cart-text-detail"><a title="Defect_20262_001"><b>Defect_20262_001</b><br></a>
                                                        <span>รหัสสินค้า 638939</span><br> <b><span class="item-price">119.00
                                                                ฿ </span></b>
                                                        <!---->
                                                    </div>
                                                    <!---->
                                                    <div class="qty visible-xs"><span>จำนวน</span> 1
                                                    </div>
                                                    <div class="no-padding col-xs-12 visible-xs">
                                                        <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                                    </div>
                                                    <div class="visible-xs">
                                                        <!---->
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="visible-xs">
                                                    <!---->
                                                </div>
                                            </div>
                                            <!---->
                                            <div class="col-sm-2 hidden-xs no-padding">
                                                <div class="cart-pd-detail-box text-center">
                                                    1
                                                </div>
                                            </div>
                                            <div class="col-sm-3 no-padding text-right hidden-xs">
                                                <div class="cart-pd-detail-box"><span><b>119.00 ฿</b></span></div>
                                            </div>
                                            <div class="col-xs-9 col-xs-offset-3 hidden-xs">
                                                <!---->
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!--group-items-->
                            <!---->

                        </div>
                    </div>
                </div>
            </div>

            <!--Column Right-->
            <div class="col-md-4 col-right cart-summary-sidebar">
                <div class="box">
                    <div class="summary-box">
                        <div class="pay-pickup-text text-bold"><b>สรุปรายการสั่งซื้อ</b></div>
                        <div class="promotion">
                            <!---->
                        </div>

                        <div class="cart-pd-table clearfix thead">
                            <div class="col-xs-7">รายละเอียดสินค้า</div>
                            <div class="col-xs-2 no-padding text-center hidden-xs">จำนวน</div>
                            <div class="col-xs-3 no-padding text-center hidden-xs">ราคารวม</div>
                        </div>
                        <!---->
                        <div class="summary-box-item height-auto">
                            <div class="summary-box-table">
                                <div class="cart-pd-table">
                                    <div class="topic-table4">
                                        <div class="head text-bold">
                                            <span class="icon"><i class="fas fa-truck-container"></i></span>
                                            <a data-toggle="collapse" href=".pd-collapse" aria-expanded="false"
                                                aria-controls="pd-collapse" class="title">
                                                <b>สินค้าพร้อมส่ง</b></a>
                                            <div class="item-qty">
                                                <span class="item-count">2</span> รายการ
                                                <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus hover"
                                                    data-content="<div id=&quot;lbl_information_ready_to_pickup_product&quot;>สินค้าที่มีสต๊อก และสามารถรับสินค้าที่สาขาภายใน 2-4 วัน หลังชําระเงิน</div>"
                                                    data-placement="auto bottom" id="tip_ready_to_ship_information"
                                                    data-original-title="" title=""><i class="fas fa-info-circle"></i></a>

                                                <button data-toggle="collapse" href="#pd-collapse" aria-expanded="false"
                                                    aria-controls="pd-collapse" class="btn btn-xs btn-default" id="pd-toggle"></button>
                                            </div>
                                        </div>
                                        <!---->
                                    </div>
                                    <div class="collapse in pd-scroll pd-collapse" id="pd-collapse">
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                    </div>


                                    <div class="topic-table4">
                                        <div class="head text-bold">
                                            <span class="icon"><i class="far fa-hand-holding-box"></i></span>
                                            <a data-toggle="collapse" href=".pd-collapse2" aria-expanded="false"
                                                aria-controls="pd-collapse" class="title">
                                                <b>สินค้าสั่งจอง</b></a>
                                            <div class="item-qty">
                                                <span class="item-count">2</span> รายการ
                                                <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus hover"
                                                    data-content="<div id=&quot;lbl_information_ready_to_pickup_product&quot;>สินค้าที่มีสต๊อก และสามารถรับสินค้าที่สาขาภายใน 2-4 วัน หลังชําระเงิน</div>"
                                                    data-placement="auto bottom" id="tip_ready_to_ship_information"
                                                    data-original-title="" title=""><i class="fas fa-info-circle"></i></a>

                                                <button data-toggle="collapse" href="#pd-collapse2" aria-expanded="false"
                                                    aria-controls="pd-collapse" class="btn btn-xs btn-default" id="pd-toggle"></button>
                                            </div>
                                        </div>
                                        <!---->
                                    </div>
                                    <div class="collapse pd-scroll pd-collapse2" id="pd-collapse2">
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                        <div class="cart-pd-box clearfix" delivery-type="">
                                            <div class="no-padding pd-img-col col-sm-6 col-xs-12 col-xs-12 col-sm-7">
                                                <div class="pd-img"><img src="https://staging-dynamic-cdn-makro.eggdigital.com/b34aElbWB.jpg?process=resize&amp;resize_width=150&amp;resize_height=150"
                                                        class="img-responsive"></div>
                                                <div class="col-xs-8 col-sm-8">
                                                    <div class="item-detail">
                                                        <div><b>Defect_20262_001</b></div>
                                                        <div>รหัสสินค้า 638939</div>
                                                        <div class="price"><b>119.00 ฿</b> per EA
                                                        </div>
                                                        <!---->
                                                        <!---->
                                                    </div>
                                                    <div class="visible-xs print-hidden">
                                                        <div class="no-padding col-xs-12 col-sm-2"><span class="qty"><b>จำนวน
                                                                    1</b></span></div>
                                                        <div class="no-padding col-xs-12 col-sm-3"><span class="cart-subtotal"><b>ราคารวม
                                                                    119.00 ฿</b></span></div>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-padding text-center hidden-xs pd-qty-col col-xs-12 col-sm-2"><span
                                                    class="qty"><b>1</b></span></div>
                                            <div class="no-padding text-right hidden-xs pd-price-col col-xs-12 col-sm-3"><span
                                                    class="cart-subtotal"><b>119.00 ฿</b></span></div>
                                            <!---->
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div>
                        <div class="summary-section">
                            <div class="total-price-box">
                                <div class="total-price1"><b>ราคาสินค้า</b></div>
                                <div id="lbl_shopping_total" class="total-price2">
                                    170.00 ฿
                                </div>
                                <!---->
                                <div class="total-price1"><b>ราคารวม (รวมภาษีมูลค่าเพิ่ม)</b></div>
                                <div id="lbl_subtotal" class="total-price2">
                                    170.00 ฿
                                </div>
                                <div>
                                    <div class="clearfix"></div>
                                    <div class="total-price1"><b>ค่าจัดส่ง</b></div>
                                    <div id="lbl_delivery_fee" class="total-price2">
                                        ฟรี
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="coupon-box mgt-2">

                                    <input type="" class="form-control form-coupon" placeholder="รหัสคูปอง">
                                    <a type="button" class="btn btn-secondary btn-coupon">
                                        ใช้คูปอง
                                    </a>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="coupon-box mgt-2">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6  eq-height">
                                            <div class="align-middle">
                                                <div class="text-bold coupon-id">
                                                    <i class="fas fa-ticket-alt"></i>
                                                    WINTER50
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 eq-height">
                                            <div class="edit-group align-middle">
                                                <a type="button" class="btn btn-default ">
                                                    แก้ไข
                                                </a>
                                                <a type="button" class="btn btn-default ">
                                                    ลบ
                                                </a>
                                            </div>

                                        </div>
                                    </div>


                                    <div class="clearfix"></div>
                                </div>
                                <div class="discount-box mgt-2">
                                    <div class="discount-text1">ส่วนสดจากคูปอง 50%</div>
                                    <div class="discount-text2 text-bold"> -999฿</div>
                                </div>
                                <div class="clearfix"></div>
                            </div>


                            <div class="total-price-box2">
                                <div class="total-price1"><b><span>ยอดซื้อสุทธิ (รวมภาษีมูลค่าเพิ่ม)</span></b></div>
                                <div class="total-price2"><b><span>170.00 ฿</span></b></div>
                                <div class="clearfix"></div>
                            </div>
                            <!---->
                            <!---->
                            <!---->
                            <!---->
                            <!---->
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-sm-6 col-md-12 col-lg-6 margin-bottom-15"><a href="http://makroclick.test/th/carts"
                                        class="btn btn-default btn-block">
                                        ตะกร้าสินค้า
                                    </a></div>
                                <div class="col-sm-6 col-md-12 col-lg-6"><a class="btn btn-primary btn-block pull-right">
                                        <!----> ทำรายการสั่งซื้อต่อ
                                    </a></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<!--container-->

<!--================================================== -->
<!--================================================== -->
@endsection