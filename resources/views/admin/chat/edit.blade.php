@extends('admin.layouts.app')

@section('title', 'Edit Chat')

@section('content')

@livewire(App\Livewire\chats\Edit::class,['chat'=>$chat])

@endsection
