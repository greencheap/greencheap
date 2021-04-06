import Version from "../lib/version";
var marked = require("marked");
var Update = {
    el: "#app",

    name: "UpdateSystem",

    data() {
        return _.merge(
            {
                hasUpdate: false,
                isInstall: false,
                output: "",
                status: "success",
                finished: false,
                progressbar: 0,
            },
            window.$client
        );
    },

    mounted() {
        this.getVersion();
    },

    computed: {
        isVersionSame() {
            if (Version.compare(this.hasUpdate.php_version, this.client_php_version, "<")) {
                return true;
            }
            return false;
        },
    },

    methods: {
        getVersion() {
            this.clientResource("api/atomy/main_packages/checkversion", {
                stability: this.settings.beta ? "STATUS_BETA" : "STATUS_PUBLISHED",
                php_version: "8.0.*",
                version: this.version,
            }).then((res) => {
                const data = res.data;
                if (Version.compare(this.version, data.version, "<")) {
                    this.hasUpdate = data;
                    return;
                }
                this.hasUpdate = false;
            });
        },

        changelog(md) {
            const renderer = new marked.Renderer();
            let section;

            renderer.heading = function (text) {
                section = text;
                return "";
            };

            renderer.listitem = function (text) {
                switch (section) {
                    case "Added":
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-label-primary uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                    case "Deprecated":
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                    case "Removed":
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                    case "Fixed":
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                    case "Security":
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                    default:
                        return `
                    <div class="uk-width-1-6">
                        <span class="uk-label uk-text-capitalize uk-text-center uk-width-expand">
                            ${section}
                        </span>
                    </div>
                    <div class="uk-width-5-6">${text}</div>`;
                }
            };

            renderer.list = function (text) {
                return text;
            };

            return marked(md, { renderer });
        },

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

        doDownload() {
            this.isInstall = true;
            this.output = "";
            this.output += "Server Side Connecting..\n";
            setTimeout(() => {
                this.output += "New version downloading\n";
                this.progressbar = 40;
            }, 2000);
            const ref = this;
            this.progressbar = 1;
            this.$http
                .get(
                    "admin/system/update/api/download-release",
                    {
                        params: {
                            url: this.hasUpdate.download_url,
                        },
                    },
                    {
                        xhr: {
                            progress(e) {
                                if (e.lengthComputable) {
                                    ref.progressbar = (e.loaded / e.total) * 50;
                                    ref.setOutput(this.responseText);
                                }
                            },
                        },
                    }
                )
                .then(() => {
                    this.output += "Finish Download...\n\n";
                    this.progressbar = 78;
                    this.doInstall();
                })
                .catch((err) => {
                    this.$notify(err.bodtText, "danger");
                    this.isInstall = false;
                });
        },

        doInstall() {
            this.output += "Starting Update...\n\n";
            this.progressbar = 79;
            this.$http
                .post("admin/system/update/update")
                .then((res) => {
                    this.setOutput(res.bodyText);
                    this.doMigration();
                })
                .catch(() => {
                    this.error();
                });
        },

        setOutput(output) {
            const lines = output.split("\n");
            const match = lines[lines.length - 1].match(/^status=(success|error)$/);
            if (match) {
                this.status = match[1];
                delete lines[lines.length - 1];
                this.output += lines.join("\n");
            } else {
                this.output += output;
            }
        },

        error() {
            this.$notify(this.$trans("Whoops, something went wrong."));
        },

        doMigration() {
            this.progressbar = 100;
            if (this.status === "success") {
                this.$http.get("admin/system/migration/migrate").then((res) => {
                    const { data } = res;
                    this.output += `\n\n${data.status}`;
                    setTimeout(() => {
                        this.finished = true;
                    }, 3000);
                }, this.error);
            } else {
                this.error();
            }
        },
    },
};

Vue.ready(Update);
