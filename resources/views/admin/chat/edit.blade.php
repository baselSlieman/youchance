@extends('admin.layouts.app')

@section('title', 'Edit Chat')

@section('content')

    <div class="container my-3">
        <h4 class="ps-1"><i class="fas fa-edit"></i> @yield('title'):</h4>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('chats.update',$chat) }}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">Chat {{ $chat->id}} Info:</li>
                    <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $chat->username}}</li>
                    <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $chat->created_at }}</li>
                    <li class="list-group-item"><i class="fas fa-coins me-2"></i> balance: {{ $chat->balance }} NSP</li>
                    <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> Info: {{$chat->info}}</li>
                  </ul>
            </div>
            <div class="mb-3">
                <label for="balance" class="form-label">user balance:</label>
                <input type="number" name="balance" id="balance" class="form-control" placeholder=""
                    value="{{ old('balance',$chat->balance) }}" aria-describedby="helpId" />
            </div>

            <div class="mb-3">
                <label for="info" class="form-label">Info</label>
                <textarea class="form-control" name="info" id="info" rows="3">{{ old('info',$chat->info) }}</textarea>
            </div>


            <input type="submit" class="btn btn-primary" value="Save" />
            <a  class="btn btn-primary" href="{{route('chats.index')}}">Back</a>


            </from>
    </div>

@endsection
