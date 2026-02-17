@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        <div class="row">
            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <livewire:admin.users.user-list/>
            </div>
        </div>
    </main>
@endsection
@section('scripts')

@endsection
