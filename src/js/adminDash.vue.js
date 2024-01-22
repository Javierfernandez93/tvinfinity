import { UserSupport } from '../../src/js/userSupport.module.js?t=6'

/* vue */

Vue.createApp({
    components: {},
    data() {
        return {
            UserSupport: new UserSupport,
            
        };
    },
    watch: {
        
    },
    methods: {
    },
    mounted() {
        
        // this.getStats().then((response) => {
        //     this.initChart(response);
        //     this.initChartPie(response);
        // });
    },
}).mount("#app");
