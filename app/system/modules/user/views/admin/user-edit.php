<?php $view->script('user-edit', 'system/user:app/bundle/user-edit.js', ['vue']) ?>

<validation-observer tag="form" id="user-edit" class="uk-form-horizontal" ref="observer" @submit.prevent="submit" v-cloak>

    <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap" >
        <div >

            <h2 class="uk-margin-remove" v-if="user.id">{{ 'Edit User' | trans }}</h2>
            <h2 class="uk-margin-remove" v-else>{{ 'Add User' | trans }}</h2>

        </div>
        <div class="uk-margin">

            <a v-if="!processing" class="uk-button uk-button-text uk-margin-right" :href="$url.route('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit" :disabled="processing">
                <span v-if="processing" uk-spinner ratio=".8" class="uk-margin-small-right"></span>
                <span class="uk-text-middle">{{ 'Save' | trans }}</span>
            </button>
        </div>
    </div>

    <ul ref="tab" id="user-tab">
        <li v-for="section in sections"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div class="uk-switcher uk-margin" ref="content" id="user-content">
        <div v-for="(section, key) in sections" :key="key">
            <component :is="section.name" :user="user" :config="config" :form="form"></component>
        </div>
    </div>

</validation-observer>
