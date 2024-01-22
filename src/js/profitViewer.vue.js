import { User } from '../../src/js/user.module.js?v=2.1.9'   

const ProfitViewer = {
    name : 'profit-viewer',
    data() {
        return {
            User: new User,
            balance : {
                amount: 0,
                licences: 0,
                users: 0,
                credits: 0,
            }
        }
    },
    methods: {
        getProfitStats() {
            this.User.getProfitStats({},(response)=>{
                if(response.s == 1)
                {
                    Object.assign(this.balance,response.balance)
                }
            })
        }
    },
    mounted() 
    {   
        this.getProfitStats()
    },
    template : `
        <div class="row mb-4">
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="card overflow-hidden bg-primary">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-white font-weight-bold">Total de ganancias</p>
                                    <h2 class="font-weight-bolder text-white mb-0">
                                        $ {{ balance.amount.numberFormat(2) }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="bi bi-wallet text-lg text-primary" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a class="text-decoration-underline fw-sembold text-white" href=""> Ver detalle de ganancias aquí</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="card overflow-hidden ">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 font-weight-bold">
                                        Créditos disponibles
                                    </p>
                                    <h2 class="font-weight-bolder mb-0">
                                        {{balance.credits.numberFormat(0)}}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-primary text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg text-white opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a class="text-decoration-underline fw-sembold text-primary" href="../../apps/store/credit"> Comprar créditos</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="card overflow-hidden ">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 font-weight-bold">Invitados</p>
                                    <h2 class="font-weight-bolder mb-0">
                                        {{balance.users.numberFormat(0)}}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-primary text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg text-white opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a class="text-decoration-underline fw-sembold text-primary" href="../../apps/referrals/"> Mis referidos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ProfitViewer } 