<?php $view->script('extensions', 'installer:app/bundle/extensions.js', ['vue']); ?>

<div id="extensions" v-cloak>

    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap">
        <div class="uk-flex uk-flex-middle uk-flex-wrap" >
            <h2 class="uk-h3 uk-margin-remove">{{ 'Extensions' | trans }}</h2>
            <div class="uk-search uk-search-default pk-search">
                <span uk-search-icon></span>
                <input class="uk-search-input" type="search" v-model="search">
            </div>
        </div>
        <div class="uk-flex uk-flex-right uk-flex-middle">
            <a :href="$url.route('admin/system/marketplace')" class="uk-button uk-button-secondary uk-margin-small-right">{{ 'App Store' | trans }}</a>
            <package-upload :api="api" :packages="packages" type="extension"></package-upload>
        </div>
    </div>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th colspan="2">{{ 'Name' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                    <th class="pk-table-width-minimum uk-text-center">{{ 'Status' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Version' | trans }}</th>
                    <th class="pk-table-width-150">{{ 'Folder' | trans }}</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="uk-visible-toggle" v-for="(pkg , id) in filterBy(packages, search, 'title')">
                    <td style="width:50px">
                        <div class="uk-position-relative">
                            <img :data-src="icon(pkg)" width="100%" uk-img>
                        </div>
                    </td>
                    <td class="uk-text-nowrap">
                        <a @click.prevent="settings(pkg)" v-if="pkg.enabled && pkg.settings">{{ pkg.title }}</a>
                        <span v-else>{{ pkg.title }}</span>
                        <div class="uk-text-muted">{{ pkg.authors[0].name }}</div>
                    </td>
                    <td>
                        <a class="uk-button tm-button-success uk-button-small" @click="update(pkg, updates)" v-show="updates && updates[pkg.name]">{{ 'Update' | trans }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="pk-icon-circle-success" :title="'Enabled' | trans" v-if="pkg.enabled" @click="disable(pkg)"></a>
                        <a class="pk-icon-circle-danger" :title="'Disabled' | trans" v-else @click="enable(pkg)"></a>
                    </td>
                    <td class="uk-text-center">{{ pkg.version }}</td>
                    <td class="uk-text-truncate">/{{ pkg.name }}</td>
                    <td class="uk-text-right">
                        <div class="uk-invisible-hover">
                            <ul class="uk-iconnav">
                                <li><a uk-icon="icon:info;ratio:1.3" :uk-tooltip="'View Details' | trans" @click.prevent="details(pkg)"></a></li>
                                <li v-show="pkg.enabled && pkg.permissions"><a uk-icon="icon:lock;ratio:1.3" :uk-tooltip="'View Permissions' | trans" :href="$url.route('admin/user/permissions#{name}', {name:pkg.module})"></a></li>
                                <li v-show="!pkg.enabled"><a uk-icon="icon:trash;ratio:1.3" :uk-tooltip="'Delete' | trans" @click="uninstall(pkg, packages)" v-confirm="'Uninstall extension?'"></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h2 uk-text-muted uk-text-center" v-show="empty(packages)">{{ 'No extension found.' | trans }}</h3>

    <v-modal ref="details">
        <package-details :api="api" :package="package"></package-details>
    </v-modal>

    <v-modal ref="settings">
        <component :is="view" :package="package"></component>
    </v-modal>

</div>
