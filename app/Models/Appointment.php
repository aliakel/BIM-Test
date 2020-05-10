<?php

namespace BeInMedia\Models;

use BeInMedia\Models\Expert;
use BeInMedia\Models\User;
use Carbon\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use Cachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'expert_id',
        'from_time',
        'to_time',
        'day',
        'duration'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class, 'expert_id');
    }


    public function setDayAttribute($value){
        $this->attributes['day']=Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d');
    }
}
