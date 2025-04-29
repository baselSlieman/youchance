@extends('admin.layouts.app')

@section('title', 'User gifts')

@section('content')


@livewire('basel',['chat'=>$chat])
@endsection
