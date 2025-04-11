@extends('admin.layouts.app')

@section('title', 'Edit Ichancy')

@section('content')

    <div class="container my-3">
        <h4 class="ps-1"><i class="fas fa-edit"></i> @yield('title') user:</h4>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('ichancies.update',$ichancy) }}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">ichancy {{ $ichancy->id}} Info:</li>
                    <li class="list-group-item"><i class="fas fa-user-circle me-2"></i><span class="fw-bold text-primary">username:</span> {{ $ichancy->chat->username}}</li>
                    <li class="list-group-item"><i class="fas fa-hashtag me-2"></i><span class="fw-bold text-primary">chat_id:</span> {{$ichancy->chat->id}}</li>
                    <li class="list-group-item"><i class="fas fa-user-secret me-2"></i><span class="fw-bold text-success">ichancy user:</span> {{ $ichancy->username}}</li>
                    <li class="list-group-item"><i class="fas fa-lock me-2"></i><span class="fw-bold text-success">ichancy password:</span> {{ $ichancy->password}}</li>
                    <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $ichancy->created_at }}</li>
                  </ul>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">ichancy username:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder=""
                    value="{{ old('username',$ichancy->username) }}" aria-describedby="helpId" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">ichancy password:</label>
                <input type="text" name="password" id="password" class="form-control" placeholder=""
                    value="{{ old('password',$ichancy->password) }}" aria-describedby="helpId" />
            </div>



            <input type="submit" class="btn btn-primary" value="Save" />
            <a  class="btn btn-primary" href="{{route('ichancies.index')}}">Back</a>


            </from>
    </div>

@endsection
