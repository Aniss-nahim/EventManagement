<template>
    <div>
        <div class="modal-body">
            <h3>Create Event</h3>
            <hr class="text-success">
            <div v-if="error">
                <div class="alert alert-danger" role="alert">
                    {{message}}
                    <div v-if="!isValid">
                        <hr>
                        {{validationMessages.title}}
                        <ul v-for="(violation, index) in validationMessages.violations" :key="index">
                            <li>
                                {{violation.propertyPath}} : {{violation.title}}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <form v-on:submit.prevent="submitEvent">
                <div class="container">
                    <input type="hidden" v-model="csrf">
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="title"> Title </label>
                            <input type="text" class="form-control" v-model="event.title" id="title" required/>
                        </div>
                        <div class="form-group col-6">
                            <label for="type"> Type </label>
                            <input type="text" class="form-control" id="type" v-model="event.type" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="description"> Description </label>
                            <textarea class="form-control" v-model="event.description" maxlength="255" id="description" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="startDate"> Starts On </label>
                            <input type="datetime-local" step="1" class="form-control" id="startDate" v-model="event.startDate" required/>
                        </div>
                        <div class="form-group col-6">
                            <label for="endDate"> Ends On </label>
                            <input type="datetime-local" step="1" class="form-control" id="endDate" v-model="event.endDate" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="street"> Street </label>
                            <input type="text" class="form-control" id="street" v-model="event.street" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="city"> City </label>
                            <input type="text" class="form-control" id="city" v-model="event.city" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="country"> Country </label>
                            <input type="text" class="form-control" id="country" v-model="event.country" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="zipCode"> Zip Code </label>
                            <input type="number" class="form-control" id="zipCode" v-model="event.zipCode"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-on:click="resetState" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" :disabled="loading">
                            Save
                            <span v-if="loading" class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    var moment = require('moment');
    import axios from 'axios';
    import {eventListener} from '../app';

export default {
    name : 'CreateEvent',
    props : {
        csrf: String,
        createeventurl : String
    },
    
    data(){
        return{
            error : false,
            loading : false,
            isValid : true,
            validationMessages : {},
            message : "",
            event :  {
                title : "",
                type : "",
                description : "",
                startDate : "",
                endDate : "",
                state : "Default state",
                street: "",
                country : "",
                city : "",
                zipCode: "",
                address : ""
            }
        }
    },

    methods :{

        buildAddress(){
            this.event.address =  `${this.event.street}, ${this.event.city} ${this.event.zipCode}, ${this.event.country}`;
        },

        validate(){
            this.event.startDate = moment(this.event.startDate);
            this.event.endDate = moment(this.event.endDate);

            let diffrenceInTime = this.event.endDate.diff(this.event.startDate);
            this.buildAddress();

            if(diffrenceInTime > 0 && this.event.address !== "" && this.event.city != ""){
                return true;
            }
            return false;
        },

        submitEvent(){
            if(this.validate()){
                this.loading = true;
                // 
                axios.post(this.createeventurl, this.event)
                    .then(reponse => {
                        this.resetState();
                        $("#createEventForm").modal('hide');
                        eventListener.$emit('show-toast', {
                            title : 'New Event created',
                            date : reponse.data.createdAt,
                            message : 'Your event is created successfully .',
                            color : "success"
                        })
                    })
                    .catch(error => {
                        this.loading = false;
                        if (error.response.status === 400){
                            this.displayValidation(error.response.data.data);
                        }
                        this.displayError("Faild to create event.");
                    });
                }else{
                    this.displayError("Please enter a correct date and time.");
                }
        },

        displayError(message){
            this.error = true;
            this.message = message;
        },

        displayValidation(validationObj){
            this.isValid = false;
            this.validationMessages = validationObj;
        },

        resetState(){
            this.isValid = true;
            this.loading = false;
            this.error = false;
            this.message = "";
            this.validationMessages = {};
        }
    }
}
</script>

<style scoped>

</style>