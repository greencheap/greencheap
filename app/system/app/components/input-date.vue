<template>

    <div class="uk-grid-small uk-child-width-1-2@l" uk-grid>
        <div>
            <div ref="datepicker" class="uk-inline">
                <span class="uk-form-icon" uk-icon="calendar" />
                <input class="uk-input" type="text" v-model.lazy="date">
            </div>
        </div>
        <div>
            <div ref="timepicker" class="uk-inline">
                <span class="uk-form-icon" uk-icon="clock" />
                <input class="uk-input" type="text" v-model.lazy="time">
            </div>
        </div>
    </div>
    <!-- TODO Component Validation -->
</template>

<script>

import { $ } from 'uikit-util';

export default {

    props: ['value', 'required'],

    data() {
        return {
            datetime: this.value,
            options: {
                datepicker: {
                    allowInput: true,
                    locale: {
                        amPM: window.$locale.DATETIME_FORMATS.AMPMS,
                        firstDayOfWeek: 1,
                        months: {
                            longhand: window.$locale.DATETIME_FORMATS.STANDALONEMONTH,
                            shorthand: window.$locale.DATETIME_FORMATS.SHORTMONTH,
                        },
                        weekdays: {
                            longhand: window.$locale.DATETIME_FORMATS.DAY,
                            shorthand: window.$locale.DATETIME_FORMATS.SHORTDAY,
                        },
                    },
                },
                timepicker: {
                    allowInput: true,
                    enableTime: true,
                    minuteIncrement: 1,
                    noCalendar: true,
                },
            },
        };
    },

    created() {},

    mounted() {
        this.$nextTick(() => {
            flatpickr($('input', this.$refs.datepicker), _.extend(this.options.datepicker, {
                altInput: true,
                altFormat: this.dateFormat,
                dateFormat: 'Y-m-d'
            }));
            flatpickr($('input', this.$refs.timepicker), _.extend(this.options.timepicker, {
                time_24hr: (this.clockFormat == '24h'),
                dateFormat: this.timeFormat,
            }));
        });
    },

    computed: {

        getlocale: function locale() {
            return {
                months: window.$locale.DATETIME_FORMATS.STANDALONEMONTH,
                weekdays: window.$locale.DATETIME_FORMATS.SHORTDAY,
            };
        },

        dateFormat() {
            return window.$locale.DATETIME_FORMATS.shortDate
                .replace(/\bdd\b/i, 'D').toLowerCase()
                .replace(/\bmm\b/i, 'M').toLowerCase()
                .replace(/\byy\b/i, 'Y')
                .toLowerCase()
                .split('')
                .join('')
                .replace(/[^\x00-\xFF]/g, "");
        },

        timeFormat() {
            return window.$locale.DATETIME_FORMATS.shortTime
                .replace(/\bmm\b/i, 'i')
                .replace(/\bhh\b/i, 'H')
                .replace(/\bh\b/i, (this.clockFormat == '24h') ? 'H' : 'h')
                .replace(/\ba\b/i, 'K')
                .replace(/[^\x00-\xFF]/g, "");
        },

        clockFormat() {
            return window.$locale.DATETIME_FORMATS.shortTime.match(/a/) ? '12h' : '24h';
        },

        date: {

            get() {
                return this.datetime;
            },

            set(date) {
                if (!date) return;
                const prev = new Date(this.datetime);
                date = new Date(date);
                date.setHours(prev.getHours(), prev.getMinutes(), 0);
                this.$set(this, 'datetime', date.toISOString());
                this.$emit('input', this.datetime);
            },

        },

        time: {

            get() {
                return flatpickr.formatDate(new Date(this.datetime), this.timeFormat);
            },

            set(time) {
                if (!time) return;
                const fulltime = time;
                const date = new Date(this.datetime);
                let hour, min;
                time = time.replace(/AM|PM/, '').trim().split(':');
                hour = parseInt(time[0]) + ((fulltime.indexOf('PM') !== -1) ? 12 : 0);
                min = parseInt(time[1]);
                date.setHours(hour, min);
                this.$set(this, 'datetime', date.toISOString());
                this.$emit('input', this.datetime);
            },

        },

        isRequired() {
            return typeof this.required !== 'undefined';
        },

    }
};

Vue.component('input-date', (resolve, reject) => {
    Vue.asset({
        css: [
            'app/assets/flatpickr/dist/flatpickr.min.css'
        ],
        js: [
            'app/assets/flatpickr/dist/flatpickr.min.js',
        ],
    }).then(() => {
        resolve(require('./input-date.vue'));
    });
});

</script>
