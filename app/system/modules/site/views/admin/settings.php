<?php $view->script('site-settings', 'system/site:app/bundle/settings.js', ['vue', 'editor']) ?>

<validation-observer tag="form" id="settings" ref="observer" @submit.prevent="submit" v-cloak>

    <div class="pk-grid-large" uk-grid>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-default pk-nav-large" ref="tab">
                    <li :class="{'uk-active': section.active}" v-for="section in orderBy(sections, 'priority')" :key="section.name">
                        <a><span :class="section.icon ? 'uk-margin-right ' + section.icon : ''"></span><span class="uk-text-middle">{{ section.label | trans }}</span></a>
                    </li>
                </ul>

            </div>

        </div>
        <div class="pk-width-content">

            <ul class="uk-switcher settings-tab uk-margin" ref="content">
                <li v-for="section in orderBy(sections, 'priority')" :key="section.name">
                    <component :is="section.name" :config.sync="config"></component>
                </li>
            </ul>

        </div>
    </div>

</validation-observer>
