@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="bg-w">
            <div class="col-lg-12 col-md-12 no-padding border-left">
                <div class="deta-box">
                    <div class="topic-text">
                        <b>{{ $content['name'] }}</b>
                    </div>

                    <div class="deta-box2 post-content">
                        {!! $content['description'] !!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('script')

@endsection