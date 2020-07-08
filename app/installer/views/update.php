<?php $view->script('update-system', 'installer:app/bundle/update.js', ['vue', 'marked']) ?>

<div id="app" v-cloak>
    <div v-if="hasVersion">
        <progress class="uk-progress" :value="progress" max="100" v-show="progress > 0"></progress>
        <div class="uk-grid" uk-grid>
            <div class="uk-width-large@m uk-flex uk-flex-middle">
                <div>
                    <div>
                        <h1 class="uk-heading-large uk-width-small">{{'Update Available' | trans}}</h1>
                        <ul class="uk-list uk-list-divider">
                            <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                <span class="uk-margin-small-right">{{'YOUR VERSION' | trans}}</span> <strong>{{system.version}}</strong>
                            </li>
                            <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                <span class="uk-margin-small-right">{{'NEW VERSION' | trans}}</span> <strong>{{version.data.version}}</strong>
                            </li>
                            <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                <span class="uk-margin-small-right">{{'CONSTRAINT' | trans}}</span> <strong class="uk-text-primary uk-text-lowercase">{{version.data.constraint}}</strong>
                            </li>
                        </ul>

                        <button type="button" class="uk-button uk-button-default uk-width-expand uk-button-large" @click.prevent="install" :disabled="processing">
                            <span v-if="processing" class="uk-margin-right" uk-spinner></span>
                            <span>{{ 'Update Now' | trans }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="uk-width-expand@m">
                <div class="uk-background uk-background-muted uk-padding-small uk-height-xlarge uk-overflow-auto">
                    <h2>{{'Readme' | trans}}</h2>
                    <div v-html="$options.filters.markdown(version.readme)"></div>
                    <hr>
                    <h2>{{'Changelog' | trans}}</h2>
                    <div class="uk-grid-small" uk-grid v-html="changelog(version.changelog)"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-section uk-section-large uk-flex uk-flex-center uk-flex-middle" v-else>
        <div class="uk-text-center uk-width-xlarge">
            <span class="uk-text-warning" uk-icon="icon:warning;ratio:6"></span>
            <h3 class="uk-h1 uk-margin-small uk-text-uppercase">{{'A New Update Has Not Been Released!' | trans}}</h3>
            <p>{{'Your system is running on the latest version. We will notify you when we give a new update. You should continue working until this time.' | trans}}</p>
            <hr>
            <ul class="uk-subnav uk-subnav-divider uk-flex uk-flex-center">
                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                    <span class="uk-margin-small-right">{{'YOUR SYSTEM VERSION' | trans}}</span> <strong>{{version.data.version}}</strong>
                </li>
                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                    <span class="uk-margin-small-right">{{'CONSTRAINT' | trans}}</span> <strong class="uk-text-primary uk-text-lowercase">{{version.data.constraint}}</strong>
                </li>
            </ul>
        </div>
    </div>
</div>
