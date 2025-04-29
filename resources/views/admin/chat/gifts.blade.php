@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')

@livewire(App\Livewire\chats\Gift::class,['chat'=>$chat])
@endsection
