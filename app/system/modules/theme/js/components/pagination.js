import UIkit from 'uikit';
import { $, on, css, attr, addClass, data, removeClass, hasClass, toNodes, append, find, findAll, empty, getIndex, trigger, closest } from 'uikit-util';

let active;

UIkit.component('pagination', {

    props: {
        items: Number,
        itemsOnPage: Number,
        pages: Number,
        displayedPages: Number,
        edges: Number,
        currentPage: Number,
        lblPrev: Boolean,
        lblNext: Boolean,
        onSelectPage() {},
    },

    data: {
        items: 1,
        itemsOnPage: 1,
        pages: 0,
        displayedPages: 7,
        edges: 1,
        currentPage: 0,
        lblPrev: false,
        lblNext: false,
        onSelectPage() {},
    },

    connected() {
        const $this = this;

        this.pages = this.pages ? this.pages : Math.ceil(this.items / this.itemsOnPage) ? Math.ceil(this.items / this.itemsOnPage) : 1;
        this.currentPage = this.currentPage;
        this.halfDisplayed = this.displayedPages / 2;

        // this._render();
    },

    events: [
        {

            name: 'click',

            delegate() {
                return 'a[data-page]';
            },

            handler(e) {
                e.preventDefault();
                this.selectPage(data(closest(e.target, 'a[data-page]'), 'page'));
            },

        },
    ],

    methods: {

        _getInterval() {
            return {
                start: Math.ceil(this.currentPage > this.halfDisplayed ? Math.max(Math.min(this.currentPage - this.halfDisplayed, (this.pages - this.displayedPages)), 0) : 0),
                end: Math.ceil(this.currentPage > this.halfDisplayed ? Math.min(this.currentPage + this.halfDisplayed, this.pages) : Math.min(this.displayedPages, this.pages)),
            };
        },

        render(pages) {
            this.pages = pages || this.pages;
            this._render();
        },

        selectPage(pageIndex, pages) {
            this.currentPage = pageIndex;
            this.render(pages);

            this.onSelectPage.apply(this, [pageIndex]);
            trigger(this.$el, 'select.uk.pagination', [this, pageIndex]);
        },

        _render() {
            const o = this,
                  interval = this._getInterval();
            let i;

            if (this.displayedPages - (interval.end - interval.start) < 0) {
                return
            }

            empty($(this.$el));

            // Generate Prev link
            if (o.lblPrev) this._append(this.currentPage - 1, { text: o.lblPrev });

            // Generate start edges
            if (interval.start > 0 && o.edges > 0) {
                const end = Math.min(o.edges, interval.start);

                for (i = 0; i < end; i++) this._append(i);

                if (o.edges < interval.start && (interval.start - o.edges != 1)) {
                    append(this.$el, '<li class="uk-disabled"><span>...</span></li>');
                } else if (interval.start - o.edges == 1) {
                    this._append(o.edges);
                }
            }

            // Generate interval links
            for (i = interval.start; i < interval.end; i++) this._append(i);

            // Generate end edges
            if (interval.end < this.pages && o.edges > 0) {
                if (this.pages - o.edges > interval.end && (this.pages - o.edges - interval.end != 1)) {
                    append(this.$el, '<li class="uk-disabled"><span>...</span></li>');
                } else if (this.pages - o.edges - interval.end == 1) {
                    this._append(interval.end++);
                }

                const begin = Math.max(this.pages - o.edges, interval.end);

                for (i = begin; i < this.pages; i++) this._append(i);
            }

            // Generate Next link (unless option is set for at front)
            if (o.lblNext) this._append(this.currentPage + 1, { text: o.lblNext });
        },

        _append(pageIndex, opts) {
            let item, options;

            // var isHtml = function (str) {
            //     return str[0] === '<' || str.match(/^\s*</);
            // }

            pageIndex = pageIndex < 0 ? 0 : (pageIndex < this.pages ? pageIndex : this.pages - 1);
            options = Object.assign({ text: pageIndex + 1 }, opts);

            item = (pageIndex == this.currentPage) ? `<li class="uk-active"><span>${options.text}</span></li>` : `<li><a href="#page-${pageIndex + 1}" data-page="${pageIndex}">${options.text}</a></li>`;

            if (typeof options.text === 'string') {
                let str = String(options.text);
                // if (isHtml(str)) {
                    item = (pageIndex == this.currentPage) ? `<li class="uk-hidden"><span>${options.text}</span></li>` : item;
                // }
            }

            if (typeof options.text === 'boolean') return

            append(this.$el, item);
        },

    },

});
