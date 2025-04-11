@extends('admin.layouts.app')

@section('title', 'Edit Charge')

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
        <form action="{{ route('charges.update',$charge) }}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">charge {{ $charge->id}} Info:</li>
                    <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $charge->chat->username}}</li>
                    <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $charge->chat_id}}</li>
                    <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $charge->created_at }}</li>
                    <li class="list-group-item"><i class="fas fa-coins me-2"></i> amount: {{ $charge->amount }} NSP</li>
                    <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> pid: {{$charge->processid}}</li>
                    <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> status: {{$charge->status}}</li>
                  </ul>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">amount:</label>
                <input type="number" name="amount" id="amount" class="form-control" placeholder=""
                    value="{{ old('amount',$charge->amount) }}" aria-describedby="helpId" />
            </div>

            <div class="mb-3">
                <label for="processid" class="form-label">process id:</label>
                <input type="number" name="processid" id="processid" class="form-control" placeholder=""
                    value="{{ old('processid',$charge->processid) }}" aria-describedby="helpId" />
            </div>


            <input type="submit" class="btn btn-primary" value="Save" />
            <a  class="btn btn-primary" href="{{route('charges.index')}}">Back</a>


            </from>
    </div>

@endsection
