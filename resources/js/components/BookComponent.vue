<template>
    <div class="card">
        <div class="card-body">
            <div class="form-group d-flex flex-column align-items-center">
                <label class="h3">Select Day</label>
                <datepicker :disabledDates="{ to: new Date(start_date - 8640000) }"
                            :inline="true"
                            @input="form.day = customFormatter($event)"
                            class="m-auto"
                            :value="form.day">

                </datepicker>
                <p class="h5 py-4 text-center">Timezone: {{timezone}}</p>
                <p class="h5 py-2">Your time: {{customFormatter()}}</p>
            </div>
            <div class="form-group">
                <label class="h4">Duration</label>
                <select @change="duration_slots=times_slots[form.duration]" class="form-control" name="duration"
                        v-model="form.duration">
                    <option value="15">15 minutes</option>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">1 hours</option>
                </select>
            </div>
            <div class="form-group">
                <label class="h4">Timeslot</label>
                <select @change="setSlot($event)"  class="form-control" name="slot">
                    <option :selected="!form.from_time" value="">Select time slot</option>
                    <option :value="index" v-for="(slot,index) in duration_slots">
                        {{slot.slot}}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="rang.start && rang.end">
                {{'Your appointment will be on '+formattedDate()+' from '+rang.start+' to '+rang.end}}
            </div>
            <div class="form-group text-center">
                <button @click="saveAppointment" class="btn btn-md btn-primary">Submit</button>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import moment_timezone from 'moment-timezone'

    export default {
        props: {
            slots: Object,
            other: Object
        },
        components: {
            Datepicker
        },
        watch:{
            'form.day':function (oldVal,newVal) {
                if(newVal)
                this.getSlots();
            }
        },
        data() {
            return {
                form: {
                    duration: 60,
                    day: '',
                    from: '',
                    to: ''
                },
                times_slots: [],
                duration_slots: [],
                start_date: '',
                timezone: '',
                loading:this.$globalStore.data,
                rang:{start:'',end:''}
            }
        },
        methods: {
            setSlot: function (e) {
                let index = e.target.value;
                this.form.from_time = this.duration_slots[index].start;
                this.form.to_time = this.duration_slots[index].end;
                this.rang.start=this.duration_slots[index].start_slot;
                this.rang.end=this.duration_slots[index].end_slot;
            },
            reset:function(slots){
                this.times_slots = slots;
                this.duration_slots = this.times_slots[this.form.duration];
                this.form.from_time='';
                this.form.to_time='';
                this.checkIfSlotsAvailable();
            },
            getSlots: function () {
                let self = this;
                if (self.form.day) {
                    self.loading.loading = true;
                    axios.post('/book/' + self.form.expert_id, {date: self.form.day}).then(data => {
                        if (data.data.status === 'success') {
                           self.reset(data.data.data);
                        }
                        self.loading.loading = false;
                    }).catch(() => {
                        self.loading.loading = false;
                    });
                }

            },
            formattedDate:function(){
                return moment(this.form.from_time,'YYYY-MM-DD hh:mm:ss').tz(this.other.timezone).format('DD MMM YYYY');
            },
            customFormatter(date='') {
                return date?moment(date).tz(this.other.timezone).format():moment().tz(this.other.timezone).format('YYYY MM DD hh:mm A');
            },
            saveAppointment() {
                let self = this;
                if (!this.form.from_time || !this.form.to_time) {
                    self.$swal({
                        position: 'center',
                        icon: 'error',
                        html: 'Please choose time slot',
                        showConfirmButton: true
                    });
                    return;
                }
                self.loading.loading = true;
                axios.post('/appointments', self.form).then(data => {
                    if (data.data.status === 'success') {
                        self.$swal({
                            title: data.data.message,
                            type: data.data.status,
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.value) {
                                window.location.href = data.data.data;
                            }
                        });
                    }else if (data.data.status === 'warning') {
                        self.loading.loading = false;
                        self.$swal({
                            position: 'center',
                            icon: data.data.status,
                            html: data.data.message,
                            showConfirmButton: true
                        });
                        self.reset(data.data.data);
                    }

                }).catch((e) => {
                    self.loading.loading=false;
                });
            },
            updateTimeSlots(duration) {
                let slots_in_user_timezone = {15: [], 30: [], 45: [], 60: []};
                let self = this;
                Object.keys(duration).forEach(function (key, index) {
                    duration[key].forEach(function (slot, ind) {
                        moment.tz.setDefault('UTC');
                        let start=moment.tz(moment(slot.start_slot, 'hh:mm').tz('UTC'), self.other.timezone).format('YYYY-MM-DD hh:mm:ss.uP');
                        let end=moment.tz(moment(slot.end_slot, 'hh:mm').tz('UTC'), self.other.timezone).format('YYYY-MM-DD hh:mm:ss.uP');
                        let start_slot=moment.tz(moment(slot.start_slot, 'hh:mm').tz('UTC'), self.other.timezone).format('hh:mm A');
                        let end_slot=moment.tz(moment(slot.end_slot, 'hh:mm').tz('UTC'), self.other.timezone).format('hh:mm A');
                        slots_in_user_timezone[key].push({
                            'start':start,
                            'end': end,
                            'slot':start_slot+' - '+end_slot,
                            'start_slot':start_slot,
                            'end_slot':end_slot
                        });
                    });
                });

                return slots_in_user_timezone;
            },
            checkIfSlotsAvailable(){
                if(!this.times_slots[15].length){
                    this.$swal({
                        position: 'center',
                        html: 'No time slot available to book tody, Please check next day',
                        icon: 'warning',
                        showConfirmButton: true
                    });
                }
            }
        },
        created(){
            this.start_date = moment().tz(this.other.timezone);
            this.form.day = this.start_date.format();
        },
        mounted() {
            if (this.other.user_id) this.form.user_id = this.other.user_id;

            if (this.other.expert_id) this.form.expert_id = this.other.expert_id;

            this.timezone = this.other.visitor_tz;

            this.times_slots = this.slots;

            this.duration_slots = this.slots[this.form.duration];

            this.checkIfSlotsAvailable();

            let self = this;
            if (typeof this.$echo !== "undefined") {
                this.$echo.channel('appointment-channel')
                    .listen('.slotsChanges', (message) => {

                        let dt = moment.tz(message.date, self.other.timezone).format('YYYY-MM-DD');
                        let formattedDate = moment(self.form.day).format('YYYY-MM-DD');
                        if (message.user!=window.user_id && dt === formattedDate && message.expert == self.form.expert_id) {
                            self.reset(self.updateTimeSlots(message.slots));
                            self.$swal({
                                position: 'center',
                                html: 'Timeslots changed',
                                icon: 'warning',
                                showConfirmButton: true
                            });
                        }

                    });
            }
        }
    }
</script>
