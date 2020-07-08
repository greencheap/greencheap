// Components
import Help from '../components/help.vue';
import Visit from '../components/visit.vue';
import Logout from '../components/logout.vue';
//import UpdateDev from '../components/update_dev.vue';
//import UpdateStable from '../components/update_stable.vue';

window.Dashpanel = {
    el: '#dashpanel',
    name:'Dashpanel',
    data(){
        return {
            sections:[]
        }
    },

    created() {
        const sections = [];
        _.forIn(this.$options.components, (component, name) => {
            if (component.section) {
                sections.push(_.extend({ name, priority: 0 }, component.section));
            }
        });
        this.$set(this, 'sections', _.sortBy(sections, 'priority'));
    },

    components:{
        Help,
        Visit,
        Logout,
        //UpdateDev,
        //UpdateStable,
    }
}

export default window.Dashpanel
Vue.ready(window.Dashpanel);