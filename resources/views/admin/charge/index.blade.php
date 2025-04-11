@extends('admin.layouts.app')

@section('title', 'Charges')

@section('content')



    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">

            <div class="col col-md-12 px-3">

                <div class="d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-wallet"></i> @yield('title')</h3>
                    {{-- <div><a href="{{ route('charges.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i></a></div> --}}
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
                <div class="table-responsive">
                <table class="table mt-3 table-bordered" style="vertical-align: middle;">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th class=" text-secondary"><i class="fas fa-user"></i> User</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> info</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($charges as $charge)
                            <tr>
                                <td>{{ $charge->id }}</td>
                                <td>
                                    <p class="mt-1"><i class="far fa-user"></i> username: {{ $charge->chat->username}}</p>
                                    <p class="mt-1"><i class="fas fa-user"></i> chat id: {{ $charge->chat_id }}</p>
                                    <p class="mb-1 mt-3"><i class="fas fa-coins"></i> amount: <strong class="text-danger">{{ $charge->amount}}</strong> NSP</p>
                                </td>



                                <td class="d-none d-md-table-cell">
                                    <p class="d-none d-md-block"><i class="far fa-calendar-alt"></i> {{ $charge->created_at }}</p>
                                    <p><i class="fas fa-hashtag"></i> PID: {{$charge->processid}}</p>
                                    <p>
                                        @switch($charge->status)
                                        @case("complete")<i class="far fa-check-circle text-success"></i>@break
                                        @case("pending")<i class="far fa-hourglass text-primary"></i>@break
                                        @default

                                    @endswitch {{$charge->status}}</p>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    <div class="text-center mt-2">
                                        <a href="{{ route('chats.createMessage', $charge->chat) }}" class="btn btn-primary"><i class="fab fa-telegram-plane"></i></a>
                                        <a href="{{ route('charges.edit', $charge) }}" class="btn btn-success"><i class="fas fa-marker"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="d-table-row d-md-none">
                                <td colspan="2">
                                    <i class="far fa-calendar-alt me-2"></i> <span class="fw-bold">date:</span> {{$charge->created_at}}<br>
                                    <i class="fas fa-info-circle me-2"></i> <span class="fw-bold">PID:</span> {{$charge->processid}}<br>
                                    <i class="fas fa-info-circle me-2"></i> <span class="fw-bold">status:</span> {{$charge->status}}<br>
                                    <i class="fas fa-coins me-2"></i> <span class="fw-bold">balance:</span> {{$charge->chat->balance}} NSP
                                </td>
                            </tr>
                            <tr class="d-table-row d-md-none">
                                <td colspan="2" class="text-center bg-body-tertiary ">


                                    <a href="{{ route('chats.createMessage', $charge->chat) }}" class="btn btn-primary btn-sm mx-3"><i class="fab fa-telegram-plane"></i></a>
                                    <a href="{{ route('charges.edit', $charge) }}" class="btn btn-success btn-sm mx-3"><i class="fas fa-marker"></i></a>
                                </td>
                            </tr>

                            <!-- Modal -->
<div class="modal fade" id="messageModal{{$charge->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="{{ route('charges.store', $charge) }}">
            <div class="modal-body">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="type here..."/>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
        </form>
      </div>
    </div>
  </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <h3>No charges</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

                {{ $charges->links() }}
            </div>
            <!-- Button trigger modal -->






            {{-- <div class="col col-md-3 p-3">
                <h4>Filter</h4> --}}
                {{-- <form method="GET">
                    <div class="form-group mb-2">
                        <label for="name">Name or Description</label>
                        <input type="text" name="name" id="name" value="{{ $name }}"
                            class="form-control" placeholder="name" aria-describedby="helpId" />
                    </div>
                    <h5 class="mb-0 mt-3">Categories</h5>

                    @foreach ($categories as $key =>$category)
                        <div class="form-check">
                            <input id="cat-{{$key}}" @checked(in_array($category->id, Request::input('categories') ?? [])) type="checkbox" name="categories[]"
                                value="{{ $category->id }}" class="form-check-input" />
                            <label for="cat-{{$key}}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                    @endforeach
                    <h5 class="mb-0 mt-3">Pricing</h5>
                    <div class="form-group mb-2">
                        <label for="name">Min</label>
                        <input type="number" name="min" id="min" value="{{$min}}"
                            class="form-control" placeholder="min" aria-describedby="helpId" />
                    </div>
                    <div class="form-group mb-1">
                        <label for="max">Max</label>
                        <input type="number" name="max" id="max" value="{{$max}}"
                            class="form-control" placeholder="max" aria-describedby="helpId" />
                    </div>


                    <div class="form-group mb-2 mt-3">
                        <input type="submit" class="btn btn-primary" value="filter" />
                        <a class="btn btn-secondary" href="{{route('products.index')}}">Reset</a>
                    </div>
                </form> --}}


        {{-- </div> --}}
    </div>
    </div>


@endsection
