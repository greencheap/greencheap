<template>
    <div uk-modal :class="modalCls">
        <div class="uk-modal-dialog" :class="dialogCls">
            <slot v-if="opened">
            </slot>
        </div>
    </div>
</template>

<script>

import { on } from 'uikit-util';

export default {

    props: {
        large     : Boolean,
        lightbox  : Boolean,
        modalFull : Boolean,
        modalSmall: Boolean,
        widthAuto : Boolean,
        center    : Boolean,
        contrast  : Boolean,
        bgClose   : Boolean,
        escClose  : Boolean,
        closed    : Function,
        modifier: { type: String, default: '' },
        options: {
            type: Object,
            default() {
                return {};
            },
        },
    },

    data() {
        return {
            opened: false
        };
    },

    computed: {

        modalCls() {
            const modalCls = this.modifier.split(' ');

            if (this.large) {
                modalCls.push('uk-modal-container');
            }

            if (this.modalFull) {
                modalCls.push('uk-modal-full');
            }

            if (this.modalSmall) {
                modalCls.push('tm-modal-small');
            }

            return modalCls;
        },

        dialogCls() {
            const dialogCls = [];

            if (this.center) {
                dialogCls.push('uk-margin-auto-vertical');
            }

            if (this.contrast) {
                dialogCls.push('uk-light');
            }

            if (this.widthAuto) {
                dialogCls.push('uk-width-auto');
            }

            return dialogCls;
        },

    },

    mounted() {
        const vm = this;

        this.modal = UIkit.modal(this.$el, _.extend({
            bgClose  : !this.bgClose,
            escClose : !this.escClose,
            stack    : true,
        }, this.options));

        on(this.modal.$el, 'hidden', (ref) => {

            vm.opened = false;

            if (this.closed) {
                this.closed();
            }

        });
    },

    methods: {

        open() {
            this.opened = true;
            this.modal.show();
        },

        close() {
            this.modal.hide();
        },

    },

};

</script>
