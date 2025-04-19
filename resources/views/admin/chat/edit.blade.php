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


            <input type="submit" class="btn btn-primary me-3" value="Save" /> |
            <a  class="btn btn-primary mx-3" href="{{route('chats.index')}}">Back</a> |
            <button type="button" class="btn btn-danger ms-3" data-bs-toggle="modal"
            data-bs-target="#exampleModal">Delete</button>

        </form>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Chat:</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modal-body">
                            <p>Are you sure you want to delete this item?</p>
                            <span class="text-danger">This will lead to the deletion of all data associated with it.</span>
                        </div>
                        <div class="modal-footer" id="mfooter">
                            <form method="POST" class="d-inline-block" action="{{ route('chats.destroy', $chat) }}">
                                @csrf @method('DELETE') <button type="submit" class="btn btn-danger me-3"><i
                                        class="fas fa-trash me-1"></i>Confirm</button><button type="button"
                                    class="btn btn-secondary" data-bs-dismiss="modal">Close</button></form>
                        </div>
                    </div>
                </div>
            </div>
    </div>

@endsection
