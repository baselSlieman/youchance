@extends('admin.layouts.app')

@section('title', 'User Affiliates')

@section('content')

@livewire(App\Livewire\chats\Affiliate::class,['chat'=>$chat])
@endsection
