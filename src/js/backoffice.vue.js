/* vue */ 
import { NoticeViewer } from '../../src/js/noticeViewer.vue.js?v=2.1.9'

import { NotificationService } from '../../src/js/notification.module.js?v=2.1.9'   
import { AccountactivationViewer } from '../../src/js/accountactivationViewer.vue.js?v=2.1.9'
import { ProfitViewer } from '../../src/js/profitViewer.vue.js?v=2.1.9'
import { LandingViewer } from '../../src/js/landingViewer.vue.js?v=2.1.9'
import { ZuumsignupViewer } from '../../src/js/zuumsignupViewer.vue.js?v=2.1.9'

Vue.createApp({
    components : { 
        ProfitViewer, LandingViewer, ZuumsignupViewer, NoticeViewer, AccountactivationViewer
    },
    data() {
        return {
            NotificationService : new NotificationService
        }
    },
}).mount('#app')