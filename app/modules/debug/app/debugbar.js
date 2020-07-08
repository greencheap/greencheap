const Debugbar = Vue.extend(require('./debugbar.vue').default);

Debugbar.component('time-component', require('./components/time.vue').default);
Debugbar.component('system', require('./components/system.vue').default);
Debugbar.component('events', require('./components/events.vue').default);
Debugbar.component('routes', require('./components/routes.vue').default);
Debugbar.component('memory', require('./components/memory.vue').default);
Debugbar.component('database', require('./components/database.vue').default);
// Debugbar.component('request', require('./components/request.vue'));
Debugbar.component('auth', require('./components/auth.vue').default);
Debugbar.component('log', require('./components/log.vue').default);
Debugbar.component('profile', require('./components/profile.vue').default);

Vue.ready(() => {
    // new Debugbar().$mount().$appendTo('body');
    const debugbar = new Debugbar().$mount();
    UIkit.util.append(UIkit.util.$('body'), debugbar.$el);
});

module.exports = Debugbar;
