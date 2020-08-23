<?php $view->script('marketplace', 'installer:app/bundle/marketplace.js', 'vue') ?>
<?php $view->style('marketplace', 'installer:assets/css/marketplace.css') ?>
<section id="marketplace" v-cloak>

    <div class="uk-section uk-section-small uk-text-center">
        <h1>{{'The GreenCheap Marketplace' | trans}}</h1>
        <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="search"></span>
            <input class="uk-input uk-width-large@s uk-form-blank uk-form-large uk-text-center" v-model="config.filter.search" :placeholder="'Search Package' | trans">
        </div>
    </div>
    <div>
        <ul class="tm-switcher" uk-tab>
            <li><a @click.prevent="setType('greencheap-extension')" :class="{'uk-active':config.filter.type == 'greencheap-extension'}">{{'EXTENSION' | trans}}</a></li>
            <li><a @click.prevent="setType('greencheap-theme')" :class="{'uk-active':config.filter.type == 'greencheap-theme'}">{{'THEME' | trans}}</a></li>
        </ul>
    </div>

    <div class="uk-section uk-section-xsmall">
        <div class="uk-grid uk-child-width-1-4@xl uk-child-width-1-3@m uk-child-width-1-2@s uk-grid-match" uk-grid>
            <div v-for="pkg in pkgs">
                <div class="uk-card tm-marketplace-item" @click.prevent="openModal(pkg)">
                    <div class="uk-card-media-top">
                        <img :src="pkg.image.src" :alt="pkg.image.alt">
                    </div>
                    <div class="uk-card-body uk-card-small">
                        <div v-if="pkg.installed">
                            <button v-show="!pkg.installed.update" class="uk-align-right uk-button uk-button-primary uk-button-small" disabled>{{ 'Installed'| trans }}</button>
                            <button v-show="pkg.installed.update" class="uk-align-right uk-button uk-button-primary tm-button-update uk-button-small">{{ 'Update' | trans }}</button>
                        </div>
                        <div v-else>
                            <button class="uk-align-right uk-button uk-button-primary uk-button-small">{{'Install' | trans}}</button>
                        </div>
                        <div>
                            <h4 class="uk-margin-remove">{{pkg.title}}</h4>
                            <span class="uk-display-block uk-text-small uk-text-muted">{{pkg.author}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <v-modal ref="modalDeatil" :large="true" :center="true">
        <div v-if="modalpkg" class="uk-modal-body uk-padding">
            <section class="uk-grid" uk-grid>
                <div class="uk-width-expand@m uk-first-column">
                    <h1 class="uk-h2 uk-margin-remove">{{modalpkg.title}}</h1>
                    <ul class="tm-subnav uk-subnav uk-subnav-divider uk-margin-small-top">
                        <li><span class="uk-text-capitalize">{{modalpkg.author}}</span></li>
                        <li><span class="uk-text-capitalize">{{ 'Version' | trans }} {{modalpkg.version}}</span></li>
                    </ul>
                </div>
            </section>
            <article class="uk-grid" uk-grid>
                <div class="uk-width-expand@m uk-first-column">
                    <div class="uk-margin">
                        <img :src="getImage(modalpkg)" class="tm-modal-image">
                    </div>
                    <div v-if="modalpkg.content" class="uk-margin">
                        <h2>{{ 'Description' | trans }}</h2>
                        <div v-html="$options.filters.markdown(modalpkg.content)"></div>
                    </div>
                    <div v-if="modalpkg.data.content.changelog" class="uk-margin">
                        <h2>{{ 'Changelog' | trans }}</h2>
                        <div v-html="$options.filters.markdown(modalpkg.data.content.changelog)"></div>
                    </div>
                </div>
                <div class="uk-width-medium@m">
                    <div v-if="modalpkg.installed">
                        <button v-show="!modalpkg.installed.update" class="uk-width-expand uk-button uk-button-primary uk-button-small uk-button-large" disabled>{{ 'Installed' | trans }}</button>
                        <a v-show="modalpkg.installed.update" @click.prevent="downloadPackage" class="uk-width-expand uk-button uk-button-primary tm-button-update uk-button-small uk-button-large">{{ 'Update' | trans }}</a>
                    </div>
                    <div v-else>
                        <a @click.prevent="downloadPackage" class="uk-width-expand uk-button uk-button-primary uk-button-small uk-button-large">{{ 'Install' | trans }}</a>
                    </div>
                    <ul class="uk-list uk-list-large uk-list-divider uk-text-muted uk-text-small uk-margin-top">
                        <li>
                            <div class="uk-grid uk-child-width-1-2" uk-grid="">
                                <div class="uk-text-left uk-first-column">
                                    {{ 'Type' | trans }} </div>
                                <div v-if="modalpkg.type == 'greencheap-extension'" class="uk-text-right">
                                    {{ 'Extension' | trans }}
                                </div>
                                <div v-if="modalpkg.type == 'greencheap-theme'" class="uk-text-right">
                                    {{ 'Theme' | trans }}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="uk-grid uk-child-width-1-2" uk-grid="">
                                <div class="uk-text-left uk-first-column">
                                    {{ 'Author' | trans }}
                                </div>
                                <div class="uk-text-right">
                                    {{modalpkg.author}}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="uk-grid uk-child-width-1-2" uk-grid="">
                                <div class="uk-text-left uk-first-column">
                                    {{ 'Version' | trans }} </div>
                                <div class="uk-text-right">
                                    {{modalpkg.version}}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="uk-grid uk-child-width-1-2" uk-grid="">
                                <div class="uk-text-left uk-first-column">
                                    {{ 'Created' | trans }} </div>
                                <div class="uk-text-right">
                                    {{modalpkg.date | date}}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="uk-grid uk-child-width-1-2" uk-grid="">
                                <div class="uk-text-left uk-first-column">
                                    {{ 'Last Update' | trans }}
                                </div>
                                <div class="uk-text-right">
                                    {{modalpkg.modified | date}}
                                </div>
                            </div>
                        </li>
                        <li v-show="modalpkg.data.content.repository_url">
                            <div class="uk-grid" uk-grid="">
                                <div class="uk-width-small uk-text-left uk-first-column">
                                    {{ 'Repository' | trans }}
                                </div>
                                <div class="uk-width-expand uk-text-right uk-text-truncate">
                                    <a class="uk-link-muted" :href="modalpkg.data.content.repository_url" target="_blank">{{modalpkg.data.content.repository_url}}</a>
                                </div>
                            </div>
                        </li>
                        <li v-show="modalpkg.data.content.support_url">
                            <div class="uk-grid" uk-grid="">
                                <div class="uk-width-small uk-text-left uk-first-column">
                                    {{ 'Support' | trans }}
                                </div>
                                <div class="uk-width-expand uk-text-right uk-text-truncate">
                                    <a class="uk-link-muted" :href="modalpkg.data.content.support_url" target="_blank">{{modalpkg.data.content.support_url}}</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <a v-show="modalpkg.data.content.demo_url" :href="modalpkg.data.content.demo_url" target="_blank" class="uk-button uk-button-default uk-button-large uk-width-expand uk-margin">{{ 'Demo' | trans }}</a>
                </div>
            </article>
        </div>
    </v-modal>

    <v-modal v-if="modalpkg" ref="installDetail" :center="true" :options="{escClose:false , bgClose:false}">
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">
                {{modalpkg.title}} {{modalpkg.version}}
            </h2>
        </div>
        <div class="uk-modal-body">
            <pre v-show="output" class="uk-margin" v-html="output"></pre>
            <div v-show="!isLoader && status === 'success'" class="uk-alert-success" uk-alert>
                <p>{{ 'Installed successfully.' | trans }}</p>
            </div>
            <div v-show="!isLoader && status === 'error'" class="uk-alert-danger" uk-alert>
                <p>{{ 'There was a problem installing.' | trans }}</p>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <div v-if="isLoader && modalpkg">
                <i uk-spinner></i>
                <span class="uk-margin-left">{{ 'Wait' | trans }}</span>
            </div>
            <button v-if="!isLoader" class="uk-button uk-button-default" type="button" @click.prevent="cancelPkg">{{ 'Cancel' | trans }}</button>
            <button v-if="!isLoader && status === 'success'" class="uk-button uk-button-primary" @click.prevent="enablePkg" type="button">{{ 'Enable Package' | trans }}</button>
        </div>
    </v-modal>

    <v-pagination :pages="pages" v-model="config.page" v-show="pages > 1 || config.page > 0"></v-pagination>
    <h3 class="uk-h2 uk-text-muted uk-text-center" v-show="pkgs && !pkgs.length">{{ 'No packages found.' | trans }}</h3>
</section>