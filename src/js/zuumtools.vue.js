import { User } from '../../src/js/user.module.js?v=2.1.9'   

/* vue */ 
import { ZuumtoolsViewer } from '../../src/js/zuumtoolsViewer.vue.js?v=2.1.9'

Vue.createApp({
    components : { 
        ZuumtoolsViewer
    },
    data() {
        return {
            User : new User,
            profits : {}
        }
    },
    watch : {
        user : {
            handler() {
                
            },
            deep: true
        },
    },
    methods: {
    },
    mounted() 
    {
        // this.getProfits()
    },
}).mount('#app')