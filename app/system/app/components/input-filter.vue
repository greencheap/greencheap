<template>
    <div class="pk-filter" :class="['uk-form-custom', value ? 'uk-active' : '']" uk-form-custom>
        <span>{{ label }}</span>
        <select v-if="isNumber" v-model.number="select" class="uk-select">
            <template v-for="(option, key) in list">
                <optgroup v-if="option.label" :key="key" :label="option.label">
                    <option v-for="opt in option.options" :key="opt.value" :value="opt.value">
                        {{ opt.text }}
                    </option>
                </optgroup>
                <option v-else :key="key" :value="option.value">
                    {{ option.text }}
                </option>
            </template>
        </select>
        <select v-else v-model="select" class="uk-select">
            <template v-for="(option, key) in list">
                <optgroup v-if="option.label" :key="key" :label="option.label">
                    <option v-for="opt in option.options" :key="opt.value" :value="opt.value">
                        {{ opt.text }}
                    </option>
                </optgroup>
                <option v-else :key="key" :value="option.value">
                    {{ option.text }}
                </option>
            </template>
        </select>
    </div>
</template>

<script>

export default {

    props: ['title', 'value', 'options', 'number'],

    data() {
        return {
            select: this.value,
        };
    },

    computed: {

        isNumber() {
            return (typeof this.number !== 'undefined');
        },

        list() {
            return [{ value: '', text: this.title }].concat(this.options);
        },

        label() {
            const list = this.list.concat(_.flatten(_.map(this.list, 'options')));
            const value = _.find(list, { value: this.select });
            return value ? value.text : this.title;
        },

    },

    watch: {
        select(value) {
            this.$emit('input', value);
        },

        value(value) {
            this.select = value;
        },
    },

    created() {
        if (this.value === undefined) {
            this.select = '';
        }
    },

};

</script>
