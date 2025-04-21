<div class="col col-md-12 px-3">


    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0"><i class="fab fa-telegram-plane"></i> Chats:</h2>
        <div><input wire:model.live.debounce.500ms="search" type="search" class="form-control" placeholder="Search..."/></div>
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
        <thead  class="bg-dark-subtle">
            <tr>
                <th>id</th>
                <th><i class="fas fa-user"></i> User</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> info</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> option</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chats as $chat)
                <tr>
                    <td>{{ $chat->id }}</td>
                    <td>
                        <p class="mt-1"><i class="far fa-user"></i> username: {{ $chat->username}}</p>
                        <p class="mt-1"><i class="fas fa-user"></i> fullname: {{ $chat->first_name }} {{$chat->last_name}}</p>
                        <p class="mb-1 mt-3"><i class="fas fa-coins"></i> balance: <strong class="text-danger">{{ $chat->balance}}</strong> NSP</p>
                    </td>



                    <td class="d-none d-md-table-cell">
                        <p class="d-none d-md-block mt-1"><i class="far fa-calendar-alt"></i> {{ $chat->created_at }}</p>
                        @if ($chat->info)
                        <p class="mt-1"><i class="fas fa-info-circle"></i> {{$chat->info}}</p>
                        @endif
                        @if ($chat->affiliate_code)
                            <p class="mb-1 mt-3"><i class="fas fa-users"></i> {{$chat->affiliate_code}}</p>
                        @endif
                    </td>

                    <td class="d-none d-md-table-cell">
                        <div class="text-center mt-2">
                            <div>
                                <a href="{{ route('chats.createMessage', $chat) }}" class="btn btn-primary"><i class="fab fa-telegram-plane"></i></a>
                                <a href="{{ route('chats.edit', $chat) }}" class="btn btn-success"><i class="fas fa-marker"></i></a>
                            </div>
                            <div class="mt-1">
                                <a href="{{ route('chats.userAffiliates', $chat) }}" class="btn btn-danger"><i class="fas fa-percent"></i></a>
                                <a href="{{ route('chats.usergifts', $chat) }}" class="btn btn-warning"><i class="fas fa-gift"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="d-table-row d-md-none">
                    <td colspan="2">
                        <p class="mt-1"><i class="far fa-calendar-alt me-2"></i> join: {{ $chat->created_at }}</p>
                        @if ($chat->affiliate_code)
                            <p class="mt-1"><i class="fas fa-users"></i> affiliate_agent: {{$chat->affiliate_code}}</p>
                        @endif
                        @if ($chat->info)
                        <p class="mt-1 mb-1"><i class="fas fa-info-circle me-2"></i>info: {{$chat->info}}</p>
                        @endif
                    </td>
                </tr>
                <tr class="d-table-row d-md-none">
                    <td colspan="2" class="text-center bg-body-secondary">
                        <a href="{{ route('chats.createMessage', $chat) }}" class="btn btn-primary btn-sm mx-3"><i class="fab fa-telegram-plane"></i></a>
                        <a href="{{ route('chats.edit', $chat) }}" class="btn btn-success btn-sm mx-3"><i class="fas fa-marker"></i></a>
                        <a href="{{ route('chats.userAffiliates', $chat) }}" class="btn btn-danger btn-sm mx-3"><i class="fas fa-percent"></i></a>
                    </td>
                </tr>

                <!-- Modal -->
<div class="modal fade" id="messageModal{{$chat->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Message</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{ route('chats.store', $chat) }}">
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
                        <h3>No chats</h3>
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

    {{ $chats->links() }}
</div>
