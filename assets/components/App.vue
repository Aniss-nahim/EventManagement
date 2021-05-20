<template>
    <div>
        <h4> Symfony | Vuejs | Bootstrap </h4>
        <button class="btn btn-primary btn-block" @click="counter++"><i class="fas fa-mouse"></i> Click me {{counter}}</button>
    </div>
</template>
<script>
export default {
    name : 'app',
    
    data(){
        return {
            counter : 0
        }
    }
}
</script>
<style scoped>
    
</style>