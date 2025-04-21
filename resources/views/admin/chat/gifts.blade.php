@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')
    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">
            <div class="col col-md-12 px-3">


                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-gift"></i> User gifts:</h2>
                    <div>
                        {{-- <a href="{{ route('chats.index') }}" class="btn btn-outline-success me-2">
                            <i class="fas fa-gifts"></i></a> --}}
                            <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal"
                            data-bs-target="#exampleModal"><i class="fas fa-gifts"></i></button>
                        <a href="{{ route('chats.index') }}" class="btn btn-outline-danger me-2"><i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @elseif (session('danger'))
                    <div class="alert alert-danger mt-3">
                        {{ session('danger') }}
                    </div>
                @endif

                <div class="table-responsive mb-3">
                    <table class="table mt-3 table-bordered table-striped" style="vertical-align: middle;">
                        <thead class="bg-dark-subtle">
                            <tr>
                                <th class="text-center">id</th>
                                <th><i class="fas fa-user"></i> User</th>
                                <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gifts as $gift)
                                <tr>
                                    <td class="text-center">{{ $gift->id }}</td>
                                    <td>
                                        <p class="mt-1"><i class="fas fa-user"></i> user:
                                            {{ $gift->chat->id }}-{{ $gift->chat->username }}</p>

                                        <p class="mb-1 mt-3"><i class="fas fa-coins"></i> amount: <strong
                                                class="text-danger">{{ $gift->amount }}</strong> NSP</p>
                                        <div class="d-block d-md-none mb-1 mt-3">
                                            <p class="mt-1"><i class="fas fa-user-plus"></i> code:
                                                {{ $gift->code }}</p>
                                            <p class="mt-1"><i class="far fa-calendar-alt"></i>
                                                {{ $gift->created_at }}</p>
                                            <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i>
                                                {{ $gift->status }}</p>
                                        </div>
                                    </td>



                                    <td class="d-none d-md-table-cell">
                                        <p class="mt-1"><i class="fas fa-receipt"></i> code: {{ $gift->code }}
                                        </p>
                                        <p class="mt-1"><i class="far fa-calendar-alt"></i> {{ $gift->created_at }}</p>
                                        <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i> {{ $gift->status }}
                                        </p>
                                    </td>

                                </tr>



                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <h3>No gifts</h3>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{ $gifts->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send Gift To User:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('chats.giftstore')}}">
                    @csrf
                <div class="modal-body" id="modal-body">
                    <p>Enter gift Amount:</p>
                    <input type="hidden" name="chat_id" value="{{$chatId}}"/>
                    <div class="mb-3">

                        <input
                            type="number"
                            class="form-control"
                            name="amount"
                            id="amount"
                            aria-describedby="helpId"
                            placeholder="amount"
                        />
                        <small id="helpId" class="form-text text-muted">money you will send to user</small>
                    </div>

                </div>
                <div class="modal-footer" id="mfooter">
                     <button type="submit" class="btn btn-success me-3"><i
                                class="fas fa-gift me-1"></i>Confirm</button><button type="button"
                            class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
            </div>
        </div>
    </div>

@endsection
