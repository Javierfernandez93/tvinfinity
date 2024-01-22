/* vue */
import { AddbannerViewer } from '../../src/js/addbannerViewer.vue.js?v=2.1.9'

Vue.createApp({
    components: {
        AddbannerViewer
    },
    data() {
        return {
            
        }
    },
    watch: {
    },
    methods: {
        goToAddCampaign: function()
        {
            window.location.href = '../../apps/banner/addCampaign'
        }
    },
    mounted() {
    },
}).mount('#app')