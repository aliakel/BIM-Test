<div class="card">
    <div class="d-flex justify-content-center align-items-center py-3">
        <img class="img-thumbnail"
             style="width: 150px;border-radius: 100%"
             src="{{$expert->user->photo_url}}"
             alt="{{$expert->user->name}}">
    </div>
    <div class="card-body d-flex align-items-center flex-column">
        <h3 class="card-title text-info">{{$expert->user->name}}</h3>
        <h5 class="card-text">{{$expert->expert}}</h5>
        <div class="expert-extra-info">
            <p class="card-text">Country: {{$expert->country}}</p>
            <p class="card-text">Working hours: {{$expert->working_hours}}</p>
        </div>
        @if(!$book && !(auth()->check() && auth()->id()==$expert->user->id))
            <a href="{{route('expert.book',$expert->id)}}" class="btn btn-primary">Book now</a>
        @endif
    </div>
</div>
