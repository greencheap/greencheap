<?php $view->script("update-system", "installer:app/bundle/update.js", ["vue"]); ?>

<div id="app">
    <div v-if="hasUpdate && !finished" class="">
        <div v-if="!isInstall">
            <div class="uk-child-width-1-2@m" uk-grid>
                <div>
                    <h2>{{'GreenCheap New Update' | trans}}</h2>
                </div>
                <div class="uk-flex-right">
                    <ul class="uk-flex-right uk-grid" uk-grid="">
                        <li>
                            <span class="uk-text-small uk-text-meta">{{'Version' | trans}}:</span>
                            <p class="uk-margin-remove">{{hasUpdate.version}}</p>
                        </li>
                        <li>
                            <span class="uk-text-small uk-text-meta">{{'Php Version' | trans}}:</span>
                            <p class="uk-margin-remove">{{hasUpdate.php_version}}</p>
                        </li>
                        <li>
                            <button @click.prevent="doDownload" class="uk-button uk-button-primary uk-button-large">{{'Install' | trans}}</button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="uk-margin" v-html="$options.filters.markdown(hasUpdate.content)"></div>
            <h2>{{'Changelog' | trans}}</h2>
            <div class="uk-grid-small" uk-grid v-html="changelog(hasUpdate.changelog)"></div>
        </div>
        <div class="uk-section uk-section-large uk-text-center" v-else>
            <div>
                <i uk-spinner="ratio:5"></i>
                <span class="uk-text-muted uk-text-large uk-display-block uk-margin">{{'Please wait until the installation process is finished' | trans}}</span>
            </div>

            <div class="uk-flex uk-flex-center">
                <div class="uk-width-2xlarge">
                    <progress class="uk-progress" :value="progressbar" max="100"></progress>
                    <div v-show="output" style="background: #f1f1f1;padding: 10px 20px;text-align:left">
                        <pre class="uk-margin" v-html="output" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <v-notfound v-if="!hasUpdate && !finished" :title="'There is no update you can install' | trans">
        <div>{{'The GreenCheap version you are using is %version%' | trans({version: version})}}</div>
    </v-notfound>
    <div v-if="finished" class="uk-flex uk-flex-center uk-flex-middle" uk-height-viewport="expand:true">
        <div class="uk-text-center">
            <i uk-icon="icon:check;ratio:6" class="uk-text-primary"></i>
            <p class="uk-text-muted">{{'Installation completed successfully.' | trans({version: version})}}</p>
        </div>
    </div>
</div>

