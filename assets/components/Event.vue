<template>
    <div class="row justify-content-center mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row align-items-center justify-content-left">
                        <div class="col-1">
                            <img class="rounded-circle border-success" style="height: 40px; width: 40px;" :src="userImage" alt="event creator" />
                        </div>
                        <div class="col-9">
                            <a class="lead m-0 ml-2 text-decoration-none text-dark" :href="'/profile/'+event.owner.id">{{userName}}</a>
                            <small class="m-0 ml-2"><i class="fas fa-clock"></i> {{ createdAt }}</small><br/>
                        </div>
                        <div class="col-1">
                            <a class="nav-link text-dark" href="#" :id="'explore-' + event.id" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg-right" :aria-labelledby="'explore-'+event.id">
                                <a class="dropdown-item" :href="'/event/'+event.id">View</a>
                                <div v-if="myEvent">
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" @click="unpublish(event.id)"> Unpublish</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="lead m-0 ml-2"> <i class="fas fa-bullhorn fa-xs"></i> <a :href="'/event/'+event.id" class="text-decoration-none text-success"><strong>{{ event.title }}</strong> </a> </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <img class="img-fluid" :src="eventCover" alt="" />
                    <div class="alert m-2" role="alert">
                        <div class="row">
                            <div class="col-sm-12">
                                <h6> <i class="fas fa-map-marker-alt"></i> {{event.address}}</h6>
                            </div>
                            <div class="col-12">
                                <span v-for="(eventTag, index) in event.eventTags" :key="eventTag.id" class="badge badge-pill badge-success mr-2">
                                    {{eventTag.tag.tagName}}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="card-footer bg-white">
                    <div class="row justify-content-btween m-2">
                        <div class="col-sm-6">
                            <p> <i class="fas fa-calendar-day pr-2"></i><strong>Starts On : </strong>{{ startDate }}</p>
                        </div>
                        <div class="col-sm-6 text-right">
                            <p> <i class="fas fa-calendar-day pr-2"></i><strong>Ends On : </strong> {{ endDate }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    var moment  = require('moment');
    import axios from 'axios';
    import {eventListener} from '../app';

    export default {
        name : 'Event',
        props : {
            uid : String,
            event : Object,
            userimages : String,
            eventcovers: String
        },
        
        data(){
            return{

            }
        },

        created(){
            
        },

        methods : {
            unpublish(eventId){
                axios.get('/event/unpublish/'+eventId)
                .then(response => {
                    eventListener.$emit('show-toast', {
                        title : 'Unpublished',
                        message : 'Your event is currently unpublished',
                        color : "success"
                    });
                    this.$emit('removeEvent', eventId);
                })
                .catch(error => {
                    eventListener.$emit('show-toast', {
                        title : 'Error',
                        message : 'Please try again later',
                        color : "danger"
                    })
                })
            }
        },

        computed : {
            createdAt : function(){
                return moment(this.event.createdAt).fromNow();
            },

            startDate : function(){
                return moment(this.event.startDate).format('YYYY-MM-DD');
            },

            endDate : function(){
                return moment(this.event.endDate).format('YYYY-MM-DD');
            },

            userName : function(){
                return this.event.owner.lastName+' '+this.event.owner.firstName;
            },

            userImage : function(){
                return this.userimages+'/'+this.event.owner.image;
            },

            eventCover : function(){
                return this.eventcovers+'/'+this.event.coverImage;
            },

            myEvent : function(){
                return parseInt(this.uid) == this.event.owner.id
            }

        },
        
    }
</script>

<style>

</style>