<?php $view->script("site-edit", "system/site:app/bundle/edit.js", ["vue", "editor", "uikit"]); ?>

<validation-observer tag="form" id="site-edit" ref="observer" @submit.prevent="submit" v-cloak>

    <div class="uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
        <div>
            <v-title v-if="node.id" :title="'Edit %type%' | trans({type:type.label})"></v-title>
            <v-title v-else :title="'Add %type%' | trans({type:type.label})"></v-title>
        </div>
        <div class="uk-margin">

            <a v-if="!processing" class="uk-button uk-button-text uk-margin-right" :href="$url.route('admin/site/page')">{{ node.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit" :disabled="processing">
                <span v-if="processing" uk-spinner ratio=".8" class="uk-margin-small-right"></span>
                <span class="uk-text-middle">{{ 'Save' | trans }}</span>
            </button>

        </div>
    </div>

    <ul ref="tab" v-show="sections.length > 1" id="page-tab">
        <li v-for="section in sections" :key="section.name"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div ref="content" class="uk-switcher uk-margin" id="page-content">
        <div v-for="section in sections" :key="section.name">
            <component :is="section.name" :node.sync="node" :roles.sync="roles"></component>
        </div>
    </div>

</validation-observer>
