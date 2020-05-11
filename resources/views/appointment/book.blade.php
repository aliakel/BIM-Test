@extends('layouts.beinmedia')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-4">
                @include('experts.card',['expert'=>$expert,'book'=>true])
            </div>
            <div class="col-12 col-md-7">
                <booking-component :slots="{{json_encode($slots)}}"
                                   :other="{{json_encode($other)}}">

                </booking-component>
            </div>
        </div>
    </div>
@endsection
