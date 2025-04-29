@extends('admin.layouts.app')

@section('title', 'Edit Chat')

@section('content')

@livewire('chats.edit',['chat'=>$chat])
@endsection
