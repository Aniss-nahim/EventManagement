<template>
    <div class="card filter fixed p-2">
        <p class="lead text-center">What event are you interested in ?</p>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="titrefilter"><i class="fas fa-search"></i> Title </label>
                    <input type="text" id="titrefilter" placeholder="Title" v-model="params.title" class="form-controle-sm form-control rounded-pill" maxlength="100">
                </div>
            </div>
            <div class="col-8">
                <div class="form-group">
                    <label for="orderByfilter"><i class="fas fa-sort-amount-up-alt"></i> Order By </label>
                     <select class="custom-select mr-sm-2 rounded-pill" v-model="params.orderBy" id="orderByfilter">
                        <option selected value="createdAt">Publcation date</option>
                        <option value="title">Title</option>
                        <option value="startDate">Start date</option>
                        <option value="endDate">End date</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="orderByfilter"><i class="fas fa-sort-amount-up-alt"></i></label>
                     <select class="custom-select rounded-pill mr-sm-2" v-model="params.order" id="orderByfilter">
                        <option selected value="DESC">Descendant</option>
                        <option value="ASC">Ascendant</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="tagsfilter"><i class="fas fa-tag"></i> Tags </label>
                    <input type="text" name="tagsfilter" v-model="params.tags" class="form-control form-controle-sm tagin rounded-pill" data-placeholder="Type Tags Here" data-separator=" "/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="startDatefilter"><i class="fas fa-calendar-day"></i> Starts On </label>
                    <input type="date" id="startDatefilter" v-model="params.startDate" class="form-controle-sm form-control rounded-pill">
                </div>
            </div>

             <div class="col-6">
                <div class="form-group">
                    <label for="typefilter"><i class="fas fa-filter"></i>Event type </label>
                    <input type="text" id="typefilter" v-model="params.type" placeholder="Type" class="form-controle-sm form-control rounded-pill">
                </div>
            </div>
        </div>

         <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="endDatefilter"><i class="fas fa-calendar-day"></i> Ends On </label>
                    <input type="date" id="endDatefilter" v-model="params.endDate" class="form-controle-sm form-control rounded-pill">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="cityfilter"><i class="fas fa-location-arrow"></i> City </label>
                    <input type="text" id="cityfilter" v-model="params.city" placeholder="City" class="form-controle-sm form-control rounded-pill">
                </div>
            </div>
        </div>
        
        <div class="row">
           <div class="col-6">
                <div class="form-group">
                    <button class="btn btn-success btn-block rounded-pill" v-on:click="filter" :disabled="loading">
                        Filter
                        <span v-if="loading" class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                        <span class="sr-only">Loading...</span>
                    </button>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block rounded-pill" v-on:click="filterClear">Clear</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import {eventListener} from '../app';
    var moment = require('moment');

    export default {
        name : 'FilterEvent',
        props : {
            filtereventurl : String
        },

        data(){
            return {
                loading : false,
                params : {
                    title : "",
                    city : "",
                    type : "",
                    startDate : "",
                    endDate : "",
                    tags : ["All"],
                    orderBy : "createdAt",
                    order: "DESC"
                }
            }
        },

        created(){
            this.tagsString = this.params.tags.join(' ');
        },

        mounted(){
            for (const el of document.querySelectorAll('.tagin')) {
                el.value = this.params.tags[0];
                tagin(el);
            }
        },

        methods : {
            filter(){   
                // set tags
                const tags = document.querySelector('.tagin');
                this.params.tags = tags.value.split(' ');
                this.loading = true;
                // validate date
                if(this.dateValidation()){
                    axios.get(this.filtereventurl, { params: this.params })
                        .then(response => {
                            this.loading = false;
                            this.$emit('filter', response.data.data);
                            this.notify({
                                title : 'Notification',
                                date : "",
                                message : response.data.count+' event filtred.',
                                color : "success"
                            });
                        })
                        .catch(error => {
                            console.log(error);
                            this.notify({
                                title : 'Error',
                                date : "",
                                message : 'Ooop !! something went wrong, please try again later.',
                                color : "danger"
                            });
                            this.loading = false;
                        })
                }

            },

            filterClear(){
                this.params = {
                    title : "",
                    city : "",
                    type : "",
                    startDate : "",
                    endDate : "",
                    tags : ["All"],
                    orderBy : "createdAt",
                    order : "DESC"
                }
                this.$emit('clear');
            },

            dateValidation(){
                if(this.params.startDate !== "" && this.params.endDate !== ""){
                    let statDate = moment(this.params.startDate);
                    let endDate = moment(this.params.endDate);
    
                    if(endDate.diff(startDate) < 0){
                        this.notify({
                            title : 'Error: Incorrect date',
                            date : "",
                            message : 'Your filtring date is incorrect.',
                            color : "danger"
                        });
                        this.loading = false;
                        return false;
                    }
                }
                return true;
            },

            notify(msg){
                 eventListener.$emit('show-toast', msg);
            }
        }
    }
</script>

<style scoped>
    div.filter{
        z-index: -100 !important;
    }
</style>