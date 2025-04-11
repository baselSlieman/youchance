@extends('admin.layouts.app')

@section('title', 'Edit Withdraw')

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
        <form action="{{ route('withdraws.update',$withdraw) }}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">Withdraw {{ $withdraw->id}} Info:</li>
                    <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $withdraw->chat->username}} - {{ $withdraw->chat->id }}</li>
                    <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> Status: {{$withdraw->status}}</li>
                    <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $withdraw->created_at }}</li>
                    <li class="list-group-item"><i class="fas fa-coins me-2"></i> Final: {{ $withdraw->finalAmount }} NSP</li>
                  </ul>
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">user code/phone:</label>
                <input type="number" name="code" id="code" class="form-control" placeholder=""
                    value="{{ old('name',$withdraw->code) }}" aria-describedby="helpId" />
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" name="amount" id="amount" value="{{ old('description',$withdraw->amount) }}">
            </div>


            <input type="submit" class="btn btn-primary" value="Save" />
            <a  class="btn btn-primary" href="{{route('withdraws.index')}}">Back</a>
            @if ($withdraw->status == "rejected")
            | <a  class="btn btn-success" href="{{route('withdraws.completeOrder',$withdraw)}}"><i class="fas fa-check text-light"></i> Accept</a>
            @endif


            </from>
    </div>

@endsection
