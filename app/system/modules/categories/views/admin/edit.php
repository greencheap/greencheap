<?php $view->script('categories-edit' , 'system/categories:app/bundle/categories-edit.js' , ['vue' , 'editor']) ?>

<validation-observer tag="form" id="app" ref="observer" @submit.prevent="submit" v-cloak>
    <div class="uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
        <div class="">
            <h2 class="uk-margin-remove" v-if="category.id">{{ 'Edit' }}</h2>
            <h2 class="uk-margin-remove" v-else>{{ 'Add' }}</h2>
        </div>
        <div class="uk-margin">
            <a v-if="data.redirect" class="uk-button uk-button-text uk-margin-right" :href="$url.route(data.redirect)">{{ category.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">
                {{ 'Save' | trans }}
            </button>
        </div>
    </div>
    <ul ref="tab" v-show="sections.length > 1" id="tab">
        <li v-for="section in sections" :key="section.name"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div ref="content" class="uk-switcher uk-margin" id="content">
        <div v-for="section in sections" :key="section.name">
            <component :is="section.name" :category.sync="category" :data.sync="data"></component>
        </div>
    </div>

</validation-observer>
