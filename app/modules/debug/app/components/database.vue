<template>
    <div>
        <a title="Database" class="pf-parent"><span class="pf-icon pf-icon-database" /> {{ data.nb_statements }}</a>

        <div class="pf-dropdown">
            <table class="pf-table pf-table-dropdown">
                <tbody>
                    <tr>
                        <td>Queries</td>
                        <td>{{ data.nb_statements }}</td>
                    </tr>
                    <tr>
                        <td>Time</td>
                        <td>{{ data.accumulated_duration_str }}</td>
                    </tr>
                    <tr>
                        <td>Driver</td>
                        <td>{{ data.driver }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

module.exports = {

    section: {
        priority: 50,
        panel: '#panel-database',
        template: `
                <div>
                    <h1>Queries</h1>

                    <p v-show="!nb_statements">
                        <em>No queries.</em>
                    </p>

                    <div v-for="statement in statements">

                        <pre><code>{{ statement.sql }}</code></pre>

                        <p class="pf-submenu">
                            <span>{{ statement.duration_str }}</span>
                            <span>{{ statement.params | json }}</span>
                        </p>

                    </div>
                </div>`,
    },

    replace: false,

    props: ['data'],

    filters: {
        json: value => JSON.stringify(value, null, 2),
    },

};

</script>
