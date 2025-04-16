@extends('admin.layouts.app')

@section('title', 'Ichancy '.$type)

@section('content')



    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">

            <div class="col col-md-12 px-3">

                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-random"></i> @yield('title'):</h3>
                    <div><a href="{{route('ichancies.index')}}" class="btn btn-outline-danger me-2"><i class="fas fa-arrow-right"></i></a></div>
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
                <table class="table mt-3 table-bordered">
                    <thead>
                        <tr>
                            <th class=" text-secondary"><i class="fas fa-retweet"></i> Transaction</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-rocket"></i> Ichancy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ichancyTransRes as $ichancytr)
                            <tr>
                                <td>
                                    <p class="text-info"><i class="fas fa-share"></i> ID: {{ $ichancytr->id }}</p>
                                    <div class="d-flex justify-content-between align-items-center"><p><i class="fas fa-rocket"></i> ichancy: {{$ichancytr->ichancy->username}}<small class="text-danger ms-2 d-none"><i class="fas fa-lock"></i> {{$ichancytr->ichancy->password}}</small></p><p><i class="fas fa-eye"></i></p></div>
                                    <p class="mt-1"><i class="fas fa-rocket"></i> player id: {{ $ichancytr->ichancy->identifier }}</p>
                                    <p><i class="fas fa-coins"></i> amount: <span class="text-danger">{{$ichancytr->amount}}</span> NSP</p>
                                    <p><i class="far fa-calendar-alt"></i> date: {{ $ichancytr->created_at }}</p>
                                    <div class="d-block d-md-none">
                                        <p class="mt-1"><i class="far fa-user"></i> user: {{ $ichancytr->chat->username}}</p>
                                        <p class="mt-1"><i class="fas fa-user"></i> chat_id: {{ $ichancytr->chat_id }}</p>
                                        <p>
                                            @switch($ichancytr->status)
                                            @case("complete")<i class="far fa-check-circle text-success"></i>@break
                                            @case("requested")<i class="far fa-hourglass text-primary"></i>@break
                                            @default
                                        @endswitch {{$ichancytr->status}}</p>
                                    </div>
                                </td>



                                <td class="d-none d-md-table-cell">
                                    <p class="mt-1"><i class="far fa-user"></i> user: {{ $ichancytr->chat->username}}</p>
                                    <p class="mt-1"><i class="fas fa-user"></i> chat_id: {{ $ichancytr->chat_id }}</p>
                                    <p>
                                        @switch($ichancytr->status)
                                        @case("complete")<i class="far fa-check-circle text-success"></i>@break
                                        @case("requested")<i class="far fa-hourglass text-primary"></i>@break
                                        @default

                                    @endswitch {{$ichancytr->status}}</p>
                                </td>


                            </tr>



                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <h3>No chats</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

                {{ $ichancyTransRes->links() }}
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
