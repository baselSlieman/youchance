@extends('admin.layouts.app')

@section('title', 'User Affiliates')

@section('content')

@livewire('chats.affiliate',['chat'=>$chat])

@endsection
