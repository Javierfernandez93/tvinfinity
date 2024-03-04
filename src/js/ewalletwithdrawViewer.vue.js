import { User } from '../../src/js/user.module.js?v=2.6.4'   

const EwalletwithdrawViewer = {
    name : 'ewalletwithdraw-viewer',
    emits: ['getewallet'],
    props: ['ewallet'],
    data() {
        return {
            User: new User,
            bank : null,
            withdraw: {
                fee: null,
                amount: null,
                withdraw_method_per_user_id: null
            },
            FEE_WITHDRAW_TRANSACTION: 0,
            error: null,
            MIN_AMOUNT_TO_WITHDRAW: 50,
            ERRORS: {
                NOT_WITHDRAWS_METHODS : {
                    code: 1,
                    text: 'No tienes métodos de retiro, por favor configura uno'
                },
                NOT_ENOUGH_FUNDS : {
                    code: 2,
                    text: 'No tienes fondos suficientes'
                },
                NOT_WITHDRAWS_METHOD_CHOICED : {
                    code: 3,
                    text: `Configura tu método de retiro <a href="../../apps/ewallet/withdrawMethods">aquí</a>`
                },
                NOT_MINIMUM_AMOUNT : {
                    code: 4,
                    text: `El monto mínimo de retiro es de`
                },
            }
        }
    },
    watch : {
        withdraw: {
            handler() {
                this.error = null

                this.withdraw.fee = (this.withdraw.amount * this.FEE_WITHDRAW_TRANSACTION) / 100

                const tempAmount = parseFloat(this.withdraw.amount) + parseFloat(this.withdraw.fee)

                if(this.ewallet.amount >= tempAmount)
                {
                    if(this.withdraw.amount >= this.MIN_AMOUNT_TO_WITHDRAW)
                    {
                        if(this.bank.bankConfigurated == true)
                        {
                            if(this.withdrawMethods != false)
                            {

                            } else {
                                this.error = this.ERRORS.NOT_WITHDRAWS_METHODS
                            }
                        } else {
                            this.error = this.ERRORS.NOT_WITHDRAWS_METHOD_CHOICED
                        }
                    } else {
                        this.error = this.ERRORS.NOT_MINIMUM_AMOUNT
                    }
                } else {
                    this.error = this.ERRORS.NOT_ENOUGH_FUNDS
                }
            },
            deep: true
        }
    },
    methods: {
        goToTransaction(hash) {            
            window.location.href = `../../apps/blockchain/transaction?txn=${hash}`
        },
        goToConfigureWithdrawMethods(hash) {            
            window.location.href = `../../apps/ewallet/withdrawmethods`
        },
        sendEwalletFunds() {            
            this.User.sendEwalletFunds(this.withdraw,(response)=>{
                if(response.s == 1)
                {
                    this.$emit('getewallet')

                    $(this.$refs.offcanvasRight).offcanvas('hide')
                }
            })
        },
        openWithdraw() {     
            this.openOffCanvas()
        },
        openOffCanvas() {     
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
        filterWithdrawMethods(withdrawMethods) {
            return new Promise((resolve, reject) => { 
                withdrawMethods = withdrawMethods.filter((withdrawMethod)=>{
                    return withdrawMethod['wallet'] != false
                })

                if(withdrawMethods.length > 0)
                {
                    resolve(withdrawMethods)
                } else {
                    reject()   
                }
            })
        },
        getWithdrawsMethods() {
            return new Promise((resolve, reject) => {
                this.User.getWithdrawsMethods({  }, (response) => {
                    if (response.s == 1) {
                        resolve(response)
                    }

                    reject()
                })
            })
        },
        getTransactionWithdrawFee() {            
            this.User.getTransactionWithdrawFee({},(response)=>{
                
                if(response.s == 1)
                {
                    this.FEE_WITHDRAW_TRANSACTION = response.fee_withdraw
                }
            })
        },
        withdrawFunds() {
            this.User.withdrawFunds(this.withdraw, (response) => {
                if (response.s == 1) {
                    this.$emit('getewallet')

                    $(this.$refs.offcanvasRight).offcanvas('hide')
                } else if(response.r == "NOT_ACTIVE") {
                    alertMessage('Debes de estar activo para poder retirar dinero')
                }
            })
        },
    },
    mounted() 
    {       
        this.getTransactionWithdrawFee()

        this.getWithdrawsMethods().then((response)=>{
            this.withdraw.fee = response.fee

            this.bank = response.bank
        }).catch(() => { 
            
        })
    },
    template : `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasWithBackdropLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">
                        <div>
                            <t>Retirar MXN</t>
                        </div>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div v-if="ewallet" class="offcanvas-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-gradient-warning fs-5 shadow-lg">
                                        <i class="bi bi-currency-exchange text-white"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div>
                                        <span class="badge text-secondary p-0">Balance</span>
                                    </div>
                                    <div class="fs-5 fw-semibold text-dark">
                                        $ {{ewallet.amount.numberFormat(2)}} MXN
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input 
                                    v-model="withdraw.amount"
                                    :class="error != ERRORS.NOT_ENOUGH_FUNDS && withdraw.amount > 0 ? 'is-valid' : ''"
                                    type="number" class="form-control" id="address" placeholder="Cantidad a retirar">
                                <label for="address">Cantidad a retirar</label>
                            </div>

                            <div v-if="bank.bankConfigurated" class="form-floating mb-3">
                                <div class="mb-3">
                                    <div class="text-xs text-secondary">CLABE</div>
                                    {{bank.clabe}}
                                </div>
                                <div class="mb-3">
                                    <div class="text-xs text-secondary">Cuenta</div>
                                    {{bank.account}}
                                </div>
                                <div class="mb-3">
                                    <div class="text-xs text-secondary">Banco</div>
                                    {{bank.bank}}
                                </div>
                            </div>

                            <div class="form-floating mb-3 text-center">
                                <span class="badge text-secondary p-0">Cantidad + (fee $ {{FEE_WITHDRAW_TRANSACTION.numberFormat(2)}}%) </span>
                                <div class="fw-semibold text-dark">
                                    $ {{(parseFloat(withdraw.amount)+parseFloat(withdraw.fee)).numberFormat(2)}} MXN
                                </div>
                            </div>

                            <div v-if="error" class="alert alert-danger text-white">
                                <strong>Aviso</strong> - <span v-html="error.text"></span>

                                <span v-if="error == ERRORS.NOT_MINIMUM_AMOUNT">$ {{MIN_AMOUNT_TO_WITHDRAW.numberFormat(2)}}</span>

                                <div v-if="error == ERRORS.NOT_WITHDRAWS_METHODS" class="mt-3">
                                    <button class="btn btn-light mb-0" @click="goToConfigureWithdrawMethods">Ir a configurar método</button>
                                </div>
                            </div>
                            <div v-else class="alert alert-success text-white">
                                <div v-if="withdraw.fee > 0">
                                    <strong>Aviso</strong> Fee de la transacción $ {{withdraw.fee.numberFormat(2)}} MXN
                                </div>
                                <div v-else>
                                    <strong>Aviso</strong> Los retiros duran 72 horas hábiles en ser procesados
                                </div>
                            </div>

                            <button 
                                :disabled="error != null"
                                @click="withdrawFunds"
                                class="btn btn-primary shadow-none waves-effect waves-light">
                                Retirar fondos
                            </button>
                        </div>    
                    </div>    
                </div>
            </div>
        </div>
    `,
}

export { EwalletwithdrawViewer } 