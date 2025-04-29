@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')

@livewire('chats.gift',['chat'=>$chat])

@endsection
