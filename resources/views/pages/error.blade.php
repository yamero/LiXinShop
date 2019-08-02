@extends('layouts.app')
@section('title', '错误')

@section('content')
    <div class="card">
        <div class="card-header">发生了些小意外</div>
        <div class="card-body text-center">
            <h3>{{ $msg }}</h3>
            <a class="btn btn-primary" href="{{ route('root') }}">返回首页</a>
        </div>
    </div>
@endsection