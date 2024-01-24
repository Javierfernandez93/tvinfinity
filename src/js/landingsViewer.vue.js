import { Guest } from '../../src/js/guest.module.js?v=2.1.9'   

const LandingsViewer = {
    name : 'landings-viewer',
    data() {
        return {
            Guest : new Guest,
            userData : null,
            landing : null,
            ACTIONS: {
                WHATSAPP : 1,
                LINK : 2,
                SIGNUP : 3
            }
        }
    },
    methods: {
        getLandingByData(path,landing) {
            this.Guest.getLandingByData({path:path,landing:landing},(response)=>{
                if(response.s == 1)
                {
                    this.landing = response.landing
                    this.userData = response.userData
                }
            })
        },
        doAction(landing)
        {
            if(landing.catalog_landing_action_id == this.ACTIONS.WHATSAPP)
            {
                window.open(this.userData.whatsApp.getWhatsAppFromText(`¡Hola *${this.userData.names.trim()}* me interesa tu video *${landing.title}* de *Infinity*. ¿Puedes darme más información?`))
            } else if(landing.catalog_landing_action_id == this.ACTIONS.LINK) {
                window.open(landing.action)
            } else if(landing.catalog_landing_action_id == this.ACTIONS.SIGNUP) {
                window.open(this.userData.landing.getLandingPath())
            }
        },
        isYoutubeVideo(url)
        {
            const preg = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            
            return url.match(preg) ? true : false
        },
        isVimeoVideo(url)
        {
            const preg = /^.+vimeo.com\/(.*\/)?([^#\?]*)/

            return url.match(preg) ? true : false
        },
        copyToClipBoard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.$refs.landing.innerText = 'Copiada'
            });
        },
        sendByWhatsapp(landing) {
            window.open(`*¡Hola!* quiero invitarte a un *proyecto increíble* que te permite *ganar dinero* por el *entretenimiento* ¡regístrate ya! ${landing}`.getWhatsappLink())
        },
    },
    mounted() 
    {   
        if(getParam("path"))
        {
            if(getParam("landing"))
            {
                this.getLandingByData(getParam("path"),getParam("landing"))
            }
        }
    },
    template : `
        <div class="row justify-content-center">
            <div class="col-11 col-xl-6">
                <div v-if="landing" class="card">
                    <div class="card-body">
                        <div class="fs-4 mb-3 text-center fw-bold">{{landing.title}}</div>
                        
                        <div v-if="landing.content">
                            <span v-html="landing.content"></span>
                        </div>

                        <div v-if="isYoutubeVideo(landing.video)">
                            <span v-html="landing.video.getYoutubeVideoFrame()"></span>
                        </div>
                        <div v-else-if="isVimeoVideo(landing.video)">
                            <span v-html="landing.video.getVimeoFrame()"></span>
                        </div>

                        <div class="d-grid">
                            <button v-if="landing.catalog_landing_action_id == ACTIONS.WHATSAPP" @click="doAction(landing)" class="btn btn-success fs-4 mb-0 shadow-none btn-lg">
                                {{landing.text}}
                            </button>
                            <button v-else-if="landing.catalog_landing_action_id == ACTIONS.LINK" @click="doAction(landing)" class="btn btn-primary fs-4 mb-0 shadow-none btn-lg">
                                {{landing.text}}
                            </button>
                            <button v-else-if="landing.catalog_landing_action_id == ACTIONS.SIGNUP" @click="doAction(landing)" class="btn btn-danger fs-4 mb-0 shadow-none btn-lg">
                                {{landing.text}}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-center text-white">Infinity ® All rights reserved 2023</div>
            </div> 
        </div>
    `,
}

export { LandingsViewer } 