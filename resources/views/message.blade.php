@if(session()->has('message'))
    <div class="my-4 border px-4 py-4 mx-4 text-sm font-light rounded text @if(session()->get('message')['type'] == 'success') text-green-700 bg-green-200 border-green-400 @else text-red-700 bg-red-200 border-red-400 @endif">
        {!! session()->get('message')['message'] !!}
    </div>
@endif
