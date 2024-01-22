import { User } from '../../src/js/user.module.js?v=2.1.9'   

const StorecheckoutViewer = {
    name : 'storecheckout-viewer',
    props : ['cart'],
    emits : ['nextstep'],
    data() {
        return {
            User: new User,
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        getCartResume: function () {
            return new Promise((resolve,reject) => {
                this.User.getCartResume({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.resume)
                    }

                    reject()
                })
            })
        },
        deleteItem: function(id)
        {
            this.User.deleteItem({id:id}, (response) => {
                if (response.s == 1) {
                    this.getCartResume()
                }
            })
        },
        nextStep: function()
        {
            this.$emit('nextstep')
        }
    },
    mounted() {
        this.getCartResume().then(resume => this.cart.resume = resume).catch( (error) => { })
    },
    template : `
        <div v-if="cart.resume" class="row justify-content-center">
            <div class="col-12 col-xl-5">
                <div v-if="cart.resume.items" class="card overflow-hidden border-radius-2xl">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item py-5 border-light">
                                <div class="row align-items-center">
                                    <div class="col text-secondary">
                                        Método de pago
                                    </div>
                                    <div class="col-auto fw-sembold text-primary text-gradient">
                                        {{cart.resume.payment_method}}
                                    </div>
                                </div>
                            </li>
                            <li v-for="item in cart.resume.items" class="list-group-item border-light py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar avatar-xl position-relative">
                                            <img src="https://cdn.dribbble.com/users/2671670/screenshots/17205827/media/7dbc01c09ff4c175274f05a475a0c506.png?compress=1&resize=1200x900&vertical=top" class="img-thumbnail">
                                            
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle px-2 border text-dark bg-white border-secondary">
                                                {{item.quantity}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="fs-5 fw-semibold  text-dark">{{item.item.tblFields.title}}</div>
                                        {{item.item.tblFields.description}}
                                    </div>
                                    <div class="col-auto fw-semibold text-primary">
                                        $ {{item.item.tblFields.amount.numberFormat(2)}}
                                    </div>
                                    <div class="col-auto fw-semibold text-dark">
                                        <button @click="deleteItem(item.item.tblFields.package_id)" type="button" class="btn-close btn-close-white" aria-label="Close"></button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        Total
                                    </div>
                                    <div class="col-auto fs-4 fw-semibold text-primary">
                                        $ {{cart.resume.amount.numberFormat(2)}}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button @click="nextStep" class="btn mb-0 btn-lg fs-4 btn-primary bg-gradient-primary w-100 shadow-none">¡Realizar <u>compra</u>!</button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { StorecheckoutViewer } 