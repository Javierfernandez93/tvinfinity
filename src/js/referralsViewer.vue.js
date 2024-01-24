import { User } from '../../src/js/user.module.js?v=2.1.9'   

const ReferralsViewer = {
    name : 'referrals-viewer',
    data() {
        return {
            User : new User,
            networkAux: null,
            network: null,
        }
    },
    methods: {
        goToEvent(banner) {
            window.open(banner.link)
        },
        search(target,levelIndex) {
            let query = $(target).val()

            this.network = {...this.networkAux}

            this.network[levelIndex] = this.network[levelIndex].filter((user)=>{
                return user.names.toLowerCase().includes(query.toLowerCase())
            })
        },
        getNetwork() {
            this.User.getNetwork({},(response)=>{
                if(response.s == 1)
                {
                    this.network = response.network
                    this.networkAux = response.network
                } else {
                    this.network = false
                }
            })
        }
    },
    mounted() {
        this.getNetwork()
    },
    template : `
        <div class="container">
            <div class="alert sans bg-primary text-center">
                <div class="mb-n1 lead text-white">
                    ¡Echa un vistazo a tus referidos a dos niveles! 
                </div>
                <div class="h4 text-white">
                    ¡Descubre la red que estás construyendo en Infinity!
                </div>
            </div>
            <div v-if="network">
                <div v-for="(level,levelIndex) in network" class="card mb-3 animation-fall-down" :style="{'--delay':(levelIndex+1)*250+'ms'}">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl h4">
                                Nivel
                                {{levelIndex+1}}
                            </div>    
                            <div class="col-12 col-xl-auto">
                                <input type="text" @keyup="search($event.target,levelIndex)" class="form-control" placeholder="buscar..."/>
                            </div>    
                        </div>    
                    </div>    

                    <div class="table-responsive-sm">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th class="tex-xs text-uppercase text-secondary">ID</th>
                                    <th class="tex-xs text-uppercase text-secondary">Nombre</th>
                                    <th class="tex-xs text-uppercase text-secondary">Fecha de registro</th>
                                    <th class="tex-xs text-uppercase text-secondary">Activo hace</th>
                                    <th class="tex-xs text-uppercase text-secondary">Patrocinador</th>
                                    <th class="tex-xs text-uppercase text-secondary">Activación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in level" class="text-center">
                                    <td class="align-middle">{{user.company_id}}</td>
                                    <td class="align-middle sans">{{user.names}}</td>
                                    <td class="align-middle">
                                        <span v-if="user.signup_date">
                                            {{user.signup_date.formatFullDate()}}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span v-if="user.last_login_date" class="badge bg-success">
                                            {{user.last_login_date.timeSince()}}
                                        </span>
                                        <span v-else class="badge bg-secondary">
                                            Nunca
                                        </span>
                                    </td>
                                    <td class="align-middle sans">
                                        {{user.sponsor.names}}
                                    </td>
                                    <td class="align-middle">
                                        <span v-if="!user.activation" class="badge bg-secondary">
                                            <i class="bi h5 text-white bi-x"></i>
                                        </span>
                                        <span v-else class="badge bg-success">
                                            <i class="bi h5 text-white bi-check"></i>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div v-else-if="network == false">
                <div class="alert alert-info text-center text-white">
                    <div>Aún no tienes tu unilevel activo</div>
                    <div class="h4 text-white">Comienza invitando personas a Infinity</div>

                    <a href="../../apps/backoffice" class="btn mt-3 btn-outline-light">Ir a mi dashboard</a>
                </div>
            </div>
        </div>
    `,
}

export { ReferralsViewer } 