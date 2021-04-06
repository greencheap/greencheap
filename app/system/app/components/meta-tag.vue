<template>
    <div>
        <div class="uk-margin">
            <label for="form-meta-title" class="uk-form-label"
                >{{ "Meta Title" | trans }} <span v-show="countTitle > 0">{{ countTitle }}</span></label
            >
            <div class="uk-form-controls">
                <input id="form-meta-title" v-model="source.data.vmeta['og:title']" class="uk-form-width-large uk-input" :class="{ 'uk-form-danger': countTitle > 75, 'uk-form-success': countTitle > 60 && countTitle < 74 }" type="text" :placeholder="source.title" />
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-meta-description" class="uk-form-label"
                >{{ "Meta Description" | trans }} <span v-show="countDesc > 0">{{ countDesc }}</span></label
            >
            <div class="uk-form-controls">
                <textarea id="form-meta-description" v-model="source.data.vmeta['og:description']" :class="{ 'uk-form-danger': countDesc > 165, 'uk-form-success': countDesc > 140 && countDesc < 166 }" class="uk-form-width-large uk-textarea" rows="5" type="text" />
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-meta-title" class="uk-form-label">{{ "Meta Keyword" | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-meta-title" v-model="source.data.vmeta['og:keyword']" class="uk-form-width-large uk-input" type="text" />
            </div>
        </div>

        <div class="uk-margin uk-section uk-section-muted uk-padding-small">
            <div class="">
                <h3 class="uk-h4 source-green">{{ getTitle }} | {{ greencheap.name }}</h3>
                <p class="uk-margin-remove uk-text-meta source-blue">{{ greencheap["project-uri"] }}/{{ source.slug }}</p>
                <p class="uk-margin-remove uk-text-meta">{{ getDesc }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "VMetaTag",
    props: ["source"],

    data() {
        return {
            resource: null,
            greencheap: window.$greencheap,
        };
    },

    created() {
        if (!this.source["vmeta"]) {
            this.$set(
                this,
                "resource",
                _.merge(
                    {
                        vmeta: {
                            "og:title": "",
                            "og:description": "",
                            "og:keyword": "",
                        },
                    },
                    this.source.data
                )
            );
            this.source.data = this.resource;
        }
    },

    computed: {
        getTitle() {
            var msg = null;
            if (this.source.data.vmeta["og:title"].length) {
                msg = this.source.data.vmeta["og:title"];
            } else if (this.source.title) {
                msg = this.source.title;
            } else {
                msg = "";
            }
            if (msg.length > 0) {
                return msg.substr(0, 75);
            }
            return msg;
        },

        getDesc() {
            var msg;
            if (this.source.data.vmeta["og:description"].length) {
                msg = this.source.data.vmeta["og:description"];
                return msg.substr(0, 165);
            }
            return;
        },

        countTitle() {
            var msg, count;

            if (this.source.data.vmeta["og:title"].length) {
                msg = this.source.data.vmeta["og:title"] + " | " + this.greencheap.name;
                return msg.length;
            }
            return 0;
        },

        countDesc() {
            var msg, count;

            if (this.source.data.vmeta["og:description"].length) {
                msg = this.source.data.vmeta["og:description"];
                return msg.length;
            }
            return 0;
        },
    },
};
</script>

<style>
.source-green {
    color: #45ae3c;
    margin: 0px;
    font-weight: bold;
    font-size: 15px;
}

.source-blue {
    color: #5353f2;
}
</style>
