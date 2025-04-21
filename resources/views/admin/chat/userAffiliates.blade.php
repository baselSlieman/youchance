@extends('admin.layouts.app')

@section('title', 'User Affiliates')

@section('content')
    <div class="container my-3">
        <div class="row justify-content-center g-2 gx-3">
            <div class="col col-md-12 px-3">


                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-user-tie"></i> User Affiliates:</h2>
                    <div><a href="{{ route('chats.index') }}" class="btn btn-outline-danger me-2"><i
                                class="fas fa-arrow-right"></i></a></div>
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
                <div class="row mt-3 mx-2 p-2 border">
                    <div class="col-12 row text-success my-2 align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0">This Month:</h5>
                        </div>
                        <div class="col-md-4 my-2 md-0">
                            <h5 class="mb-0">Count: {{ $totalAffCount }} <sup>aff</sup></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0">Total: {{ $totalAffAmount }} <sup>NSP</sup></h5>
                        </div>
                    </div>
                    <div class="col-12 row text-danger my-2">
                        <div class="col-md-4">
                            <h5 class="mb-0">Last Month:</h5>
                        </div>
                        <div class="col-md-4 my-2 md-0">
                            <h5 class="mb-0">Count: {{ $totalAffCount_last }} <sup>aff</sup></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0">Total: {{ $totalAffAmount_last }} <sup>NSP</sup></h5>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table mt-3 table-bordered table-striped" style="vertical-align: middle;">
                        <thead class="bg-dark-subtle">
                            <tr>
                                <th class="text-center">id</th>
                                <th><i class="fas fa-user-tie"></i> Agent</th>
                                <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($affiliates as $affiliate)
                                <tr>
                                    <td class="text-center">{{ $affiliate->id }}</td>
                                    <td>
                                        <p class="mt-1"><i class="fas fa-user-tie"></i> agent:
                                            {{ $affiliate->chat->id }}-{{ $affiliate->chat->username }}</p>
                                        <p class="mt-1"><i class="fas fa-percentage"></i> affiliate_amount: <strong
                                                class="text-danger">{{ $affiliate->affiliate_amount }}</strong> NSP</p>
                                        <p class="mb-1 mt-3"><i class="fas fa-coins"></i> amount: <strong
                                                class="text-danger">{{ $affiliate->amount }}</strong> NSP</p>
                                        <div class="d-block d-md-none mb-1 mt-3">
                                            <p class="mt-1"><i class="fas fa-user-plus"></i> user:
                                                {{ $affiliate->client }}</p>
                                            <p class="mt-1"><i class="far fa-calendar-alt"></i>
                                                {{ $affiliate->created_at }} (<strong
                                                    class="text-success">{{ $affiliate->month_at }}</strong>)</p>
                                            <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i>
                                                {{ $affiliate->status }}</p>
                                        </div>
                                    </td>



                                    <td class="d-none d-md-table-cell">
                                        <p class="mt-1"><i class="fas fa-user-plus"></i> user: {{ $affiliate->client }}
                                        </p>
                                        <p class="mt-1"><i class="far fa-calendar-alt"></i> {{ $affiliate->created_at }}
                                            (<strong class="text-success">{{ $affiliate->month_at }}</strong>)</p>
                                        <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i> {{ $affiliate->status }}
                                        </p>
                                    </td>

                                </tr>



                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <h3>No Affiliates</h3>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{ $affiliates->links() }}
            </div>
        </div>
    </div>


@endsection
