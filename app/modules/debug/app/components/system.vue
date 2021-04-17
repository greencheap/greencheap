<template>
    <a title="System Information" >
        <img :src="$url('app/system/modules/theme/assets/images/greencheap-logo.svg')" width="20" />
    </a>
</template>

<script>
module.exports = {
    section: {
        priority: 10,
        panel: "#panel-system",
        template: `
            <div>
                <h1>Information</h1>

                <h2>System</h2>
                <table class="pf-table pf-table-dropdown">
                    <tbody>
                    <tr>
                        <td>GreenCheap</td>
                        <td>{{ version }}</td>
                    </tr>
                    <tr>
                        <td class="uk-text-nowrap">{{ 'UIkit' | trans }}</td>
                        <td>{{ UIkitVersion }}</td>
                    </tr>
                    <tr>
                        <td class="uk-text-nowrap">{{ 'Vue' | trans }}</td>
                        <td>{{ VueVersion }}</td>
                    </tr>
                    <tr>
                        <td>Server</td>
                        <td>{{ server }}</td>
                    </tr>
                    <tr>
                        <td>Useragent</td>
                        <td>{{ useragent }}</td>
                    </tr>
                    </tbody>
                </table>

                <h2>PHP</h2>
                <table class="pf-table pf-table-dropdown">
                    <tbody>
                    <tr>
                        <td>PHP</td>
                        <td>{{ phpversion }}</td>
                    </tr>
                    <tr>
                        <td>PHP SAPI</td>
                        <td>{{ sapi_name }}</td>
                    </tr>
                    <tr>
                        <td>System</td>
                        <td>{{ php }}</td>
                    </tr>
                    <tr>
                        <td>Extensions</td>
                        <td>{{ extensions }}</td>
                    </tr>
                    </tbody>
                </table>

                <h2>Database</h2>
                <table class="pf-table pf-table-dropdown">
                    <tbody>
                    <tr>
                        <td>Driver</td>
                        <td>{{ dbdriver }}</td>
                    </tr>
                    <tr>
                        <td>Version</td>
                        <td>{{ dbversion }}</td>
                    </tr>
                    <tr>
                        <td>Client</td>
                        <td>{{ dbclient }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>`,
    },

    replace: false,

    props: ["data"],

    computed: {
        VueVersion() {
            return window.Vue ? Vue.version : "-";
        },
        UIkitVersion() {
            return window.UIkit ? UIkit.version : "-";
        },
    },
};
</script>
