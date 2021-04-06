<?php $view->script("system_comment", "system/comment:app/bundle/index.js", "vue"); ?>
<div id="app" v-cloak>
    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap" >
        <div class="uk-flex uk-flex-middle uk-flex-wrap" >

            <h2 class="uk-h3 uk-margin-remove" v-if="!selected.length">{{ '{0} %count% Comments|{1} %count% Comment|]1,Inf[ %count% Comments' | transChoice(count, {count:count}) }}</h2>

            <template v-else>
                <h2 class="uk-h3 uk-margin-remove">{{ '{1} %count% Comment selected|]1,Inf[ %count% Comments selected' | transChoice(selected.length, {count:selected.length}) }}</h2>

                <div class="uk-margin-left" >
                    <ul class="uk-iconnav">
                        <li><a uk-icon="icon:check;ratio:1" :uk-tooltip="'Publish' | trans" @click="status(1)"></a></li> 
                        <li><a uk-icon="icon:ban;ratio:1" :uk-tooltip="'Unpublish' | trans" @click="status(0)"></a></li>
                        <li><a uk-icon="icon:warning;ratio:1" :uk-tooltip="'Spam' | trans" @click="status(2)"></a></li>
                        <li><a uk-icon="icon:trash;ratio:1" :uk-tooltip="'Delete' | trans" @click="remove" v-confirm="'Delete Posts?'"></a></li>
                    </ul>
                </div>
            </template>

            <div class="uk-search uk-search-default pk-search">
                <span uk-search-icon></span>
                <input class="uk-search-input" type="search" v-model="config.filter.search" debounce="300">
            </div>

        </div>
    </div>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input class="uk-checkbox" type="checkbox" v-check-all:selected="{ selector: 'input[name=id]' }" number></th>
                    <th class="pk-table-min-width-200">
                        {{ 'Content' | trans }}
                    </th>
                    <th class="pk-table-width-100">
                        <span v-if="!canEditAll">{{ 'Author' | trans }}</span>
                        <input-filter :title="$trans('Author')" :value.sync="config.filter.author" :options="users" v-model="config.filter.author" v-else></input-filter>
                    </th>
                    <th class="pk-table-width-100 uk-text-center">
                        <span v-if="!canEditAll">{{ 'Type' | trans }}</span>
                        <input-filter :title="$trans('Type')" :value.sync="config.filter.type" :options="getTypes" v-model="config.filter.type" v-else></input-filter>
                    </th>
                    <th class="pk-table-width-100 uk-text-center">
                        <input-filter :title="$trans('Status')" :value.sync="config.filter.status" :options="statusOptions" v-model="config.filter.status"></input-filter>
                    </th>
                    <th class="pk-table-width-100" v-order:created="config.filter.order">{{ 'Date' | trans }}</th>
                    <th class="pk-table-width-200 pk-table-min-width-200">{{ 'URL' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="check-item" v-for="comment in comments" :key="comment.id" :class="{'uk-active': active(comment)}">
                    <td><input class="uk-checkbox" type="checkbox" name="id" :value="comment.id"></td>
                    <td>
                        <a :href="$url.route('admin/comment/page/edit/{id}', { id: comment.id })" v-html="comment.content.substring(0,70)"></a>
                        <span v-if="comment.parent_id > 0" class="uk-text-small uk-text-muted uk-display-block">{{ 'Child Comment' | trans }}</span>
                    </td>
                    <td>
                        <a :href="$url.route('admin/user/edit', { id: comment.user_id })">{{ comment.author.name }}</a>
                    </td>
                    <td class="uk-text-center uk-text-capitalize">{{ comment.type }}</td>
                    <td class="uk-text-center">
                        <a :title="getStatusText(comment)" :class="{
                            'pk-icon-circle': comment.status == 0,
                            'pk-icon-circle-success': comment.status == 1,
                            'pk-icon-circle-danger': comment.status == 2,
                        }" @click="toggleStatus(comment)" uk-tooltip></a>
                    </td>
                    <td class="pk-table-width-100">
                        {{ comment.created | date }}
                    </td>
                    <td class="pk-table-text-break">
                        <a target="_blank" v-if="comment.url" :href="comment.url" target="_blank">{{ comment.url }}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h2 uk-text-muted uk-text-center" v-show="comments && !comments.length">{{ 'No comments found.' | trans }}</h3>

    <v-pagination :pages="pages" v-model="config.page" v-show="pages > 1 || config.page > 0"></v-pagination>
</div>
