<?php $view->script('dashboard', 'system/dashboard:app/bundle/index.js', ['vue']) ?>
<div id="dashboard" v-cloak>

    <Update></Update>

    <div class="uk-flex uk-flex-middle uk-flex-right">
        <div>
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

    <div class="uk-child-width-1-2@s uk-child-width-1-3@l uk-grid-medium uk-margin-top" uk-grid>
        <div v-for="i in [0,1,2]" :key="i">
            <draggable class="list-group" :class="{
                'uk-height-large':isMove,
                'tm-draggable-placeholder': isMove && !widgets[i].length
            }" :list="widgets[i]" group="widgets" @start="startMove()" @end="stopMove()">
                <div draggable="true" v-for="widget in widgets[i]" :key="widget.id">
                    <div class="uk-margin">
                        <panel class="uk-card uk-card-default uk-visible-toggle" :widget="widget" :editing.sync="editing[widget.id]"></panel>
                    </div>
                </div>
            </draggable>
        </div>
    </div>
    
</div>
