import { User } from '../../src/js/user.module.js?v=2.6.5'   

const WithdrawmethodsViewer = {
    name : 'withdrawmethods-viewer',
    data() {
        return {
            User: new User,
            bank : null
        }
    },
    methods: {
        toggleEditing(bank) {
            bank.editing = !bank.editing
        },
        getBankData() {
            return new Promise((resolve, reject) => {
                this.User.getBankData({  }, (response) => {
                    if (response.s == 1) {
                        resolve(response.bank)
                    }

                    reject()
                })
            })
        },
        editBank(bank) {
            this.User.editBank(bank, (response) => {
                if (response.s == 1) {
                    this.toggleEditing(bank)


                }
            })
        },
    },
    mounted() 
    {       
        this.getBankData().then((bank)=>{
            this.bank = bank
        }).catch((error) => { this.bank = false })
    },
    template : `
        <div class="card mt-4 overflow-hidden border-radius-xl">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="badge fs-6 bg-secondary"><i class="bi bi-credit-card"></i></span>
                    </div>
                    <div class="col">
                        <h6 class="mb-0">Método de retiro</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-group list-group">
                    <li class="list-group-item border-0">
                        <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                            <div class="w-100">
                                <div v-if="!bank.editing"
                                    @click="toggleEditing(bank)">
                                    <div v-if="bank.clabe"
                                        class="text-truncate">
                                        <div class="row">
                                            <div class="col-12 col-xl">
                                                <div class="text-xs">Banco</div>
                                                <h6 class="mb-0">{{bank.bank}}</h6>
                                            </div>
                                            <div class="col-12 col-xl">
                                                <div class="text-xs">CLABE</div>
                                                <h6 class="mb-0">{{bank.clabe}}</h6>
                                            </div>
                                            <div class="col-12 col-xl">
                                                <div class="text-xs">No. Cuenta</div>
                                                <h6 class="mb-0">{{bank.account}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="cursor-pointer">
                                        <u>Configurar cuenta</u>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row align-items-center mt-3">
                                        <div class="col-12 col-xl">
                                            <div class="form-floating mb-3">
                                                <input 
                                                    v-model="bank.bank"
                                                    placeholder="Banco"
                                                    type="text" class="form-control"/>

                                                <label>Banco</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input 
                                                    v-model="bank.clabe"
                                                    placeholder="Clabe"
                                                    type="text" class="form-control"/>

                                                <label>Clabe</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input 
                                                    v-model="bank.account"
                                                    placeholder="Cuenta"
                                                    type="text" class="form-control"/>

                                                <label>Número de cuenta</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-auto">
                                            <button 
                                                :disabled="!bank.account && !bank.clabe && !bank.bank"
                                                @click="editBank(bank)"
                                                class="btn btn-success shadow-none mb-0">Actualizar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span 
                                v-if="!bank.editing"
                                @click="toggleEditing(bank)"
                                class="ms-auto">
                                <i class="fas fa-pencil-alt text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true" aria-label="Edit Card"></i>
                                <span class="sr-only">Editar tarjeta</span>
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    `,
}

export { WithdrawmethodsViewer } 