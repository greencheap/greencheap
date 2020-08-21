export default {

    data() {
        return {
            pkg: {},
            updatePkg: {},
            output: '',
            status: 'loading',
            options: {
                bgClose: false,
                escClose: false,
            },
            showOutput: false,
            filterPhrase: 'Dependency resolution completed',
        };
    },

    created() {
        this.$mount();
    },

    methods: {

        init(request) {
            this.open();
        },

        setOutput(output) {
            // Filtered output
            this.showOutput = true;
            const lines = output.split('\n');
            const match = lines[lines.length - 1].match(/^status=(success|error)$/);

            if (match) {
                this.status = match[1];
                delete lines[lines.length - 1];
                this.output = lines.join('\n');
            } else {
                this.output = output;
            }

            this.scrollToEnd();
        },

        scrollToEnd() {
            let container = this.$el.querySelector(".pk-pre");
            if (container && container.scrollHeight) container.scrollTop = container.scrollHeight;
        },

        open() {
            this.$refs.output.open();
            UIkit.util.on(this.$refs.output.modal.$el, 'hide', this.onClose);
        },

        close() {
            this.scrollToEnd();
            this.$refs.output.close();
        },

        closeSecond() {
            this.close();
            location.reload();
        },

        onClose() {
            if (this.cb) {
                console.log(this)
                this.cb(this);
            }

            this.$destroy();
        },

    },

    watch: {
        status() {
            if (this.status !== 'loading') {
                this.$refs.output.modal.$options.props.bgClose = true;
                this.$refs.output.modal.$options.props.keyboard = true;
            }
        },
    },

};
