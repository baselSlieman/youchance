@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')

@livewire(App\Livewire\chats\gift::class,['chat'=>$chat])
@endsection
