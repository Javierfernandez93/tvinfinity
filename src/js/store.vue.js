import { User } from '../../src/js/user.module.js?v=2.1.9'   

/* vue */
import { StoreitemsViewer } from '../../src/js/storeitemsViewer.vue.js?v=2.1.9'
import { StorepaymentmethodsViewer } from '../../src/js/storepaymentmethodsViewer.vue.js?v=2.1.9'
import { StorecheckoutViewer } from '../../src/js/storecheckoutViewer.vue.js?v=2.1.9'
import { StoreinvoiceViewer } from '../../src/js/storeinvoiceViewer.vue.js?v=2.1.9'

Vue.createApp({
    components: {
        StoreitemsViewer, StorepaymentmethodsViewer, StorecheckoutViewer, StoreinvoiceViewer
    },
    data() {
        return {
            User : new User,  
            STATES: {
                CHOICE_ITEMS: {
                    code: 1,
                    text: "Elegir paquete",
                },
                CHOICE_PAYMENT_METHOD: {
                    code: 2,
                    text: "Elegir método de pago",
                },
                CHECKOUT: {
                    code: 3,
                    text: "Resumen de compra",
                },
                INVOICE: {
                    code: 4,
                    text: "Ticket de pago",
                },
                NOT_ACTIVE: {
                    code: 5,
                    text: "Franquicia inactiva",
                }
            },      
            cart: {
                hasItems : false,
                id: null,
                catalog_currency_id: null,
                catalog_payment_method_id: null,
                package_id: null,
                state: null,
                package_type: null,
                buy_per_user : {
                    buy_per_user_id: null
                }
            }
        }
    },
    methods: {
        nextStep() {
            if(this.cart.state.code < Object.keys(this.STATES).length)
            {
                this.proccessBeforeContinue(this.cart.state).then(()=>{
                    this.cart.state = this.getStateByCode(this.cart.state.code+1)
                })
            }
        },
        proccessBeforeContinue(state) {
            return new Promise((resolve) => {
                if(state == this.STATES.CHECKOUT)
                {
                    this.saveBuy().then(()=>{
                        resolve()
                    })
                } else {
                    resolve()
                }
            })
        },
        getStateByCode(code) {
            const states = Object.keys(this.STATES);
            let state = null
            
            for(let i=0; i< states.length; i++)
            {
                if(this.STATES[states[i]].code == code)
                {
                    state = this.STATES[states[i]]
                }
            }

            return state
        },
        saveBuy() {
            return new Promise((resolve) => {
                this.User.saveBuy(this.cart, (response) => {
                    if (response.s == 1) {
                        this.cart.buy_per_user.buy_per_user_id = response.buy_per_user_id
                    }

                    resolve()
                })
            })
        },
        initCart() {
            return new Promise((resolve,reject) => {
                this.User.initCart({}, (response) => {
                    if (response.s == 1) {
                        this.cart.id = response.instance_id

                        resolve()
                    }

                    reject()
                })
            })
        },
        isActive() {
            return new Promise((resolve,reject) => {
                if(['package','credit'].includes(this.cart.package_type)) {
                    this.User.isActive({}, (response) => {
                        if (response.s == 1) {
                            resolve(response.active)
                        }
                        
                        reject()
                    })
                } else {
                    resolve(true)
                }
            })
        },
    },
    mounted() {
        const package_type = getLastUrlPart()
        
        if(['package','credit','game'].includes(package_type))
        {
            this.cart.package_type = package_type
            this.cart.state = this.STATES.NOT_ACTIVE
            // this.cart.state = this.STATES.INVOICE

            this.isActive().then((active) =>{
                this.active = active

                if(active)
                {
                    this.cart.state = this.STATES.CHOICE_ITEMS

                    this.initCart().then(() =>{
                        
                    }).catch(() => {
                        alert("Hubo un error")
                    })
                } else if(['package','credit'].includes(this.cart.package_type)) {
                    alertInfo({
                        icon:'<i class="bi bi-x fs-3"></i>',
                        message: `<div class="h3 text-white">Aún no te has activo en tu Franquicia.</div> <div>Primero debes de activarte para poder comprar más licencias</div>`,
                        _class:'bg-gradient-danger text-white'
                    })
                }
            })

        }
    },
}).mount('#app')