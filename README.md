## Booking project


## Features
- Multiple timezone
- Auto detect timezone using ip
- User can choose duration of meeting
- Auto calculation for time slots according to duration
- Using pusher to Broadcast new timeslots to all users who book same expert on a same day, So all used 
timeslots will disappear. 
- Show times and date in user timezone
- User can list his appointment and delete them.
- duration can be any number between 1 and 60
- Auth 

## Techniques
- Cache models
- Use queue to broadcast event
- Use Pusher and laravel echo to broadcast new timeslots after booking an appointment
- Use Vuejs (Inject components in blade)

## To Do (I need some time)
- Unit test 
- Appointments management by expert.
- Improve layout
- Add localization
