<div class="container my-3">
    <h4 class="ps-1"><i class="fab fa-telegram-plane"></i> @lang('Send Message'):</h4>
    @if ($errors->any())
        <div class="alert alert-danger  alert-dismissible">

                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mt-3 alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit="send">
        <div class="my-3">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">@lang('Send message to user') {{ $chat->id}} - {{$chat->username}}:</li>
              </ul>
        </div>


        <div class="mb-3">
            <label for="message" class="form-label">@lang('Message Text'):</label>
            <textarea class="form-control" name="message" id="message" rows="3" wire:model="message">{{ old('message') }}</textarea>
        </div>
        <div class="my-3">
            <button type="submit" class="btn btn-primary"><span wire:loading.remove wire:target="send"><i class="far fa-envelope"></i> @lang('Send')</span><span wire:loading wire:target="send"><i class="fas fa-spinner"></i> @lang('Sending..')</span></button>
            <button type="button" class="btn btn-danger ms-2" onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button>
        </div>

        </from>
</div>

