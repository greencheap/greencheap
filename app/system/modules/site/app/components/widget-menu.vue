<template>
    <div class="pk-grid-large pk-width-sidebar-large" uk-grid>
        <div class="pk-width-content uk-form-horizontal">
            <div class="uk-margin">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            	<v-input id="form-title" type="text" name="title" placeholder="Enter Title" view="class: uk-form-width-large uk-input" rules="required" v-model="widget.title" message="Title cannot be blank."/>
            </div>

            <div class="uk-margin">
                <label for="form-menu" class="uk-form-label">{{ 'Menu' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-menu" v-model="widget.data.menu" class="uk-form-width-large uk-select">
                        <option value="">
                            {{ '- Menu -' | trans }}
                        </option>
                        <option v-for="m in menus" :key="m.id" :value="m.id">
                            {{ m.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-level" class="uk-form-label">{{ 'Start Level' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-level" v-model="widget.data.start_level" class="uk-form-width-large uk-select" number>
                        <option value="1">
                            1
                        </option>
                        <option value="2">
                            2
                        </option>
                        <option value="3">
                            3
                        </option>
                        <option value="4">
                            4
                        </option>
                        <option value="5">
                            5
                        </option>
                        <option value="6">
                            6
                        </option>
                        <option value="7">
                            7
                        </option>
                        <option value="8">
                            8
                        </option>
                        <option value="9">
                            9
                        </option>
                        <option value="10">
                            10
                        </option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-depth" class="uk-form-label">{{ 'Depth' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-depth" v-model="widget.data.depth" class="uk-form-width-large uk-select" number>
                        <option value="">
                            {{ 'No Limit' | trans }}
                        </option>
                        <option value="1">
                            1
                        </option>
                        <option value="2">
                            2
                        </option>
                        <option value="3">
                            3
                        </option>
                        <option value="4">
                            4
                        </option>
                        <option value="5">
                            5
                        </option>
                        <option value="6">
                            6
                        </option>
                        <option value="7">
                            7
                        </option>
                        <option value="8">
                            8
                        </option>
                        <option value="9">
                            9
                        </option>
                        <option value="10">
                            10
                        </option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Sub Items' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-margin-small">
                        <label><input v-model="widget.data.mode" class="uk-radio" type="radio" value="all"><span class="uk-margin-small-left">{{ 'Show all' | trans }}</span></label>
                    </p>

                    <p class="uk-margin-small">
                        <label><input v-model="widget.data.mode" class="uk-radio" type="radio" value="active"><span class="uk-margin-small-left">{{ 'Show only for active item' | trans }}</span></label>
                    </p>
                </div>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <component :is="'template-settings'" :widget.sync="widget" :config.sync="config" :form="form" />
        </div>
    </div>
</template>

<script>

var WidgetMenu = {

    section: {
        label: 'Settings',
    },

    props: ['widget', 'config', 'form'],

    data() {
        return {
            menus: {},
        };
    },

    inject: ['$components'],

    created() {
        _.extend(this.$options.components, this.$components);

        this.$http.get('api/site/menu').then(function (res) {
            this.$set(this, 'menus', res.data.filter(menu => menu.id !== 'trash'));
        });
    },

};

export default WidgetMenu;

window.Widgets.components['system-menu.settings'] = WidgetMenu;

</script>
