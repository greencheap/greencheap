<template>
    <div>
        <a class="tm-icon-visit" :href="$url.route('')" :title="'Visit Site' | trans " target="_blank"/>
    </div>
</template>

<script>
export default {
    section: {
        label: 'Visit',
        priority: 90,
    },
}
</script>
