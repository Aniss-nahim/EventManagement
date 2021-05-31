<template>
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <div v-if="loading"> 
                <div class="row justify-content-center align-items-center mt-5">
                    <div class="col-sm-12 text-center">
                        <div class="spinner-grow text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="events.length > 0" v-for="event in events" :key="event.id" class="row justify-content-center mb-3">
                <Event :event="event" :userimages="userimages" :uid="userid" :eventcovers="eventcovers" @removeEvent="remove"/>
            </div>
            <div v-else>
                <div class="mt-5 row justify-content-center align-items-center">
                    <div class="col-sm-12 mt-5">
                        <h3 class="text-center">Let's create that event</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-5">
            <FilterEvent :filtereventurl="filtereventurl" @filter="eventsUpdated" @clear="getEvents"></FilterEvent>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import Event from './Event';
    import FilterEvent from './FilterEvent';
    import {eventListener} from '../app';

    export default {
        name : 'Events',
        props : {
            eventsurl : String,
            userimages : String,
            eventcovers : String,
            filtereventurl : String,
            userid : String
        },
        components : {
            Event,
            FilterEvent,
        },
        
        data(){
            return {
                loading : true,
                events : [],
            }
        },

        created(){
           this.getEvents();
        },

        methods : {
            notify(msg){
                 eventListener.$emit('show-toast', msg);
            },

            eventsUpdated(events){
                this.events = events;
            },

            getEvents(){
                axios.get(this.eventsurl)
                .then(response => {
                    this.loading = false;
                    this.events = response.data.data;
                })
                .catch(error =>{
                    this.loading  = false;
                    this.notify({
                        title : 'Error',
                        date : "",
                        message : 'Ooop !! something went wrong, please try again later.',
                        color : "danger"
                    });
                })
            },

            remove(eventId){
               this.events = this.events.filter( event => event.id != eventId);
            }
        }
    }
</script>
<style scoped>

</style>