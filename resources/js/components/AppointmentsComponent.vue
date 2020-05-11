<template>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" v-if="isExpert">User</th>
                    <th scope="col">Start on</th>
                    <th scope="col">End on</th>
                    <th scope="col">Duration</th>
                    <th scope="col" v-if="!isExpert">Expert</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(appointment,index) in appointments" :key="appointment.id">
                    <td v-if="isExpert"> {{appointment.user.name}} </td>
                    <td> {{getTimeInUserTimeZone(appointment.from_time)}} </td>
                    <td> {{getTimeInUserTimeZone(appointment.to_time)}} </td>
                    <td> {{appointment.duration}} Minutes</td>
                    <td v-if="!isExpert"> {{appointment.expert.user.name}} </td>
                    <td> <a class="btn btn-sm btn-danger text-white" @click.prevent="remove(index)">Remove</a> </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import moment_timezone from 'moment-timezone'
    export default {
        name: "AppointmentsComponent",
        props:{
            appointments:Array,
            timezone:{
                type:String,
                default:'UTC'
            },
            isExpert:{
                type:Boolean,
                default:false
            }
        },
        data(){
            return{
                loading:this.$globalStore.data
            }
        },
        methods:{
            getTimeInUserTimeZone:function (time) {
                moment.tz.setDefault('UTC');
                return moment(time).tz(this.timezone).format('YYYY-MM-DD hh:mm A')
            },
            remove:function (index) {
                let self = this;
                this.swalConfirm(function () {
                    self.loading.loading = true;
                    axios.delete('/appointments/'+self.appointments[index].id).then(data => {
                        self.loading.loading = false;
                        self.$swal({
                            position: 'center', type: data.data.status,
                            html: data.data.message,
                            showConfirmButton: true
                        });
                        if (data.data.status === 'success') {
                            self.appointments.splice(index,1);
                        }

                    }).catch(() => {
                        self.loading.loading = false;
                    });
                });
            }
        }
    }
</script>

<style scoped>

</style>
