@extends('admin.layouts.app')

@section('title', 'Edit Charge')

@section('content')

@livewire(App\Livewire\charges\Edit::class,['charge'=>$charge])
@endsection
