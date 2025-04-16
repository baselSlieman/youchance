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


            <input type="submit" class="btn btn-success me-3" value="Save" /> |
            <a  class="btn btn-primary mx-3" href="{{route('charges.index')}}">Back</a> |
            <button type="button" class="btn btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Delete</button>
            </from>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Charge:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <p>Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer" id="mfooter">
                    <form method="POST" class="d-inline-block" action="{{ route("charges.destroy",$charge) }}">@csrf @method('DELETE') <button type="submit" class="btn btn-danger me-3"><i class="fas fa-trash me-1"></i>Confirm</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></form>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
