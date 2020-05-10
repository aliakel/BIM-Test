<template>
    <div class="loader-wrapper" v-if="loading.loading">
        <div class="loader" >
        </div>
    </div>
</template>

<script>
    export default {
        name: "HelperLoader",
        data(){
            return {
                loading:this.$globalStore.data
            }
        }
    }
</script>
