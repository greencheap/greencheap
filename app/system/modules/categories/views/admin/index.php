<?php $view->script('categories', 'system/categories:app/bundle/categories.js', 'vue') ?>

<section id="app" v-cloak>

    <div class="uk-flex uk-flex-middle uk-flex-between">
        <div class="uk-flex uk-flex-middle uk-flex-wrap">
            <h2 class="uk-h3 uk-margin-remove" v-if="!selected.length">{{ '{0} %count% Category|{1} %count% Category|]1,Inf[ %count% Categories' | transChoice(count, {count:count}) }}</h2>
            <div class="uk-flex uk-flex-middle" v-else>
                <h2 class="uk-h3 uk-margin-remove">{{ '{1} %count% Category selected|]1,Inf[ %count% Categories selected' | transChoice(selected.length, {count:selected.length}) }}</h2>
                <div class="uk-margin-left">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="pk-icon-check pk-icon-hover" :uk-tooltip="'Publish' | trans" @click.prevent="status(3)"></a></li>
                        <li><a class="pk-icon-block pk-icon-hover" :uk-tooltip="'Unpublish' | trans" @click.prevent="status(2)"></a></li>
                        <li><a class="pk-icon-delete pk-icon-hover" :uk-tooltip="'Delete' | trans" @click.prevent="remove()" v-confirm="'Delete Category?'"></a></li>
                    </ul>
                </div>
            </div>
            <select class="uk-margin-left uk-select uk-form-width-medium" v-model="config.filters.type">
                <option value="">{{'All' | trans}}</option>
                <option v-for="(type , id) in types" :value="type.type">{{type.type}}</option>
            </select>
            <div class="uk-search uk-search-default pk-search">
                <span uk-search-icon></span>
                <input class="uk-search-input" type="search" v-model="config.filters.search" debounce="300">
            </div>
        </div>

        <div class="uk-text-right">
            <a v-if="config.filters.sub_category" @click.prevent="clearSubCategory" class="uk-button uk-button-default uk-margin-small-right">{{'Clear Sub Category' | trans}}</a>

            <a v-if="config.filters.type && types.length" :href="$url.route('admin/categories/edit' , {type: config.filters.type , redirect: currentUrl})" class="uk-button uk-button-primary">{{'Add' | trans}}</a>
            <button v-if="!config.filters.type && !types.length" class="uk-button uk-button-primary uk-button-disabled" disabled>{{'Add' | trans}}</button>
        </div>
    </div>

    <div class="uk-margin">
        <div class="uk-overflow-container">
            <table class="uk-table uk-table-hover uk-table-striped">
                <thead>
                    <tr>
                        <th style="width:30px"><input type="checkbox" class="uk-checkbox" v-check-all:selected="{ selector: 'input[name=id]' }" number></th>
                        <th class="">{{ 'Title' | trans }}</th>
                        <th class="uk-width-small uk-text-center">{{'Status' | trans}}</th>
                        <th class="uk-text-center uk-width-small">{{ 'Sub Category' | trans }}</th>
                        <th class="uk-text-center uk-width-small">{{ 'Type' | trans }}</th>
                        <th class="uk-text-center uk-width-small">{{ 'Date' | trans }}</th>
                        <th class="uk-width-medium">{{ 'URL' | trans }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(category , id) in categories" :key="id">
                        <td><input type="checkbox" class="uk-checkbox" :name="category.has_subcategory ? 'noting':'id'" :value="category.id" :disabled="category.has_subcategory"></td>
                        <td><a :href="$url.route('admin/categories/edit' , {id: category.id, type: category.type , redirect: currentUrl })">{{category.title}}</a></td>
                        <td class="uk-text-center">
                            <span :class="{
                                'pk-icon-circle-danger':category.status == 0,
                                'pk-icon-circle':category.status == 1,
                                'pk-icon-circle-warning':category.status == 2,
                                'pk-icon-circle-success':category.status == 3 && category.published,
                                'pk-icon-schedule': category.status == 3 && !category.published
                            }"></span>
                        </td>
                        <td class="uk-text-center"><a v-if="category.has_subcategory" @click.prevent="setConfigSub(category.id)" uk-icon="refresh" :uk-tooltip="'Has Sub Category' | trans"></a></td>
                        <td class="uk-text-center">{{ category.type }}</td>
                        <td class="uk-text-center">{{ category.date | date }}</td>
                        <td class=""><a href="#">Link</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="categories && !categories.length">{{ 'No categories found.' | trans }}</h3>
        <div v-if="!config.filters.type && !types.length" class="uk-flex uk-flex-middle uk-flex-center">
            <p class="uk-text-center uk-width-xlarge uk-text-muted">{{'There is no package that can be integrated. So you cannot add categories. First, install a package that works with the category system, for example: Blog extension' | trans}}</p>
        </div>
    </div>
</section>