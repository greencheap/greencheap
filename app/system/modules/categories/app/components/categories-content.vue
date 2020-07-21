<template>
    <div>
        Hello World
    </div>
</template>

<script>
export default {
    // eslint-disable-next-line vue/require-prop-types
    props: ['categories', 'category'],
    section: {
        label: 'Section',
        priority: 10,
    },
};
</script>
