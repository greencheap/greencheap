import UIkit from 'uikit';
import { $, on, css, attr, addClass, removeClass, hasClass, toNodes, append, find, findAll, empty, getIndex, trigger } from 'uikit-util';

let active;

UIkit.component('autocomplete', {

    props: {
        minLength: Number,
        delay: Number,
        loadingClass: String,
        flipDropdown: Boolean,
        skipClass: String,
        hoverClass: String,
        source: null,
        renderer: null,
        template: String,
    },

    data: {
        minLength: 3,
        param: 'search',
        method: 'post',
        delay: 300,
        loadingClass: 'uk-loading',
        flipDropdown: false,
        skipClass: 'uk-skip',
        hoverClass: 'uk-active',
        source: null,
        renderer: null,
        template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">{{~items}}<li data-value="{{$item.value}}"><a>{{$item.value}}</a></li>{{/items}}</ul>',

        visible: false,
        value: null,
        selected: null,
    },

    created() {

        this.template = this.rtemplate(this.template);
        this.dropdown = $('<div class="uk-dropdown"></div>');
        attr(this.dropdown, 'aria-expanded', 'false');

    },

    connected() {
        const $this = this;

        this.isSelect = false,
        this.trigger = this.debounce(function (e) {
            if (this.isSelect) {
                return (this.isSelect = false);
            }

            $this.handle();
        }, this.delay);

        on(document, 'click', (e) => {
            if (active && e.target != active.input[0]) {
                active.hide();
            }
        });

        // this.template = this.rtemplate(this.template);

        // this.dropdown = $('<div class="uk-dropdown"></div>');
        append(this.$el, this.dropdown);
        // attr(this.dropdown, 'aria-expanded', 'false');
        attr(find('input', this.$el), 'autocomplete', 'off');
        this.input = find('input', this.$el);

        this.triggercomplete = this.trigger;
    },

    events: [
        {
            name: 'keydown',

            delegate() {
                return 'input';
            },

            handler(e) {
                if (e && e.which && !e.shiftKey && this.visible) {
                    switch (e.which) {
                    case 13: // enter
                        this.isSelect = true;

                        if (this.selected) {
                            e.preventDefault();
                            this.select();
                        }
                        break;
                    case 38: // up
                        e.preventDefault();
                        this.pick('prev', true);
                        break;
                    case 40: // down
                        e.preventDefault();
                        this.pick('next', true);
                        break;
                    case 27:
                    case 9: // esc, tab
                        this.hide();
                        break;
                    default:
                        break;
                    }
                }
            },
        },

        {
            name: 'keyup',

            delegate() {
                return 'input';
            },

            handler(e) {
                this.trigger(e);
            },
        },

        {
            name: 'click',

            delegate() {
                return '.uk-autocomplete-results > *';
            },

            handler(e) {
                this.select();
            },
        },

        {
            name: 'mouseover',

            delegate() {
                return '.uk-autocomplete-results > *';
            },

            handler(e) {
                if (e.target.parentNode.tagName.toLowerCase() != 'li') return;
                this.pick($(e.target.parentNode));
            },
        },
    ],

    methods: {

        handle() {
            const $this = this; const
                old = this.value;

            this.value = this.input.value;

            if (this.value.length < this.minLength) return this.hide();

            if (this.value != old) {
                $this.request();
            }

            return this;
        },

        debounce(func, wait, immediate) {
            let timeout;
            return function () {
                const context = this; const
                    args = arguments;
                const later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        pick(item, scrollinview) {
            const $this = this;
            const items = findAll(`>:not(.${this.skipClass})`, find('.uk-autocomplete-results', $(this.dropdown)));
            let selected = false;

            if (typeof item !== 'string' && !hasClass($(item), this.skipClass)) {
                selected = item;
            } else if (item == 'next' || item == 'prev') {
                if (this.selected) {
                    const index = getIndex(this.selected, items);

                    if (item == 'next') {
                        selected = toNodes(items)[index + 1 < items.length ? index + 1 : 0];
                    } else {
                        selected = toNodes(items)[index - 1 < 0 ? items.length - 1 : index - 1];
                    }
                } else {
                    selected = !this.selected ? items[0] : items[getIndex((item == 'next') ? 'next' : 'previous', items)];
                }

                selected = $(selected);
            }

            if (selected) {
                this.selected = selected;

                items.forEach((el) => {
                    removeClass(el, $this.hoverClass);
                });

                addClass($(this.selected), this.hoverClass);

                // jump to selected if not in view
                if (scrollinview) {
                    const { top } = selected.getBoundingClientRect();
                    const { scrollTop } = $this.dropdown;
                    const dpheight = $this.dropdown.offsetHeight;

                    if (top > dpheight || top < 0) {
                        UIkit.util.scrollTop($this.dropdown, scrollTop + top);
                    }
                }
            }
        },

        select() {
            if (!this.selected) return;

            const data = Object.assign({}, this.selected.dataset);

            trigger(this.$el, 'select', [this, data]);

            if (data.value) {
                this.input.value = data.value;
                trigger(this.input, 'change');
            }

            this.hide();
        },

        show() {
            const $this = this;

            if (this.visible) return;

            this.visible = true;

            // addClass($(this.$el), 'uk-open');
            addClass($(this.dropdown), 'uk-open');

            if (active && active !== this) {
                active.hide();
            }

            active = this;

            // Update aria
            attr($(this.dropdown), 'aria-expanded', 'true');

            return this;
        },

        hide() {
            if (!this.visible) return;
            this.visible = false;
            // removeClass($(this.$el), 'uk-open');
            removeClass($(this.dropdown), 'uk-open');

            if (active === this) {
                active = false;
            }

            // Update aria
            attr($(this.dropdown), 'aria-expanded', 'false');

            return this;
        },

        request() {
            const $this = this;
            const release = function (data) {
                if (data) {
                    $this.render(data);
                }

                removeClass($this.$el, $this.loadingClass);
            };

            addClass(this.$el, this.loadingClass);

            if (this.source) {
                const { source } = this;

                switch (typeof (this.source)) {
                case 'function':

                    this.source.apply(this, [release]);

                    break;

                case 'object':

                    if (source.length) {
                        const items = [];

                        source.forEach((item) => {
                            if (item.value && item.value.toLowerCase().indexOf($this.value.toLowerCase()) != -1) {
                                items.push(item);
                            }
                        });

                        release(items);
                    }

                    break;

                case 'string':

                    var params = {};

                    params[this.param] = this.value;

                    UI.$.ajax({
                        url: this.source,
                        data: params,
                        type: this.method,
                        dataType: 'json',
                    }).done((json) => {
                        release(json || []);
                    });

                    break;

                default:
                    release(null);
                }
            } else {
                removeClass(this.$el, $this.loadingClass);
            }
        },

        render(data) {
            empty($(this.dropdown));

            this.selected = false;

            if (this.renderer) {
                this.renderer.apply(this, [data]);
            } else if (data && data.length) {
                this.dropdown.append(this.template({ items: data }));
                append(this.dropdown, this.template({ items: data }));
                this.show();

                this.trigger('show');
            }

            return this;
        },

        rtemplate(str, data) {
            const tokens = str.replace(/\n/g, '\\n').replace(/\{\{\{\s*(.+?)\s*\}\}\}/g, '{{!$1}}').split(/(\{\{\s*(.+?)\s*\}\})/g);
            let i = 0; let toc; let cmd; let prop; let val; let fn; const output = []; let
                openblocks = 0;

            while (i < tokens.length) {
                toc = tokens[i];

                if (toc.match(/\{\{\s*(.+?)\s*\}\}/)) {
                    i += 1;
                    toc = tokens[i];
                    cmd = toc[0];
                    prop = toc.substring(toc.match(/^(\^|\#|\!|\~|\:)/) ? 1 : 0);

                    switch (cmd) {
                    case '~':
                        output.push(`for(var $i=0;$i<${prop}.length;$i++) { var $item = ${prop}[$i];`);
                        openblocks++;
                        break;
                    case ':':
                        output.push(`for(var $key in ${prop}) { var $val = ${prop}[$key];`);
                        openblocks++;
                        break;
                    case '#':
                        output.push(`if(${prop}) {`);
                        openblocks++;
                        break;
                    case '^':
                        output.push(`if(!${prop}) {`);
                        openblocks++;
                        break;
                    case '/':
                        output.push('}');
                        openblocks--;
                        break;
                    case '!':
                        output.push(`__ret.push(${prop});`);
                        break;
                    default:
                        output.push(`__ret.push(escape(${prop}));`);
                        break;
                    }
                } else {
                    output.push(`__ret.push('${toc.replace(/\'/g, "\\'")}');`);
                }
                i += 1;
            }

            fn = new Function('$data', [
                'var __ret = [];',
                'try {',
                'with($data){', (!openblocks ? output.join('') : '__ret = ["Not all blocks are closed correctly."]'), '};',
                '}catch(e){__ret = [e.message];}',
                'return __ret.join("").replace(/\\n\\n/g, "\\n");',
                "function escape(html) { return String(html).replace(/&/g, '&amp;').replace(/\"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');}",
            ].join('\n'));

            return data ? fn(data) : fn;
        },

    },

});
