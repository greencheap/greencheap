<?php $view->script('dashpanel' , 'system/dashpanel:app/bundle/dashpanel.js' , ['vue' , 'theme' , 'marked']) ?>

<div id="dashpanel" class="uk-section uk-section-secondary dashpanel-section-small uk-light" v-cloak>
    <div class="uk-align-right">
        <ul class="uk-grid uk-grid-small uk-margin-remove">
            <li v-for="section in sections" :key="section.name">
                <component :is="section.name"></component>
            </li>
        </ul>
    </div>
</div>