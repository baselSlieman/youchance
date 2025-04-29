@extends('admin.layouts.app')

@section('title', 'Edit Withdraw')

@section('content')

    @livewire('withdraws.edit',['withdraw'=>$withdraw])
@endsection
