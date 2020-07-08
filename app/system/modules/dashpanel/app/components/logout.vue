<template>
    <div>
        <li><a class="tm-icon-logout" :href="$url('user/logout' , {redirect:'admin/login'})" :title="'Logout' | trans "></a></li>
    </div>
</template>

<script>
    export default {
        section:{
            label:'Logout',
            priority:110
        }
    }
</script>