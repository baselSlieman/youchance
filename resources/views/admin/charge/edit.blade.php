@extends('admin.layouts.app')

@section('title', 'Edit Charge')

@section('content')

@livewire('charges.edit',['charge'=>$charge])

@endsection
