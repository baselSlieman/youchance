@extends('admin.layouts.app')

@section('title', 'withdraws')

@section('content')



    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">

            <div class="col col-md-12">

                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-upload"></i> @yield('title')</h1>
                    <div><a href="{{ route('withdraws.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i></a></div>
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
                <table class="table mt-3 text-center table-bordered" style="vertical-align: middle;">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th><i class="fas fa-user"></i> User</th>
                            <th><i class="fas fa-money-bill-wave"></i> amount</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> info</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdraws as $withdraw)
                            <tr>
                                <td>{{ $withdraw->id }}</td>
                                <td>
                                    <p class="mt-1" title="{{$withdraw->chat->balance}} NSP"><i class="far fa-user"></i> {{ $withdraw->chat->username}}</p>
                                    <span class="badge rounded-pill text-bg-primary bg-primary">
                                        <i class="fas fa-hashtag"></i> <a class="text-light"
                                            href="#">{{ $withdraw->chat->id }}</a>
                                    </span>
                                    <p class="mb-1 mt-3"><i style="cursor: pointer" class="far fa-copy"></i> <code onclick="javascript:navigator.clipboard.writeText(this.innerText);">{{ $withdraw->code }}</code></p>
                                </td>

                                <td>
                                    <p class="mt-1 text-primary fs-6 text-break text-nowrap"><i class="fas fa-coins"></i> Amount: {{$withdraw->amount }} NSP</p>
                                    <p class="text-danger"><i class="fas fa-coins"></i> Final: {{$withdraw->finalAmount }} NSP</p>
                                    <p class="mb-1 text-success"><i class="fas fa-coins"></i> Discount: {{ $withdraw->discountAmount }} NSP</p>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    <p>{{$withdraw->status}}</p>
                                    <p class="d-none d-md-block"><i class="far fa-calendar-alt"></i> {{ $withdraw->created_at }}</p>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    @if ($withdraw->status =="requested")
                                        <div class="text-center">
                                            <a href="{{ route('withdraws.completeOrder', $withdraw) }}" class="btn btn-success"><i class="fas fa-check text-light"></i></a>
                                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#messageModal{{$withdraw->id}}"><i class="far fa-times-circle"></i></button>
                                        </div>
                                    @endif
                                    <div class="text-center mt-2">
                                        <a href="{{ route('withdraws.edit', $withdraw) }}" class="btn btn-primary"><i class="fas fa-marker"></i></a>
                                        <form method="POST" class="d-inline-block" action="{{ route('withdraws.destroy', $withdraw) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr class="d-table-row d-md-none">
                                <td colspan="2">
                                    <i class="fas fa-info-circle me-2"></i> Status: {{$withdraw->status}}
                                </td>
                                <td colspan="3"><i class="far fa-calendar-alt me-2"></i> {{ $withdraw->created_at }}</td>
                            </tr>
                            <tr class="bg-dark d-table-row d-md-none">
                                <td colspan="5" >
                                    @if ($withdraw->status =="requested")
                                    <a href="{{ route('withdraws.completeOrder', $withdraw) }}" class="btn btn-success btn-sm"><i class="fas fa-check text-light"></i></a>
                                    <button class="btn btn-warning mx-3 btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal{{$withdraw->id}}"><i class="fas fa-times-circle"></i></button>
                                    @endif

                                        <a href="{{ route('withdraws.edit', $withdraw) }}" class="btn btn-primary me-3 btn-sm"><i
                                            class="fas fa-marker"></i></a>
                                        <form method="POST" class="d-inline-block" action="{{ route('withdraws.destroy', $withdraw) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="fa fa-trash"></i></button>
                                        </form>
                                </td>
                            </tr>

                            <!-- Modal -->
<div class="modal fade" id="messageModal{{$withdraw->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="{{ route('withdraws.rejectOrder', $withdraw) }}">
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
                                    <h3>No Withdraws</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

                {{ $withdraws->links() }}
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
