@extends('admin.layouts.app')

@section('title', 'Send Message')

@section('content')

@livewire(App\Livewire\chats\Message::class,['chat'=>$chat])
@endsection
