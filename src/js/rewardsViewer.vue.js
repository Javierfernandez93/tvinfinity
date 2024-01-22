import { User } from '../../src/js/user.module.js?v=2.1.9'   

const RewardsViewer = {
    name : 'rewards-viewer',
    data() {
        return {
            User: new User,
            rewards: null
        }
    },
    methods: {
        claimReward(reward) {
            this.User.claimReward({catalog_reward_id:reward.catalog_reward_id},(response)=>{
                if(response.s == 1)
                {
                    reward.claimed = true

                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `Hemos reclamado tu recomensa para ${reward.title} ${reward.description}, pronto te contactaremos por WhatsApp`,
                        _class:'bg-gradient-success text-white'
                    })
                }
            })
        },
        getRewards() {
            return new Promise((resolve, reject) => {
                this.User.getRewards({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.rewards)
                    }

                    reject()
                })
            })
        }
    },
    mounted() 
    {     
        this.getRewards().then((rewards) => this.rewards = rewards)
    },
    template : `
        <div v-if="rewards" class="container">
            <div class="row">
                <div v-for="reward in rewards" class="col-12 col-xl">
                    <div class="card card-reward">
                        <div class="overflow-hidden card-reward-cover position-relative border-radius-lg bg-cover h-100" 
                            :style="{'background-image': 'url('+reward.image+')'}">
                            <span class="mask"
                                :class="reward.css"></span>


                            <div class="progress position-relative z-index-1 p-2" style="height:0.5rem;">
                                <div class="progress-bar bg-primary" 
                                :style="{'width': +reward.progress+'%'}"  
                                role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="card-body position-relative justify-content-center z-index-1 h-100 p-3 text-center d-flex align-items-center">
                                <div class="">
                                    <h2 class="text-white">
                                        {{reward.title}}
                                    </h2>
                                    <div class="text-white fw-sembold">
                                        {{reward.description}}
                                    </div>
                                    <div class="text-white mb-1">
                                        (necesitas {{reward.goal}} cuentas IPTV activas)
                                    </div>
                                    <div v-if="!reward.claimed">
                                        <button v-if="reward.progress == 100" @click="claimReward(reward)" class="btn btn-light mb-0 shadow-none">Reclamar Premio</button>
                                    </div>
                                    <div v-else class="text-white fw-bold">
                                        Ya reclamaste este premio
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { RewardsViewer } 