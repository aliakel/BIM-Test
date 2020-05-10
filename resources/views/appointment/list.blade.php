@extends('layouts.beinmedia')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <appointments-component :appointments="{{$appointments->toJson()}}"
                                        timezone="{{$timezone}}"></appointments-component>
            </div>
        </div>
    </div>
@endsection
