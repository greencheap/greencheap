<template>
    <div class="uk-child-width-1-3" uk-grid>
        <div v-for="(nav , id) in navs" :key="id">
            <a :href="$url.route(nav.url)" class="tm-navbar-href" :class="{'tm-navbar-active':nav.active}">
                <div class="tm-navbar-icon">
                    <img :data-src="nav.icon" width="100%" uk-img>
                </div>
                <span class="tm-navbar-item uk-text-truncate">{{nav.label}}</span>
            </a>
        </div>
    </div>
</template>

<script>
    export default{
        props:['navs'],
    }
</script>