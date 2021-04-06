<template>
    <div>
        <div v-if="editing" class="uk-card-header pk-panel-teaser">
            <form class="uk-form-stacked">
                <div class="uk-margin-top">
                    <label class="uk-form-label">{{ "User Type" | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-margin-small">
                            <label>
                                <input v-model="widget.show" class="uk-radio" type="radio" value="login" />
                                <span class="uk-margin-small-left">{{ "Logged in" | trans }}</span>
                            </label>
                        </p>
                        <p class="uk-margin-small">
                            <label>
                                <input v-model="widget.show" class="uk-radio" type="radio" value="registered" />
                                <span class="uk-margin-small-left">{{ "Last registered" | trans }}</span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">{{ "Display" | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-margin-small">
                            <label>
                                <input v-model="widget.display" class="uk-radio" type="radio" value="thumbnail" />
                                <span class="uk-margin-small-left">{{ "Thumbnail" | trans }}</span>
                            </label>
                        </p>
                        <p class="uk-margin-small">
                            <label>
                                <input v-model="widget.display" class="uk-radio" type="radio" value="list" />
                                <span class="uk-margin-small-left">{{ "List" | trans }}</span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">{{ "Total Users" | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-margin-small">
                            <label
                                ><input v-model="widget.total" class="uk-radio" type="radio" value="1" /><span class="uk-margin-small-left">{{ "Show" | trans }}</span></label
                            >
                        </p>
                        <p class="uk-margin-small">
                            <label
                                ><input v-model="widget.total" class="uk-radio" type="radio" value="" /><span class="uk-margin-small-left">{{ "Hide" | trans }}</span></label
                            >
                        </p>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="-label" for="form-user-number">{{ "Number of Users" | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-user-number" v-model="widget.count" class="uk-select uk-width-1-1" number>
                            <option value="6">6</option>
                            <option value="12">12</option>
                            <option value="18">18</option>
                            <option value="24">24</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="uk-card-body">
            <div v-if="widget.total" class="uk-text-lead">{{ "Total User:" | trans }} {{ userscount }}</div>

            <h3 v-if="widget.show == 'registered' && widget.total" class="uk-h5 uk-margin-small-top"><i uk-icon="user" class="uk-margin-small-right"></i>{{ "{0} Registered Users|{1} Registered User|]1,Inf[ Registered Users" | transChoice(userscount) }}</h3>

            <h3 v-if="widget.show != 'registered' && widget.total" class="uk-h5 uk-margin-small-top"><i uk-icon="user" class="uk-margin-small-right"></i>{{ "{0} Logged in Users|{1} Logged in User|]1,Inf[ Logged in Users" | transChoice(userscount) }}</h3>

            <h3 v-if="widget.show == 'registered' && !widget.total" class="uk-h5"><i uk-icon="user" class="uk-margin-small-right"></i>{{ "Latest registered Users" | trans }}</h3>

            <h3 v-if="widget.show != 'registered' && !widget.total" class="uk-h5"><i uk-icon="user" class="uk-margin-small-right"></i>{{ "Latest logged in Users" | trans }}</h3>

            <ul v-if="users.length && widget.display == 'thumbnail'" data-user class="uk-grid-small uk-child-width-1-5 uk-child-width-1-5@m uk-child-width-1-6@xl" uk-grid>
                <li v-for="user in users" :key="user.id">
                    <a :href="$url.route('admin/user/edit', { id: user.id })" :title="user.username">
                        <img :src="user.avatar" class="uk-border-circle" width="100" height="100" :alt="user.name" />
                    </a>
                </li>
            </ul>

            <ul v-if="users.length && widget.display == 'list'" data-user class="uk-list uk-list-divider">
                <li v-for="user in users" :key="user.id" class="uk-flex uk-flex-middle">
                    <img :src="user.avatar" class="uk-border-circle uk-margin-right" width="40" height="40" :alt="user.name" />
                    <div class="uk-flex-1 uk-text-truncate">
                        <a :href="$url.route('admin/user/edit', { id: user.id })" :title="user.name">{{ user.username }}</a>
                        <br /><a class="uk-link-muted" :href="'mailto:' + user.email">{{ user.email }}</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
module.exports = {
    type: {
        id: "user",
        label: "User",
        description() {},
        defaults: {
            type: "user",
            show: "login",
            display: "list",
            total: "",
            count: 12,
        },
    },

    replace: false,

    props: ["widget", "editing"],

    data() {
        return {
            users: [],
            userscount: null,
        };
    },

    watch: {
        "widget.show": {
            handler: "load",
            immediate: true,
        },

        "widget.count": "load",
    },

    methods: {
        load() {
            let query;

            if (this.widget.show === "registered") {
                query = {
                    params: {
                        filter: { order: "registered DESC" },
                    },
                };
            } else {
                query = {
                    params: {
                        filter: { access: 300, order: "login DESC" },
                    },
                };
            }

            this.$http.get("api/user/count", query).then(function (res) {
                this.$set(this, "userscount", res.data.count);
            });

            query.params.limit = this.widget.count;

            this.$http.get("api/user{/id}", query).then(function (res) {
                this.$set(this, "users", res.data.users.slice(0, this.widget.count));
            });
        },
    },
};

window.Dashboard.components.user = module.exports;
</script>
