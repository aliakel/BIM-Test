<?php

namespace BeInMedia\Models;

use Carbon\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Appointment
 * @package BeInMedia\Models
 */
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

    /**
     * Appointment belong to one user
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Appointment belong to one expert
     * @return BelongsTo
     */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class, 'expert_id');
    }

    /**
     * Convert time slot start time to urc before saving to db
     * @param $value
     */
    public function setFromTimeAttribute($value){
        $this->attributes['from_time']=Carbon::parse($value)->setTimezone('UTC');
    }

    /**
     * Convert time slot end time to urc before saving to db
     * @param $value
     */
    public function setToTimeAttribute($value){
        $this->attributes['to_time']=Carbon::parse($value)->setTimezone('UTC');
    }
}
