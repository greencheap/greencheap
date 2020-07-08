import {$, on, css, addClass, removeClass, hasClass, findAll} from 'uikit-util';

import MenuObject from './menu';

export default {

    name: 'sidebar',

    mixins: [MenuObject],

    type: 'theme-menu',

    data() {
        return {
            sidemenu: Theme && Theme.SidebarItems ? Theme.SidebarItems : []
        }
    },

    theme: {
        elements() {
            var vm = this;
            return {
                additem: {
                    scope: 'sidemenu',
                    type: 'dropdown',
                    icon: {
                        class: () => 'tm-menu-image',
                        attrs: {
                            'uk-icon': 'plus',
                            'ratio': 1.2
                        },
                        internal: true
                    },
                    dropdown: {
                        options: () => {
                            if (!vm.sb || !vm.breakpoint) {
                                return 'pos: right-top; mode: hover; offset: 0; delayHide: 0'
                            }
                            return 'pos: bottom-right; mode: click; offset: 0;'
                        },
                        class: () => {
                            if (!vm.sb || !vm.breakpoint) {
                                return {'tm-dropdown-parent-items uk-position-fixed': true}
                            }
                            return {'tm-dropdown-parent-items uk-position-fixed tm-left': true}
                        },
                        style: () => {
                            if (vm.sb) return { width: vm.size }
                        }
                    },
                    items: () => vm.sidemenu.additem ? vm.sidemenu.additem : []
                },
                menuitem: {
                    scope: 'sidemenu',
                    type: 'dropdown',
                    icon: {
                        class: () => 'tm-menu-image',
                        attrs: {
                            'uk-navbar-toggle-icon': true
                        },
                        internal: true
                    },
                    dropdown: {
                        options: () => {
                            if (!vm.sb || !vm.breakpoint) {
                                return 'pos: right-top; mode: hover; offset: 0; delayHide: 0'
                            }
                            return 'pos: bottom-right; mode: click; offset: 0;'
                        },
                        class: () => {
                            if (!vm.sb || !vm.breakpoint) {
                                return {'tm-dropdown-parent-items uk-position-fixed': true}
                            }
                            return {'tm-dropdown-parent-items uk-position-fixed tm-left': true}
                        },
                        style: () => {
                            if (vm.sb) return { width: vm.size }
                        }
                    },
                    items: () => vm.sidemenu.menuitem ? vm.sidemenu.menuitem : [], //vm.sidemenu.additem,//vm.sidemenu.additem,
                    priority: 0
                }
            }
        }
    },

    computed: {
        sbStyle() {
            return (this.sb) ? { width: this.size } : {}
        },
    },

    beforeCreate() {},

    created() {

        this.size = this.sbSize(this.nav);

        this.$el = this.$el || $('.tm-sidebar-left');

        this.$nextTick(() => {
            this.page = $('.tm-page');
            if (this.sb) {
                css(this.page, 'margin-left', this.size);
                addClass(this.$el, 'tm-sidebar-open');
            }
        })

    },

    mounted() {},

    methods: {

        toggleSidebar() {
            const actions = $('.tm-action-buttons', this.$el);
            const buttons = findAll('li:not(.tm-toggle-sidebar)', actions);

            if (!this.sb) css(buttons, 'display', 'none');

            this.sb = !this.sb;
            this.setStyle();
            this.$session.set('admin.sidebar', this.sb);

            if (this.sb) setTimeout(() => { css(buttons, 'display', 'list-item') }, 200)
        },

        showDropdown(item) {
            if (this.getChildren(item).length) {
                if (this.sb && item.active) {
                    return false
                }
                return true
            }
            return false
        },

        setStyle() {
            if (!hasClass(this.page, 'transition')) addClass(this.page, 'transition');

            if (this.sb) {
                addClass(this.$el, 'tm-sidebar-open');
                css(this.page, 'margin-left', this.size);
            } else {
                removeClass(this.$el, 'tm-sidebar-open');
                css(this.page, 'margin-left', '');
            }
        },

        sbSize(menu) {
            const selector    = $('.tm-sidebar-menu > li > a > .tm-menu-text'),
                  toggle      = $('.tm-sidebar-menu > li');
            let   result      = 0,
                  maxWidth    = 0,
                  defaultSize = 220,
                  rightSpace  = 35,
                  sizes       = Object.assign({}, menu),
                  font        = selector ? css(selector, 'font') : '';

            if (!font && selector) {
                let obj = css(selector, ['font-style', 'font-variant', 'font-weight', 'font-size', 'font-family']);
                font = Object.values(obj).join(' ').replace(/ +/g, ' ').trim();
            }

            sizes = _.map(sizes, item => this.textSize(this.$trans(item.label), font));
            maxWidth = Math.max.apply(null, sizes)

            result = Math.ceil((maxWidth
                    + parseInt(css($('.tm-menu-image', toggle), 'width'))
                    + rightSpace)/10)*10;

            return `${result ? result < defaultSize ? defaultSize : result : defaultSize}px`;
        },

        textSize(txt, font) {
            const element = document.createElement('canvas');
            const context = element.getContext('2d');

            context.font = font;
            return context.measureText(txt).width;
        },

    },

};
