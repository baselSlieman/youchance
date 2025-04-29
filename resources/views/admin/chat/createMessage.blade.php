@extends('admin.layouts.app')

@section('title', 'Send Message')

@section('content')

@livewire('chats.message',['chat'=>$chat])

@endsection
