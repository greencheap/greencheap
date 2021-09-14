<template>
    <div class="uk-section-large uk-text-center uk-text-muted">
        <h2 class="uk-text-capitalize uk-margin-small">{{ title }}</h2>
        <div class="uk-text-meta">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            default: null,
            type: String,
        },
    },
};
</script>
