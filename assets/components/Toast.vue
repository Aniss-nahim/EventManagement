<template>
    <div>
        <div class="toast" id="toast-container" role="alert" aria-live="polite" aria-atomic="true" data-delay="6000" style="position: fixed; top: 70px; right: 40px;">
            <div role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header" :class="color">
                    <strong class="mr-auto">{{title}}</strong>
                    <small>{{createdAt}}</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body bg-light">
                    {{message}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    var moment = require('moment');
    import {eventListener} from '../app';
    export default {
        name : 'Toast',

        data(){
            return {
                title : "",
                message : "",
                createdAt : "",
                color : ""
            }
        },
        created(){
            eventListener.$on('show-toast', (ToastObj) => {
                this.title = ToastObj.title;
                this.message = ToastObj.message;
                this.color = 'text-'+ToastObj.color;
                this.created = moment(ToastObj.created).fromNow();
                $('.toast').toast('show')
            })
        },
    }
</script>

<style scoped>
  #toast-container{
      z-index: 99999 !important;
  }
</style>