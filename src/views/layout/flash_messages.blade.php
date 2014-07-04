@if(Session::has('messages'))
    <div id="notificacion">
            @foreach(Session::get('messages') as $msg)
                <div class="alert alert-block {{ $msg['class'] }}">
                    <p>{{ $msg['msg'] }}</p>
                </div>
            @endforeach
    </div>
@endif
