<template>
    <div>
        <div class="uk-card-badge">
            <ul class="uk-iconnav uk-invisible-hover">
                <li v-show="!editing" class="uk-light">
                    <a uk-icon="file-edit" class="uk-link-muted" :title="'Edit' | trans" uk-tooltip="delay: 500" @click.prevent="$parent.edit" />
                </li>
                <li v-show="!editing" class="uk-light">
                    <a uk-icon="more-vertical" class="uk-link-muted uk-sortable-handle" :title="'Drag' | trans" uk-tooltip="delay: 500" />
                </li>
                <li v-show="editing">
                    <a v-confirm="'Delete widget?'" uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="$parent.remove" />
                </li>
                <li v-show="editing">
                    <a uk-icon="check" :title="'Close' | trans" uk-tooltip="delay: 500" @click.prevent="$parent.save" />
                </li>
            </ul>
        </div>

        <div v-show="editing" class="uk-card-header pk-panel-teaser">
            <form class="uk-form-stacked" @submit.prevent>
                <div class="uk-margin">
                    <label for="form-city" class="uk-form-label">{{ 'Location' | trans }}</label>
                    <div class="uk-form-controls">
                        <div ref="autocomplete" class="uk-autocomplete uk-width-1-1">
                            <input
                                id="form-city"
                                ref="location"
                                class="uk-input uk-width-1-1"
                                type="text"
                                :placeholder="location"
                                autocomplete="off"
                                @blur="clear"
                            >
                        </div>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">{{ 'Unit' | trans }}</label>

                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-margin-small">
                            <label><input v-model="widget.units" class="uk-radio" type="radio" value="metric"><span class="uk-margin-small-left">{{ 'Metric' | trans }}</span></label>
                        </p>
                        <p class="uk-margin-small">
                            <label><input v-model="widget.units" class="uk-radio" type="radio" value="imperial"><span class="uk-margin-small-left">{{ 'Imperial' | trans }}</span></label>
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <div v-if="status !== 'loading'" class="uk-inline-clip pk-panel-background uk-light">
            <canvas class="" width="550" height="350"></canvas>
            <div class="uk-position-cover uk-width-1-1">
                <div class="uk-flex uk-flex-center uk-flex-column uk-height-1-1">
                    <h1 class="uk-margin-remove uk-text-center pk-text-xlarge" v-if="time">{{ time | date(format) }}</h1>
                    <h2 class="uk-h4 uk-text-center uk-margin-remove" v-if="time">{{ time | date('longDate') }}</h2>
                </div>
                <div class="uk-position-bottom uk-padding-small uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
                    <h3 class="uk-h4 uk-margin-remove" v-if="widget.city">{{ widget.city }}</h3>
                    <h3 class="uk-h4 uk-flex uk-flex-middle uk-margin-remove" v-if="status=='done'">{{ temperature }} <img class="uk-margin-small-left" :src="icon" width="25" height="25" alt="Weather"></h3>
                </div>
            </div>
        </div>

        <div v-else class="uk-text-center">
            <v-loader />
        </div>
    </div>
</template>

<script>

import { on, append } from 'uikit-util';

export default {

    name: 'location',

    type: {

        id: 'location',
        label: 'Location',
        disableToolbar: true,
        description() {
        },
        defaults: {
            units: 'metric',
        },

    },

    replace: false,

    props: ['widget', 'editing'],

    data() {
        return {
            status: '',
            timezone: {},
            icon: '',
            temp: 0,
            time: 0,
            format: 'shortTime',
        };
    },

    mounted() {
        const vm = this; let
            list;

        const Autocompete = UIkit.autocomplete(this.$refs.autocomplete, {
            source(release) {
                vm.$http.get('admin/dashboard/weather', { params: { action: 'find', data: { q: this.input.value, type: 'like' } } }).then(
                    (res) => {
                        const { data } = res;
                        list = data.list || [];
                        release(list);
                    },
                    () => {
                        release([]);
                    },
                );
            },

            template: '<ul class="uk-nav uk-dropdown-nav uk-autocomplete-results">\
                              {{~items}}<li data-id="{{$item.id}}"><a>{{$item.name}} <span>, {{$item.sys.country}}</span></a></li>{{/items}}\
                              {{^items.length}}<li class="uk-skip"><a class="uk-text-muted">{{msgNoResults}}</a></li>{{/end}} \
                           </ul>',

            renderer(data) {
                append(this.dropdown, this.template({ items: data || [], msgNoResults: vm.$trans('No location found.') }));
                this.show();
            },
        });

        on(Autocompete.$el, 'select', (e, el, data) => {

            if (!data || !data.id) {
                return;
            }

            const location = _.find(list, { id: parseInt(data.id) });

            Vue.nextTick(() => {
                vm.$refs.location.blur();
            });

            if (!location) {
                return;
            }

            vm.$set(vm.widget, 'uid', location.id);
            vm.$set(vm.widget, 'city', location.name);
            vm.$set(vm.widget, 'country', location.sys.country);
            vm.$set(vm.widget, 'coords', location.coord);
        });

        this.timer = setInterval(this.updateClock(), 60 * 1000);
    },

    watch: {

        'widget.uid': {

            handler(uid) {
                if (uid === undefined) {
                    this.$set(this.widget, 'uid', '');
                    this.$parent.save();
                    this.$parent.edit(true);
                }

                if (!uid) return;

                this.load();
            },
            immediate: true,

        },

        timezone: 'updateClock',

    },

    computed: {

        location() {
            return this.widget.city ? `${this.widget.city}, ${this.widget.country}` : '';
        },

        temperature() {
            if (this.widget.units !== 'imperial') {
                return `${Math.round(this.temp)} °C`;
            }

            return `${Math.round(this.temp * (9 / 5) + 32)} °F`;
        },

    },

    methods: {

        load() {
            if (!this.widget.uid) {
                return;
            }

            this.$http.get('admin/dashboard/weather', { params: { action: 'weather', data: { id: this.widget.uid, units: 'metric' } }, cache: 60 }).then(
                function (res) {
                    const { data } = res;
                    if (data.cod == 200) {
                        this.init(data);
                    } else {
                        this.$set(this, 'status', 'error');
                    }
                },
                function () {
                    this.$set(this, 'status', 'error');
                },
            );

            this.$http.get('https://maps.googleapis.com/maps/api/timezone/json', { params: { location: `${this.widget.coords.lat},${this.widget.coords.lon}`, timestamp: Math.floor(Date.now() / 1000) }, cache: { key: `timezone-${this.widget.coords.lat}${this.widget.coords.lon}`, lifetime: 1440 } }).then(function (res) {
                const { data } = res;
                data.offset = data.rawOffset + data.dstOffset;

                this.$set(this, 'timezone', data);
            }, function () {
                this.$set(this, 'status', 'error');
            });
        },

        init(data) {
            this.$set(this, 'temp', data.main.temp);
            this.$set(this, 'icon', this.getIconUrl(data.weather[0].icon));
            this.$set(this, 'status', 'done');
        },

        getIconUrl(icon) {
            const icons = {

                '01d': 'sun.svg',
                '01n': 'moon.svg',
                '02d': 'cloud-sun.svg',
                '02n': 'cloud-moon.svg',
                '03d': 'cloud.svg',
                '03n': 'cloud.svg',
                '04d': 'cloud.svg',
                '04n': 'cloud.svg',
                '09d': 'drizzle-sun.svg',
                '09n': 'drizzle-moon.svg',
                '10d': 'rain-sun.svg',
                '10n': 'rain-moon.svg',
                '11d': 'lightning.svg',
                '11n': 'lightning.svg',
                '13d': 'snow.svg',
                '13n': 'snow.svg',
                '50d': 'fog.svg',
                '50n': 'fog.svg',

            };

            return this.$url('app/system/modules/dashboard/assets/images/weather-{icon}', { icon: icons[icon] });
        },

        updateClock() {
            const offset = this.timezone.offset || 0;
            const date = new Date();
            const time = offset ? new Date(date.getTime() + date.getTimezoneOffset() * 60000 + offset * 1000) : new Date();

            this.$set(this, 'time', time);

            return this.updateClock;
        },

        clear() {
            this.$refs.location.value = '';
        },

    },

    destroyed() {
        clearInterval(this.timer);
    },

};

</script>
