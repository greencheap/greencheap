<template>
    <div>
        <button class="uk-button uk-button-default uk-button-large gc-button-icon">
            <i class="uk-icon uk-icon-image" :style="'background-image:url(' + $url('/app/system/modules/theme/images/icons/bell.svg') + ');'" />
            <span v-if="isRead" class="gc-button-icon-point" />
        </button>
        <div uk-drop="mode: click;animation: uk-animation-slide-top-small; duration: 300">
            <div class="gc-border-radius uk-width-expand uk-box-shadow-medium uk-background uk-background-default">
                <ul v-if="notifications.length" class="uk-nav gc-notification">
                    <li v-for="noti in notifications" :key="noti.id">
                        <a :class="{ 'gc-notification-active': !noti.is_read }" @click.prevent="readNotification(noti)">
                            <img :src="$url(noti.image)" width="50" height="50" />
                            <div>
                                <p class="uk-margin-remove uk-text-small">{{ noti.title }}</p>
                                <span class="uk-display-block">{{ noti.date | relativeDate }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
                <div v-else class="uk-padding uk-text-center">
                    <span>{{ "We have not found any notifications" | trans }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notifications: false,
        };
    },

    computed: {
        isRead() {
            if (this.notifications.length) {
                return false;
            }
            const notifications = _(this.notifications).groupBy("is_read_string").value();
            if (!_.isEmpty(notifications.unread)) {
                return true;
            }
            return false;
        },
    },

    created() {
        this.resource = this.$resource("notifications{/id}");
    },

    mounted() {
        this.load();
    },

    methods: {
        load() {
            this.resource
                .query({
                    id: "get",
                })
                .then((res) => {
                    const { notifications } = res.body;
                    this.notifications = notifications;
                });
        },

        readNotification(notification) {
            notification.read_user.push(1);
            this.resource
                .save(
                    {
                        id: "read",
                    },
                    {
                        notification,
                    }
                )
                .then(() => {
                    this.load();
                });
        },
    },
};
</script>
