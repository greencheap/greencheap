<?php $view->script('categories-edit' , 'system/categories:app/bundle/categories-edit.js' , 'vue') ?>

<validation-observer tag="form" id="app" ref="observer" @submit.prevent="submit" v-cloak>

    <div class="uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
        <div class="">

            <h2 class="uk-margin-remove" v-if="category.id">{{ 'Edit %type%' | trans({type:type.label}) }}</h2>
            <h2 class="uk-margin-remove" v-else>{{ 'Add %type%' | trans({type:type.label}) }}</h2>

        </div>
        <div class="uk-margin">

            <a v-if="!processing" class="uk-button uk-button-text uk-margin-right" :href="$url.route('admin/site/page')">{{ category.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit" :disabled="processing">
                <span v-if="processing" uk-spinner ratio=".8" class="uk-margin-small-right"></span>
                <span class="uk-text-middle">{{ 'Save' | trans }}</span>
            </button>

        </div>
    </div>

    <ul ref="tab" v-show="sections.length > 1" id="tab">
        <li v-for="section in sections" :key="section.name"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div ref="content" class="uk-switcher uk-margin" id="content">
        <div v-for="section in sections" :key="section.name">
            <component :is="section.name" :category.sync="node" :roles.sync="roles"></component>
        </div>
    </div>

</validation-observer>
