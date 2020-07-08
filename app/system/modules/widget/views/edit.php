<?php $view->script('widget-edit', 'system/widget:app/bundle/edit.js', ['widgets', 'editor', 'input-tree']) ?>

<validation-observer tag="form" id="widget-edit" ref="observer" @submit.prevent="submit" v-cloak>

    <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
        <div class="">

            <h2 class="uk-margin-remove" v-if="widget.id">{{ 'Edit Widget' | trans }}</h2>
            <h2 class="uk-margin-remove" v-else>{{ 'Add Widget' | trans }}</h2>

        </div>
        <div class="uk-margin">

            <a v-if="!processing" class="uk-button uk-button-text uk-margin-right" href="<?= $view->url('@site/widget') ?>">{{ widget.title ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit" :disabled="processing">
                <span v-if="processing" uk-spinner ratio=".8" class="uk-margin-small-right"></span>
                <span class="uk-text-middle">{{ 'Save' | trans }}</span>
            </button>
        </div>
    </div>

    <ul ref="tab" v-show="sections.length > 1" id="widget-tab">
        <li v-for="section in sections" :key="section.name" :id="section.label | lowercase"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div class="uk-switcher uk-margin" ref="content" id="widget-content">
        <div v-for="section in sections" :key="section.name">
            <component :is="section.name" :widget.sync="widget" :config="config" :form="form"></component>
        </div>
    </div>

</validation-observer>
