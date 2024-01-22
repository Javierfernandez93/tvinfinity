import { UserSupport } from '../../src/js/userSupport.module.js?t=6'

const AdminwalletViewer = {
    name : 'adminwallet-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            isTransactiontFilled: false,
            lastTransactionsAux: null,
            lastTransactions: null,
            transaction: {
                address: '',
                amount: null
            },
            TRANSACTION_TYPES: {
                OUTPUT : 'output',
                INPUT : 'input',
            },
        }
    },
    watch : {
        transaction : {
            handler() {
                this.isTransactiontFilled = this.transaction.address.isValidEwalletAddress() != null && this.transaction.amount != null 
            },
            deep: true
        }
    },
    methods: {
        getEwalletInfo() {
            this.UserSupport.getEwalletInfo(this.transaction, (response) => {
                if (response.s == 1) {
                    this.lastTransactions = response.lastTransactions
                    this.lastTransactionsAux = response.lastTransactions
                } else if(response.r == 'INVALID_PERMISSION') {
                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                }
            })
        },
        sendTransactionByAdmin() {
            const alert = alertCtrl.create({
                title: `¿Deseas procesar ésta transacción?`,
                subTitle: `Procesar a ${this.transaction.address} por $ ${this.transaction.amount.numberFormat(2)}`,
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            alert.modal.dismiss();

                            this.UserSupport.sendTransactionByAdmin(this.transaction, (response) => {
                                if (response.s == 1) {
                                    alertHtml('El envío fué procesado con éxito')
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
    },
    mounted() {
        if(getParam('publicKey'))
        {
            this.transaction.address = getParam('publicKey')

            this.getEwalletInfo()
        }
    },
    template : `
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-xl">
                        <div class="form-floating">
                            <input
                                v-model="transaction.address"
                                :class="transaction.address ? 'is-valid' : ''"
                                ref="address"
                                :autofocus="true"
                                @keydown.enter.exact.prevent="$refs.amount.focus()" 
                                type="text" class="form-control pe-5" id="floatingInput" placeholder="address">
                            <label for="floatingInput">Dirección</label>
                        </div>
                    </div>
                    <div class="col-12 col-xl">
                        <div class="form-floating">
                            <input 
                                v-model="transaction.amount"
                                :class="transaction.amount ? 'is-valid' : ''"
                                ref="amount"
                                type="text" class="form-control" id="floatingInput" placeholder="amount">
                            <label for="floatingInput">Monto</label>
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <div class="d-flex">
                            <button
                                :disabled="!transaction.address.isValidEwalletAddress()"
                                @click="getEwalletInfo"
                                class="btn btn-primary w-100 mb-0 me-2 h-100 shadow-none btn-lg">Info</button>
                            <button
                                :disabled="!isTransactiontFilled"
                                @click="sendTransactionByAdmin"
                                class="btn btn-primary w-100 mb-0 h-100 shadow-none btn-lg">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="lastTransactions" class="card mt-3 rounded-0">
            <ul class="list-group list-group-flush rounded-0">
                <li v-for="lastTransaction in lastTransactions" class="list-group-item list-group-item-action border-start border-5 border-bottom-0 border-top-0 border-end-0  f-zoom-element-sm p-5"
                    :class="lastTransaction.transactionData.type == TRANSACTION_TYPES.INPUT ? 'border-success' : 'border-danger'">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div>
                                <span class="badge p-0 text-secondary">Fecha</span>
                            </div>

                            <div>
                                <u>
                                    {{lastTransaction.unix_date.formatFullDate()}}
                                </u>
                            </div>

                            <div>
                                <span class="badge p-0 text-secondary">TXIDN</span>
                            </div>

                            <div>
                                <u>
                                    {{lastTransaction.transaction_id}}
                                </u>
                            </div>
                            
                            <div>
                                <span class="badge p-0 text-secondary">Hash</span>
                            </div>
                            <div 
                                @click="goToViewTransaction(lastTransaction.hash)" class="cursor-pointer text-truncate">
                                <u>
                                    {{lastTransaction.hash}}
                                </u>
                            </div>
                            
                            <span v-if="lastTransaction.data">{{lastTransaction.data}}</span>
                        </div>
                        <div class="col-4 text-start text-truncate">
                            <div>
                                <span class="badge p-0 text-secondary">ID</span>
                            </div>

                            <div>
                                {{lastTransaction.transactionData.user_login_id}}
                            </div>

                            <div>
                                <span class="badge p-0 text-secondary">Nombre</span>
                            </div>

                            <div class="text-capitalize">
                                {{lastTransaction.transactionData.names}}
                            </div>
                        </div>
                        <div class="col-2 text-end text-truncate">
                            <div>
                                <span v-if="lastTransaction.transactionData.type == TRANSACTION_TYPES.INPUT" class="badge border border-success text-success text-xxs">
                                    Recibido
                                </span>
                                <span v-else-if="lastTransaction.transactionData.type == TRANSACTION_TYPES.OUTPUT" class="badge border border-danger text-danger text-xxs">
                                    Enviado
                                </span>
                            </div>
                            <span class="fw-semibold text-dark text-xs">
                                {{lastTransaction.transactionData.address.amount.numberFormat(6)}} USD
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `,
}

export { AdminwalletViewer } 