import { AdviceViewer } from '../../src/js/adviceViewer.vue.js?v=2.1.9'

Vue.createApp({
    components : { 
        AdviceViewer
    },
    data() {
        return {
        }
    },
    methods : {
        viewVideo() {
            let alert = alertCtrl.create({
                closeButton: false,
                html: `
                    <div class="tutorial container text-center my-5 ratio ratio-16x9"><iframe width="560" height="315" src="https://www.youtube.com/embed/fXb803vDUjc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
                `,
                size: 'modal-md'
            })
            
            alertCtrl.present(alert.modal); 
        }
    },
    mounted() 
    {
        
    },
}).mount('#app')