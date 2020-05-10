@extends('layouts.beinmedia')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <booking-component :slots="{{json_encode($slots)}}"
                                   :other="{{json_encode($other)}}">

                </booking-component>
            </div>
        </div>
    </div>
@endsection
