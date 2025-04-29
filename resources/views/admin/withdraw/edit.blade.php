@extends('admin.layouts.app')

@section('title', 'Edit Withdraw')

@section('content')

    @livewire(App\Livewire\withdraws\Edit::class,["withdraw"=>$withdraw])
@endsection
