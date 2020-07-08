<template>
    <div>
        <v-input :id="id"
            :name="name"
            type="text"
            :rules="{required: isRequired}"
            :view="view"
            v-model.lazy="link"
            :message="required"
        />

        <div v-show="url" class="uk-text-muted uk-text-small">
            {{ url }}
        </div>

        <div uk-modal ref="modal">
            <div class="uk-modal-dialog">
                <form class="uk-margin" @submit.prevent="update">
                    <div class="uk-modal-header">
                        <h2>{{ 'Select Link' | trans }}</h2>
                    </div>

                    <div class="uk-modal-body">
                        <panel-link ref="links"></panel-link>
                    </div>

                    <div class="uk-modal-footer uk-text-right">
                        <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                            {{ 'Cancel' | trans }}
                        </button>
                        <button class="uk-button uk-button-primary" type="submit" :disabled="!showUpdate()" autofocus="">
                            {{ 'Update' | trans }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import VInput from 'SystemApp/components/validation.vue';

export default {

    name: 'input-link',

    props: ['name', 'inputClass', 'id', 'required', 'value'],

    data() {
        return {
            link: this.value,
            url: false,
            isMounted: false,
            view: {
                type: 'icon',
                class: ['uk-input', this.inputClass],
                icon: 'link',
                iconClick: this.open,
                iconTag: 'a',
                iconDir: 'right',
                iconLabel: 'Select'
            }
        };
    },

    watch: {

        link: {
            handler: 'load',
            immediate: true,
        }

    },

    computed: {

        isRequired() {
            return this.required !== undefined;
        }

    },

    mounted() {
        this.modal = UIkit.modal(this.$refs.modal, {escClose: true, bgClose: false, stack: true});
        this.isMounted = true;
    },

    methods: {

        load() {
            if (this.link) {
                this.$http.get('api/site/link', { params: { link: this.link } }).then(function (res) {
                    this.url = res.data.url || false;
                }, function () {
                    this.url = false;
                });
            } else {
                this.url = '';
            }
        },

        open() {
            this.modal.show();
        },

        update() {
            this.$set(this, 'link', this.$refs.links.link);
            this.$emit('input', this.link);
            this.modal.hide();
        },

        showUpdate: function () {
            return this.isMounted && this.$refs.links && this.$refs.links.link;
        }

    },

    components: {
        VInput
    }

};

Vue.component('input-link', (resolve) => {
    resolve(require('./input-link.vue'));
});

</script>
