<?php $view->script('dashboard', 'system/dashboard:app/bundle/index.js', ['vue']) ?>
<div id="dashboard" v-cloak>

    <div class="uk-flex uk-flex-middle uk-flex-right">
        <div>
            <div class="uk-margin">
                <a class="uk-button uk-button-primary" @click.prevent>{{ 'Add Widget' | trans }}</a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-nav uk-dropdown-nav">
                        <li v-for="type in getTypes()">
                            <a class="uk-dropdown-close" @click="add(type)">{{ type.label | trans }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="uk-child-width-1-2@s uk-child-width-1-3@l uk-grid-medium" uk-grid>
        <div v-for="i in [0,1,2]" :key="i">
            <ul class="pk-sortable" :data-column="i" uk-sortable="handle: .uk-sortable-handle">
                <li v-for="widget in getColumn(i)" :data-id="widget.id" :data-idx="widget.idx" :key="widget.id">
                    <panel class="uk-card uk-card-default uk-visible-toggle" :widget="widget" :editing.sync="editing[widget.id]"></panel>
                </li>
            </ul>
        </div>
    </div>

</div>
