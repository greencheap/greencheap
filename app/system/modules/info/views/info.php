<?php $view->script('info', 'app/system/modules/info/app/bundle/info.js', 'vue') ?>

<div id="info" class="pk-grid-large" uk-grid v-cloak>
    <div class="pk-width-sidebar">

        <div class="uk-panel">
            <ul class="uk-nav uk-nav-default pk-nav-large" uk-switcher="connect: #tab-content">
                <li><a><span class="uk-margin-right" uk-icon="icon:router;ratio:1.3"></span><span class="uk-text-middle">{{ 'System' | trans }}</span></a></li>
                <li><a><span class="uk-margin-right" uk-icon="icon:php;ratio:1.3"></span><span class="uk-text-middle">{{ 'PHP' | trans }}</span></a></li>
                <li><a><span class="uk-margin-right" uk-icon="icon:database;ratio:1.3"></span><span class="uk-text-middle">{{ 'Database' | trans }}</span></a></li>
                <li><a><span class="uk-margin-right" uk-icon="icon:face-id;ratio:1.3"></span><span class="uk-text-middle">{{ 'Permissions' | trans }}</span></a></li>
            </ul>
        </div>

    </div>
    <div class="pk-width-content">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>
                <h2>{{ 'System' | trans }}</h2>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'GreenCheap Version' | trans }}</td>
                                <td>{{ info.version }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'UIkit Version' | trans }}</td>
                                <td>{{ UIkitVersion }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'Vue Version' | trans }}</td>
                                <td>{{ VueVersion }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'Web Server' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.server }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'User Agent' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.useragent }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'PHP' | trans }}</h2>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'Version' | trans }}</td>
                                <td>{{ info.phpversion }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Handler' | trans }}</td>
                                <td>{{ info.sapi_name }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Built On' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.php }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Extensions' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.extensions }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'Database' | trans }}</h2>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'Driver' | trans }}</td>
                                <td>{{ info.dbdriver }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Version' | trans }}</td>
                                <td>{{ info.dbversion }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Client' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.dbclient }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'Permission' | trans }}</h2>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th>{{ 'Path' | trans }}</th>
                                <th class="pk-table-width-200">{{ 'Status' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(writable, dir) in info.directories">
                                <td>{{ dir }}</td>
                                <td class="uk-text-success" v-if="writable">{{ 'Writable' | trans }}</span></td>
                                <td class="uk-text-danger" v-else>{{ 'Unwritable' | trans }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
        </ul>

    </div>
</div>
