<?php $view->script('permission-index', 'system/user:app/bundle/permission-index.js', 'vue') ?>

<div id="permissions" v-cloak>

    <h2 class="uk-h3">{{ 'Permissions' | trans }}</h2>

    <div :id="key_group" class="uk-overflow-auto uk-margin-large" v-for="(group, key_group) in permissions" :key="key_group">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ key_group }}</th>
                    <th class="pk-table-width-minimum"></th>
                    <th class="pk-table-width-minimum pk-table-width-100 pk-table-max-width-100 uk-text-truncate uk-text-center" v-for="(r, key_r) in roles" :key="key_r">{{ r.name }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(permission, key_permission) in group" :class="{'uk-visible-toggle': permission.trusted}" :key="key_permission">
                    <td class="pk-table-text-break">
                        <span :uk-tooltip="permission.description | trans" pos="top-left" delay="200">{{ permission.title | trans }}</span>
                    </td>
                    <td>
                        <i class="pk-icon-warning uk-invisible-hover" :uk-tooltip="'Grant this permission to trusted roles only to avoid security implications.' | trans" v-if="permission.trusted"></i>
                    </td>
                    <td class="uk-text-center" v-for="(role, key_role) in roles" :key="key_role">

                        <span class="uk-position-relative" v-show="showFakeCheckbox(role, key_permission)">
                            <!-- key = $parent.$key -->
                            <input class="uk-checkbox" type="checkbox" checked disabled>
                            <span class="uk-position-cover" v-if="!role.administrator" @click="addPermission(role, key_permission)" @click="savePermissions(role)"></span>
                        </span>

                        <input class="uk-checkbox" type="checkbox" :value="key_permission" v-show="!showFakeCheckbox(role, key_permission)" v-model="role.permissions" @click="savePermissions(role)">
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</div>
