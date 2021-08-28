<?php
//$view->script('themes', 'installer:app/bundle/themes.js', ['vue', 'uikit-upload', 'editor']);
?>
<?php $view->script("themes", "installer:app/bundle/themes.js", ["vue", "editor"]); ?>

<div id="themes" v-cloak>

    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap">
        <div class="uk-flex uk-flex-middle uk-flex-wrap">
            <v-title :title="'Themes' | trans"/>
            <div class="uk-search uk-search-default pk-search">
                <span uk-search-icon></span>
                <input class="uk-search-input" type="search" v-model="search">
            </div>
        </div>
        <div class="uk-flex uk-flex-right uk-flex-middle">
            <a :href="$url.route('admin/system/marketplace')" class="uk-button uk-button-secondary uk-margin-small-right">{{ 'App Store' | trans }}</a>
            <package-upload :api="api" :packages="packages" type="theme"></package-upload>
        </div>
    </div>

    <div class="uk-grid-medium uk-grid-match uk-child-width-1-2@s uk-child-width-1-3@m" uk-grid>
        <!-- <div v-for="pkg in packages | filterBy search in 'title' | themeorder"> -->
        <div v-for="pkg in themeorder(filterBy(packages, search, 'title'))">
            <div class="uk-card uk-card-default uk-visible-toggle">

                <div class="uk-card-media-top">
                    <div class="uk-inline-clip uk-transition-toggle">
                        <div class="uk-background-cover uk-position-cover" :style="{'background-image': 'url('+image(pkg)+')'}"></div>
                        <canvas class="uk-responsive-width uk-display-block" width="1200" height="800"></canvas>
                        <div class="uk-transition-fade uk-position-cover uk-position-small uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle"></div>
                    </div>
                </div>

                <a class="uk-position-cover" @click.prevent="details(pkg)"></a>

                <div class="uk-card-body uk-position-relative uk-padding-small">
                    <h2 class="uk-h5 uk-margin-remove">{{ pkg.title }}</h2>
                    <div class="uk-text-muted uk-text-small">{{ pkg.authors[0].name }}</div>

                    <div class="uk-position-center-right uk-padding-remove-vertical uk-padding-small">
                        <button class="uk-button uk-button-primary uk-button-small" v-show="pkg.enabled && pkg.settings" @click="settings(pkg)">{{ 'Customize' | trans }}</button>
                        <button class="uk-button tm-button-success uk-button-small" @click="update(pkg, updates)" v-show="updates && updates[pkg.name]">{{ 'Update' | trans }}</button>
                    </div>
                </div>

                <div class="uk-position-top-right uk-position-small uk-hidden-hover pk-card-overlay" v-if="!pkg.enabled">
                    <ul class="uk-subnav pk-subnav-icon uk-margin-remove-bottom">
                        <li><a uk-icon="icon:star" :uk-tooltip="'Enable' | trans" @click="enable(pkg)"></a></li>
                        <li><a uk-icon="icon:trash" :uk-tooltip="'Delete' | trans" @click="uninstall(pkg, packages)" v-confirm="'Uninstall theme?'"></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <v-notfound v-show="!packages.length" :title="'No theme found' | trans" ></v-notfound>

    <v-modal ref="details">
        <package-details :api="api" :package="package"></package-details>
    </v-modal>

    <v-modal ref="settings">
        <component :is="view" :package="package"></component>
    </v-modal>

</div>
