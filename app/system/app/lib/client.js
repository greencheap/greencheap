module.exports = {
    data() {
        return _.merge(
            {
                errorTemplate: {
                    isActive: false,
                    message: "",
                    title: "",
                },
            },
            window.$client
        );
    },

    computed: {
        isError() {
            if (this.errorTemplate.isActive) {
                return true;
            }
            return false;
        },

        getTitle() {
            return this.errorTemplate.title;
        },

        getMessage() {
            return this.errorTemplate.message;
        },
    },

    methods: {
        clientResource(url, parameter) {
            const urlParam = `${this.system_api}/${url}`;
            if (!parameter) {
                parameter = {};
            }
            const http = this.$http.get(urlParam, {
                params: parameter,
            });
            return http;
        },

        abort(active = false, message = "", title = "Error") {
            this.errorTemplate.isActive = active;
            this.errorTemplate.message = message;
            this.errorTemplate.title = title;
        },

        changelog(md) {
            const renderer = new marked.Renderer();
            let section;

            // eslint-disable-next-line func-names
            renderer.heading = function (text) {
                section = text;
                return "";
            };

            // eslint-disable-next-line func-names
            renderer.listitem = function (text) {
                switch (section) {
                    case "Added":
                        return `<div class="uk-width-1-6"><span class="uk-label uk-label-primary uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                    case "Deprecated":
                        return `<div class="uk-width-1-6"><span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                    case "Removed":
                        return `<div class="uk-width-1-6"><span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                    case "Fixed":
                        return `<div class="uk-width-1-6"><span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                    case "Security":
                        return `<div class="uk-width-1-6"><span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                    default:
                        return `<div class="uk-width-1-6"><span class="uk-label uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                }
            };

            // eslint-disable-next-line func-names
            renderer.list = function (text) {
                return text;
            };

            return marked(md, { renderer });
        },
    },
};
