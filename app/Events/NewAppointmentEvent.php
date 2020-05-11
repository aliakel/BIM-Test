<?php

namespace BeInMedia\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAppointmentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $slots;
    public $appointment;
    public $broadcastQueue = 'bin';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($appointment,$slots)
    {
        $this->slots=$slots;
        $this->appointment=$appointment;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel;

     */
    public function broadcastOn()
    {
        return new Channel('appointment-channel');
    }

    public function broadcastAs()
    {
        return 'slotsChanges';
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'slots' => $this->slots,
            'date' => $this->appointment['from_time'],
            'expert'=>$this->appointment['expert_id'],
            'user'=>$this->appointment['user_id']
        ];
    }
}
