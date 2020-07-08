var $helper = '[ThemeHelper]';

var ElementTypes = ['dropdown', 'button', 'iconnav', 'caption', 'search', 'pagination'];
var Protected    = ['sidemenu.additem', 'sidemenu.menuitem'];

var isString = function (value) {
    return typeof value === 'string';
}

var isFunction = function(obj) {
    return typeof obj === 'function';
}

var isObject = function(obj) {
    return obj !== null && typeof obj === 'object';
}

var isUndefined = function(value) {
    return value === void 0;
}

var isHtml = function(str) {
    return str[0] === '<' || str.match(/^\s*</);
}

var objLength = function(obj) {
    return isObject(obj) ? Object.keys(obj).length ? Object.keys(obj).length : '' : '';
}

var isElement = function(type) {
    return type && ElementTypes.indexOf(type) !== -1;
}

var actionIcons = function(name) {
    var self = this;
    var icons = {
        publish:   { icon: 'check', label: 'Publish', class: 'uk-text-success' },
        unpublish: { icon: 'ban', label: 'Unpublish', class: 'uk-text-danger' },
        move:      { icon: 'move', label: 'Move' },
        copy:      { icon: 'copy', label: 'Copy' },
        trash:     { icon: 'trash', label: 'Trash' },
        rename:    { icon: 'file-edit', label: 'Rename' },
        remove:    { icon: 'trash', label: 'Remove' },
        table:     { icon: 'table', label: 'Table View' },
        thumbnails:{ icon: 'thumbnails', label: 'Thumbnails View' },
        delete:    { icon: 'trash', label: 'Delete' },
        spam:      { class: 'pk-icon-spam uk-icon-hover', label: 'Mark as spam' },
        get icon() {
            var item = this[name],
                element = {
                    'uk-icon': item.icon,
                    'uk-tooltip': self.$trans(item.label),
                }
            if (item.class) element.class = item.class;
            return element;
        }
    }
    return objLength(icons[name]) && icons.icon
}

var log = function(type, key, value) {
    var name = this.name && `[${this.name.charAt(0).toUpperCase()}${this.name.slice(1)}]`,
        messages = {
        exist: `${$helper}${name} Can't create element '${value}' - already exist, extend if needed.`,
        empty: `${$helper}${name} The '${value}' is not an element or has empty items.`,
        protected: `${$helper}${name} The element '${value}' can't be change, extend if needed.`,
        tab_ele_uniq: `${$helper}${name} Unable to create tabs, element '${value}' not found or not unique on page.`,
        watch_fn: `${$helper}${name} Watch property of object - '${value}' -  must be a function.`
    };

    console[type] ? console[type](messages[key] ? messages[key] : 'Error') : '';
}

var ThemeTabs = function(name, ele, option) {

    if (!UIkit.util.$(ele) && UIkit.util.$$(ele).length > 1) {
        this.$theme.log('warn', 'tab_ele_uniq', ele);
        return;
    }

    if (!_.has(this.$data, '$themeElements')) return;

    var element, elements = this.$data.$themeElements;

    this.$set(elements, name, {
        tab: {},
        type: 'tab',
        activeTab: ''
    })

    element = elements[name];

    if (option.state) {
        var stateName = this.$theme.name.replace(/\-|\//g,'') + '.' + name;
        element.activeIndex = this.$session.get(stateName, 0);
    }

    this.$on('hook:mounted', function() {
        var self = this;
        element.tab  = UIkit.switcher(ele, option);

        UIkit.util.on(element.tab.connects, 'show', function(e, tab) {
            if (tab !== element.tab) return false;
            var baseComponent = self;
            if (_.has(self.$children[0], '$_veeObserver')) {
                baseComponent = self.$children[0];
            }
            baseComponent.$children.forEach((component, idx) => {
                if (component.$el === e.target.firstChild) {
                    let tabName = component.$options.name || component.$options._componentTag;
                    self.$set(element, 'activeTab', tabName);
                    if (option.state) {
                        self.$session.set(stateName, idx);
                    }
                }
            })
        })

        element.tab.toggles.forEach((item, idx) => {
            if (item.parentNode.classList.contains('uk-active')) {
            element.activeIndex = idx;
            };
        })

        element.tab.show(element.activeIndex);
    })

}

function renderElement(h, element, _vue) {
    var $this      = _vue,
        { $trans } = _vue,
        { $theme } = _vue.$parent;

    var get        = $theme.get.bind($theme);
    var Icons   = actionIcons.bind(_vue);

    var addProps = function(element, item) {
        if (!item.type) item.type = 'button';
        if (get(element,'actionDropdown')) {
            item.class = item.class + ' uk-dropdown-close';
        }
        if (get(element, 'actionIcons')) {
            item.icon  = {
                attrs: Icons(item.name),
                class: ['uk-margin-small-right', Icons(item.name).class],
                internal: true
            };
            item.class = item.type !== 'dropdown' ? 'uk-dropdown-close' : '';
            item.actionDropdown = item.type === 'dropdown' ? true : false;
            item.caption = $trans(get(item, 'caption')) || Icons(item.name)['uk-tooltip'];
        }
        return item
    }

    var render = {
        dropdown() {
            let dropdownOptions = () => {
                let options = get(this, 'dropdown.options');
                if (!/topmenu/.test(this.scope)) return options;
                return `${options}; offset: ${$this.offset}`;
            }
            return [
                render['button'].call(this),
                h('div', {
                    attrs: { 'uk-dropdown': dropdownOptions() },
                    class: get(this, 'dropdown.class'),
                    style: get(this, 'dropdown.style')
                }, [
                    h('ul', { class: 'uk-nav uk-dropdown-nav' }, [
                        $this.orderBy(get(this, 'items'), 'priority').map((item, name) => {
                            item = addProps(this, item);
                            return get(item, 'vif') && h('li', null, [
                                render[item.type].call(item)
                            ])
                        })

                    ])
                ])
            ]
        },
        button() {
            let spinner  = get(this, 'spinner'),
                icon     = _.has(this, 'icon') && get(this, 'icon.vif'),
                internal = icon && get(this, 'icon.internal'),
                getText  = (text) => {
                    var output, content, replace;
                    if (isHtml(text)) {
                        output  = UIkit.util.$('<div>'+text+'</div>');
                        content = output.textContent.trim();
                        replace = $trans(content);
                        output  = Vue.compile('<div>' + output.innerHTML.replace(content, replace) + '</div>');
                        if (typeof output.staticRenderFns[0] === 'function') {
                            output = output.staticRenderFns[0].call($this, h);
                            return output.children;
                        }
                        return text;
                    }

                    return $trans(text);
                },
                elements = (h) => {
                    let elements = [];
                    spinner && elements.push([
                        render.spinner(h)
                    ]);
                    icon && internal && elements.push([
                        h('span', {
                            attrs: get(this, 'icon.attrs'),
                            class: get(this, 'icon.class')
                        }),
                    ]);
                    if (get(this, 'caption')) {
                        if ((icon || spinner) && !internal) {
                            elements.push([
                                h('span', {class: 'uk-text-middle'}, getText(get(this, 'caption')))
                            ])
                        } else if (icon && internal) {
                            elements.push([
                                h('span', {class: 'uk-text-middle uk-margin-small-right'}, getText(get(this, 'caption')))
                            ])
                        } else if (!icon) {
                            elements.push([
                                getText(get(this, 'caption'))
                            ])
                        }
                    }
                    return elements;
                };

            return h('a', {
                attrs: internal ? get(this, 'attrs') : _.merge({}, get(this, 'attrs'), get(this, 'icon.attrs')),
                class: get(this, 'class'),
                style: get(this, 'style'),
                on: { click: (e) => get(this, 'on.click', e) },
                directives: Array.isArray(get(this, 'directives')) ? get(this, 'directives') : []
            }, elements(h))
        },
        iconnav() {
            let create = function (h, item) {
                let ele = {
                    type: 'button',
                    icon: { attrs: Icons(item.name) },
                    class: Icons(item.name).class
                }
                _.merge(ele, item);
                return render[ele.type].call(ele)
            }
            return h('ul', { class: 'uk-iconnav' }, get(this, 'items').map((item) =>
                get(item, 'vif') && h('li',{}, [ create(h, item) ])
            ))
        },
        caption() {
            if (get(this, 'class')) {
                return h('span', {class: get(this, 'class')},[get(this, 'caption')])
            }
            return get(this, 'caption')
        },
        pagination() {
            return h('div', {
                class: 'uk-flex uk-flex-middle'
            }, [
                h('span', {
                    class: 'uk-margin-small-right uk-text-small'
                }, $trans(get(this, 'caption'))),
                h('v-pagination', {
                    props: get(this, 'props'),
                    attrs: get(this, 'attrs'),
                    class: 'uk-margin-remove',
                    on: {
                        input: (e) => get(this, 'on.input', e)
                    }
                },[])
            ])
        },
        search() {
            Object.assign($this, {SearchElement: get(element, 'domProps.value')})
            return h('div', {
                class: ['uk-search uk-search-default', get(this, 'class')],
            }, [
                h('span', {
                    attrs: {'uk-search-icon': true},
                    class: 'uk-search-icon',
                }, []),
                h('input', {
                    attrs: _.extend({
                        type: 'search',
                        placeholder: $trans('Search') + '...'
                    }, get(this, 'attrs')),
                    class: 'uk-search-input',
                    domProps: {
                        value: get(this, 'domProps.value')
                    },
                    on: {
                        input: (e) => {
                            $this.SearchElement = e.target.value;
                            return get(this, 'on.input', e);
                        },
                    },
                    ref: 'SearchElement'
                }),
                h('a', {
                    attrs: {'uk-icon': 'close'},
                    class: ['uk-form-icon uk-form-icon-flip', $this.SearchElement ? '': 'uk-hidden'],
                    on: {
                        click: () => {
                            var event = document.createEvent('Event'),
                                input = $this.$refs.SearchElement;
                            event.initEvent('input', true, true);
                            input.value = '';
                            input.dispatchEvent(event);
                        }
                    }
                }, []),
            ]);
        },
        spinner() {
            return h('span', {
                class: 'tm-spinner-bounce uk-margin-small-right'
            },[
                h('span', {class: 'tm-bounce1'}),
                h('span', {class: 'tm-bounce2'}),
                h('span', {class: 'tm-bounce3'})
            ])
        }
    }

    return (element.type && typeof render[element.type] === 'function') ? render[element.type].call(element) : '';
}

var renderScope = function(h, scope, dir) {
    var $theme         = this.$parent.$theme;
    var get            = $theme.get.bind($theme);
    var getScope       = $theme.getScope.bind($theme);
    var isScopeEmpty   = $theme.isScopeEmpty.bind($theme);
    var isElementEmpty = $theme.isElementEmpty.bind($theme);

    var items   = getScope(dir ? dir : scope);
    var isEmpty = isScopeEmpty(items);

    var render = {
        topmenu: () => {
            return h('div', {
                class: ['tm-' + dir],
            }, [
                h('div', { class: ['tm-topmenu-item'] }, [
                    h('ul', { class: 'tm-action-buttons uk-subnav uk-flex uk-flex-middle uk-visible@m' },
                        this.orderBy(items, 'priority').map((element) =>
                            !isElementEmpty(element) && get(element, 'vif') &&
                            h('li', { class: { 'uk-disabled': get(element, 'disabled') } }, [ renderElement(h, element, this) ])
                        )
                    )
                ])
            ])
        },
        sidemenu: () => {
            var sb = this.$parent.sb, breakpoint = this.$parent.$breakpoint;
            return h('ul', { class: ['tm-action-buttons', {'uk-nav uk-nav-default': !sb}, {'uk-iconnav': sb}, {'uk-flex-nowrap': sb && isEmpty}] }, [
                h('li', {class:['tm-toggle-sidebar', {'uk-flex-last': sb}]},[
                    h('a', {
                        on: { click: () => this.$parent.toggleSidebar() },
                        attrs: { 'uk-tooltip': !sb ? this.$trans('Show') : false, pos: !sb ? 'right' : false }
                    }, [ h('span', { attrs: {'uk-icon': !sb ? 'more' : 'more-vertical'}, class: 'tm-menu-image' }) ])
                ]),
                !isEmpty &&
                this.orderBy(items, 'priority').map((element) =>
                    !isElementEmpty(element) && get(element, 'vif') &&
                    h('li', { class: { 'uk-disabled': get(element, 'disabled') } }, [ renderElement(h, element, this) ])
                ),
                sb && h('li', { class: 'uk-width-expand'}, [
                    isEmpty &&
                    h('div', { class: ['uk-height-1-1 uk-flex uk-flex-middle uk-light', {'uk-hidden': !breakpoint}] }, [ h('span', this.$trans('Extensions')) ])
                ])
            ])
        },
        breadcrumbs: () => {
            return h('ul', { class: 'tm-breadcrumbs uk-nav uk-visible@m' },
                this.orderBy(items, 'priority').map((element) =>
                    !isElementEmpty(element) && get(element, 'vif') &&
                    h('li', {
                        class: [{ 'uk-disabled': get(element, 'disabled') }, 'uk-h4 uk-margin-remove']
                    }, [ renderElement(h, element, this) ])
                )
            )
        },
        navbar: () => {
            return h('div', { class: [dir == 'navbar-right' ? 'uk-margin-right' : 'uk-navbar-item','uk-visible@m'] },
                this.orderBy(items, 'priority').map((element) =>
                    !isElementEmpty(element) && get(element, 'vif') &&
                    renderElement(h, element, this)
                )
            )
        }
    }

    return (typeof render[scope] === 'function') ? !isEmpty && render[scope].call() : '';
}

var ThemeTopMenu = {
    render(h) {
        var $theme = this.$parent.$theme;
        var scopes = ['topmenu-left', 'topmenu-center', 'topmenu-right'];

        var isScopes = scopes.map((scope) => $theme.isScopeEmpty($theme.getScope(scope))).filter((item) => !item).length;
        return isScopes && h('div', { class: 'tm-topmenu uk-visible@m'}, scopes.map((scope) => renderScope.call(this, h, 'topmenu', scope)))
    },
    created() {
        // define dropdown top offset
        let { $, getStyle } = UIkit.util;
        this.offset = parseInt(getStyle($('.uk-navbar-container'), 'paddingBottom'));
    }
}

var ThemeSideMenu = {
    render(h) {
        return renderScope.call(this, h, 'sidemenu')
    }
}

var ThemeBreadcrumbs = {
    render(h) {
        return renderScope.call(this, h, 'breadcrumbs')
    }
}

var ThemeNavbarItems = {
    props: ['dir'],
    render(h) {
        var scope = 'navbar-' + this.dir;
        return renderScope.call(this, h, 'navbar', scope)
    }
}

var ThemeMixins = Object.freeze({
    Helper: {
        created() {
            if (window.Theme && isFunction(window.Theme.$helper)) {
                let Helper = window.Theme.$helper;
                this.$theme = new Helper(this);
            }
        },
        methods: {
            objLength
        }
    },
    Components: {
        components: {
            ThemeTopMenu,
            ThemeSideMenu,
            ThemeBreadcrumbs,
            ThemeNavbarItems
        }
    },
    Elements: {
        data: () => ({ $themeElements: {} })
    }
})

var Theme = function () {
    this._init();
    this._initVM();
    this._initMixins();
}

Theme.prototype._init = function() {
    window.Theme && Object.assign(this, window.Theme);
}

Theme.prototype._initVM = function() {
    this.$vm = new Vue({ data:() => ({ Elements: {} }) })
}

Theme.prototype._initMixins = function() {
    Object.assign(this, {Mixins: ThemeMixins});
}

Theme.prototype._mountMenu = function(menus) {
    var data = menus;
    Vue.Promise.all(data.map((item)=>{
        item.$instance = Vue.extend(item.object);
        return new item.$instance;
    })).then((Components)=> {
        this.$vm.$emit('theme-menu:created');
        Components.forEach((Component, idx) => {
            Component.$mount(data[idx].target)
        })
    })
}

Theme.prototype.$mount = function(data) {
    var menus = data.filter((item) => item.object.type === 'theme-menu');
    menus.length && this._mountMenu(menus);
}

Object.defineProperties(Theme.prototype, {
    $helper: {
        get() {
            return ThemeHelper
        },
        enumerable: false
    },
})

var ThemeHelper = function(_vue) {
    if (!_vue._isVue) {
        return;
    }
    this._init(_vue);
}

ThemeHelper.prototype._init = function(_vue) {
    this.$vm = _vue;
    this._initComponents();
    this._initEvents();
}

ThemeHelper.prototype._initComponents = function() {
    this.$tabs = ThemeTabs.bind(this.$vm);
}

ThemeHelper.prototype._initEvents = function() {
    this.theme.$on('theme-menu:created', () => this.createElements().then(() => this.theme.$emit('theme-menu:items-ready')));
    this.theme.$on('theme-menu:items-ready', () => this.extendElements());
    this.$vm.$on('hook:mounted', () => this.setHidden());
}

ThemeHelper.prototype.createElements = function() {
    return Promise.all([this.addElements(), this.callWatches()])
}

ThemeHelper.prototype.addElements = function() {
    var elements = this.elementsOption;

    elements && isObject(elements) && !Array.isArray(elements) &&
    _.forEach(elements, (element, name) => {
        if (!element.scope) return;
        var path = [element.scope, name].join('.');
        if (isObject(element) && isElement(element.type) && !this.isProtected(path)) {
            try {
                if (!this.hasProp(path) || (this.hasProp(path) && !isUndefined(element.watch))) {
                    this.setProp(path, element)
                }
            } catch (e) {
                console.error(`${$helper} ${e}`);
            }
        } else {
            this.isProtected(path) && this.log('warn', 'protected', path);
            (!isObject(element) || !isElement(element.type)) && !this.isProtected(path) && this.log('warn', 'empty', path);
        }
    })
}

ThemeHelper.prototype.extendElements = function() {}

ThemeHelper.prototype.callWatches = function() {
    var elements = this.elementsOption,
        theme = this;

    elements &&
    _.forEach(elements, (element, key) => {
        if (!isUndefined(element.watch))
            if (isFunction(element.watch)) {
                this.$vm.$watch(element.watch, this.addElements.bind(theme));
            } else {
                this.log('error', 'watch_fn', key)
            }
    })
}

ThemeHelper.prototype.getScope = function(prop) {
    return Object.values(this.getProp(prop))
}

ThemeHelper.prototype.getProp = function(prop) {
    return !_.isEmpty(this.elements) && this.hasProp(prop) && _.get(this.elements, prop);
}

ThemeHelper.prototype.setProp = function(prop, value) {
    return _.set(this.elements, prop, value);
}

ThemeHelper.prototype.hasProp = function(prop) {
    return _.has(this.elements, prop)
}

ThemeHelper.prototype.get = function(item, prop, e) {
    if (!isObject(item))
        return;

    var value = _.get(item, prop);

    if (this.testProp(prop, 'vif')) {
        return isUndefined(value) ? true : this.test(value) ? true : false
    }
    if (this.testProp(prop, 'disabled'))
        return isUndefined(value) ? false : this.test(value)

    if (this.testProp(prop, 'attrs') || this.testProp(prop, 'props')) {
        var attrs = this.test(value, prop);
        _.forEach(attrs, (attr_value, attr) => {
            attrs[attr] = this.test(attr_value, attr);
        })
        return attrs;
    }

    if (this.testProp(prop, 'items') && !value) {
        return [];
    }

    return this.test(value, prop, e);
}

ThemeHelper.prototype.test = function(value, prop, e) {
    if (isUndefined(value))
        return false;
    if (isFunction(value)) {
        return this.test(value.call(this, e), prop)
    }
    if (isObject(value)) {
        if (this.testProp(prop, 'items')) {
            if (!Array.isArray(value)) {
                value = _.forEach(value, (item, key) => {
                    item.name = key;
                    return item;
                })
                return Object.values(value);
            }
        }
        return value;
    }
    return value;
}

ThemeHelper.prototype.testProp = function(prop, attr) {
    return prop && prop.indexOf(attr) !== -1;
}

ThemeHelper.prototype.isElementEmpty = function(element) {
    var type = element.type;

    if (!isElement(type))
        return true;

    if (type === 'dropdown') {
        var items = this.get(element, 'items');
        return !(Array.isArray(items) && objLength(items));
    }

    return false;
}

ThemeHelper.prototype.isScopeEmpty = function(menu) {
    return !(objLength(menu) && Object.values(menu).map((ele)=>!this.isElementEmpty(ele)).filter((val) => val != false).length) ? true : false;
}

ThemeHelper.prototype.isProtected = function(path) {
    var q = (Protected.indexOf(path) !== -1);
    if (q && !this.hasProp(path)) {
        return false;
    }
    return path && q;
}

ThemeHelper.prototype.activeTab = function(name, current) {
    var vue = this.$vm.$parent,
        path = [name, 'activeTab'].join('.');
    if (current && current._isVue) {
        var vue = current;
    }
    if (current && typeof current === 'boolean') {
        return _.get(vue.$data.$themeElements, path) === this.name;
    }
    return _.get(vue.$data.$themeElements, path);
}

ThemeHelper.prototype.actionIcons = function(name) {
    return actionIcons.call(this.$vm, name);
};

ThemeHelper.prototype.setHidden = function() {
    if (this.option && this.option.hiddenHtmlElements) {
        var elements = this.option.hiddenHtmlElements;
        if (isFunction(elements)) {
            elements = elements.call(this.$vm)
        }
        var hide = function(el) {
            var nodes = UIkit.util.$$(el);
            nodes.forEach((node) => UIkit.util.addClass(UIkit.util.$(node), 'uk-hidden@m'))
        }
        if (Array.isArray(elements)) {
            elements.forEach((element) => hide(element))
        } else if (isString(elements) || elements instanceof NodeList || elements instanceof Element) {
            hide(elements);
        }
    }
}

Object.defineProperties(ThemeHelper.prototype, {
    theme: {
        get() {
            var theme = window.Theme;
            return (theme && theme.$vm) ? theme.$vm : null;
        }
    },
    elements: {
        get() {
            return (this.theme && this.theme.Elements) ? this.theme.Elements : null;
        }
    },
    option: {
        get() {
            return (this.$vm && isObject(this.$vm.$options.theme)) ? this.$vm.$options.theme : null;
        }
    },
    elementsOption: {
        get() {
            return (this.option && isFunction(this.option.elements)) ? this.option.elements.bind(this.$vm).call() : null;
        }
    },
    extendOption: {
        get() {
            return (this.option && isFunction(this.option.elements)) ? this.option.elements.bind(this.$vm).call() : null;
        }
    },
    name: {
        get() {
            return this.$vm.$options.name || this.$vm.$options._componentTag;
        }
    },
    log: {
        get() {
            return log.bind(this);
        }
    }
})

export default {
    install (Vue) {
        Vue.component('v-loader-bounce', require('../components/loader-bounce.vue'));
        window.Theme = new Theme();
    }
}
