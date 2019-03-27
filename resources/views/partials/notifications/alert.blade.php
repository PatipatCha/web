@if (Session::has('alerts'))
    @foreach (Session::get('alerts') as $type => $alert)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-{{ $type }} alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{--<h4><i class="{{ $alert['icon'] }}"></i> {{ $alert['title'] }}</h4>--}}
                    @foreach ($alert['messages'] as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endif