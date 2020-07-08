<?php $view->script('widget-index', 'system/widget:app/bundle/index.js', ['widgets']) ?>

<style media="screen">
    .uk-sortable {
        min-height: 50px;
    }
</style>

<div id="widgets" v-cloak>

    <div class="pk-grid-large" uk-grid>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-default">
                    <li :class="{'uk-active': active()}">
                        <a @click="select()">{{ 'All' | trans }}</a>
                    </li>
                    <li :class="{'uk-active': active(unassigned)}" v-show="unassigned.widgets.length">
                        <a @click="select(unassigned)">{{ 'Unassigned' | trans }} <span class="uk-text-muted uk-float-right">{{ unassigned.widgets.length }}</span></a>
                    </li>
                    <li class="uk-nav-header">{{ 'Positions' | trans }}</li>
                    <li :class="{'uk-active': active(pos)}" v-for="pos in config.positions" :key="pos.name">
                        <a @click="select(pos)">{{ pos.label }}  <span class="uk-text-muted uk-float-right" v-show="pos.widgets.length">{{ pos.widgets.length }}</span></a>
                    </li>
                </ul>

            </div>

        </div>
        <div class="pk-width-content">

            <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap uk-grid-small" uk-grid>
                <div class="uk-flex uk-flex-middle uk-flex-wrap" >

                    <h2 class="uk-h3 uk-margin-remove" v-if="!selected.length">{{ position ? position.label : $trans('All') }}</h2>

                    <template v-else>

                        <h2 class="uk-h3 uk-margin-remove">{{ '{1} %count% Widget selected|]1,Inf[ %count% Widgets selected' | transChoice(selected.length,{count:selected.length}) }}</h2>

                        <div class="uk-margin-left">
                            <ul class="uk-iconnav">
                                <li><a uk-icon="check" :title="'Publish' | trans" uk-tooltip="delay: 500" @click.prevent="status(1)"></a></li>
                                <li><a uk-icon="ban" :title="'Unpublish' | trans" uk-tooltip="delay: 500" @click.prevent="status(0)"></a></li>
                                <li><a uk-icon="copy" :title="'Copy' | trans" uk-tooltip="delay: 500" @click.prevent="copy"></a></li>
                                <li>
                                    <a uk-icon="move" :title="'Move' | trans" uk-tooltip="delay: 500" @click.prevent></a>
                                    <div uk-dropdown="mode: click">
                                        <ul class="uk-nav uk-dropdown-nav">
                                            <li v-for="pos in config.positions" :key="pos.name"><a @click="move(pos.name, selected)">{{ pos.label }}</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" v-confirm="'Delete widget?'"></a></li>
                            </ul>
                        </div>

                    </template>

                    <div class="uk-search uk-search-default pk-search">
                        <span uk-search-icon></span>
                        <input class="uk-search-input" type="search" v-model="config.filter.search" debounce="300">
                    </div>

                </div>
                <div class="uk-position-relative" >

                    <div>
                        <button class="uk-button uk-button-primary" type="button">{{ 'Add' | trans }}</button>
                        <div uk-dropdown="mode: click">
                            <ul class="uk-nav uk-dropdown-nav">
                                <li v-for="type in types" :key="type.name"><a :href="$url.route('admin/site/widget/edit', {type: type.name, position:(position ? position.name:'')})">{{ type.label || type.name | trans }}</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="uk-overflow-auto">

                <template v-if="!position && emptyafterfilter()">

                    <div class="pk-table-fake pk-table-fake-header pk-table-fake-border">
                        <div class="pk-table-width-minimum"><input class="uk-checkbox" type="checkbox"></div>
                        <div class="pk-table-min-width-150">{{ 'Title' | trans }}</div>
                        <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
                        <div class="pk-table-width-100 pk-table-text-truncate">{{ 'Type' | trans }}</div>
                        <div class="pk-table-width-150 pk-table-text-truncate">
                            <input-filter :title="$trans('Pages')" :value.sync="config.filter.node" :options="nodes" v-model="config.filter.node" number></input-filter>
                        </div>
                    </div>

                    <h3 class="uk-h2 uk-text-muted uk-text-center uk-margin-bottom">{{ 'No widgets found.' | trans }}</h3>

                </template>

                <div class="uk-margin-bottom" :data-pos="pos.name" v-for="pos in filtered_positions" :key="pos.name">

                    <div class="pk-table-fake pk-table-fake-header" :class="{'pk-table-fake-border': !pos.widgets.length || (position && emptyafterfilter(pos.widgets))}" v-show="position || !emptyafterfilter(pos.widgets)">
                        <div class="pk-table-width-minimum"><input class="uk-checkbox" type="checkbox" v-check-all:selected="{ selector: 'input[name=id]', group: '[data-pos='+ pos.name +']' }" number></div>
                        <div class="pk-table-min-width-150" v-if="position">{{ 'Title' | trans }}</div>
                        <div class="pk-table-min-width-150" v-if="!position">{{ pos.label }}</div>
                        <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
                        <div class="pk-table-width-100 pk-table-text-truncate">{{ 'Type' | trans }}</div>
                        <div class="pk-table-width-150 pk-table-text-truncate">
                            <input-filter :title="$trans('Pages')" :value.sync="config.filter.node" :options="nodes" v-model="config.filter.node" number></input-filter>
                        </div>
                    </div>

                    <h3 class="uk-h2 uk-text-muted uk-text-center" v-if="!pos.widgets.length || (position && emptyafterfilter(pos.widgets))">{{ 'No widgets found.' | trans }}</h3>

                    <ul class="uk-list uk-margin-remove" v-sortable v-if="!emptyafterfilter(pos.widgets)" :data-position="pos.name">
                        <li class="check-item uk-margin-remove-top" :class="{'uk-active': isSelected(widget.id)}" v-for="widget in pos.widgets" :key="widget.id" :data-id="widget.id" v-show="infilter(widget)">

                            <div class="tm-sortable-panel pk-table-fake">
                                <div class="pk-table-width-minimum"><input class="uk-checkbox" type="checkbox" name="id" :value="widget.id"></div>
                                <div class="pk-table-min-width-150">
                                    <a :href="$url.route('admin/site/widget/edit', {id: widget.id})" v-if="typeExist(widget)">{{ widget.title }}</a>
                                    <span v-else>{{ widget.title }}</span>
                                </div>
                                <div class="pk-table-width-100 uk-text-center">
                                    <td class="uk-text-center">
                                        <a :class="{'pk-icon-circle-danger': !widget.status, 'pk-icon-circle-success': widget.status}" @click="toggleStatus(widget)"></a>
                                    </td>
                                </div>
                                <div class="pk-table-width-100 pk-table-text-truncate">{{ typeExist(widget) | trans }}</div>
                                <div class="pk-table-width-150 pk-table-text-truncate">{{ getPageFilter(widget) }}</div>
                            </div>

                        </li>
                    </ul>

                </div>

            </div>

        </div>
    </div>

</div>
