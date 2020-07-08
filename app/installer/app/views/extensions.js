import PackageManager from '../components/package-manager';

window.Extensions = _.merge(PackageManager, { name: 'extensions', el: '#extensions' });

Vue.ready(window.Extensions);
