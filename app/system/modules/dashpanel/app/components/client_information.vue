<template>
    <div>
        <div>
            <span v-show="isDeveloper" class="uk-label version-dev">Developer Version</span>
        </div>
    </div>
</template>

<script>
    export default {
        section:{
            label:'Client Information',
            priority:0
        },

        data(){
            return {
                isDeveloper: window.$client.system.isDeveloper
            }
        },
    }
</script>
<style>
    .version-dev{
        background: #fb256a !important;
        color: white !important;
        font-size: 10px;
        padding: 2px 5px;
        font-weight: bold;
        text-transform: capitalize;
    }
</style>