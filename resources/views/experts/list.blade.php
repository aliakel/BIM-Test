@extends('layouts.beinmedia')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @foreach($experts as $expert)
                <div class="col-md-4">
                    @include('experts.card',['expert'=>$expert,'book'=>false])
                </div>
            @endforeach
        </div>
    </div>
@endsection
