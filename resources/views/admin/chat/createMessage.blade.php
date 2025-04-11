@extends('admin.layouts.app')

@section('title', 'Send Message')

@section('content')

    <div class="container my-3">
        <h4 class="ps-1"><i class="fab fa-telegram-plane"></i> @yield('title'):</h4>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('chats.sendMessage',$chat) }}" method="POST">
            @csrf
            <div class="mb-3">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">Send message to user: {{ $chat->id}} - {{$chat->username}}:</li>
                  </ul>
            </div>


            <div class="mb-3">
                <label for="message" class="form-label">Message Text:</label>
                <textarea class="form-control" name="message" id="message" rows="3">{{ old('message') }}</textarea>
            </div>


            <input type="submit" class="btn btn-primary" value="Send" />
            <a  class="btn btn-primary" href="{{ url()->previous() }}">Back</a>


            </from>
    </div>

@endsection
