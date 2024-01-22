import { UserSupport } from '../../src/js/userSupport.module.js?t=6'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            userComplete: false,
            UserSupport: null,
            user: {
                names: null,
                signup_date: null,
                referral: {
                    names: null,
                    user_login_id: null
                },
                password: null,
                email: null,
                phone: null,
            },
        }
    },
    watch: {
        user:
        {
            handler() {
                this.userComplete = this.user.names != null && this.user.email != null

                this.getReferral(this.user.referral.user_login_id)
            },
            deep: true
        }
    },
    methods: {
        updateUser: function () {
            this.UserSupport.updateUser({ user: this.user }, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Actualizado"
                }
            })
        },
        getReferral: function (user_login_id) {
            this.UserSupport.getReferral({user_login_id:user_login_id},(response)=>{
                if(response.s == 1)
                {
                    Object.assign(this.user.referral,response.referral)
                }
            })
        },
        getUser: function (user_login_id) {
            return new Promise( (resolve) => {
                this.UserSupport.getUser({ user_login_id: user_login_id }, (response) => {
                    if (response.s == 1) {
                        Object.assign(this.user, response.user)
                    }

                    resolve(response.user_referral_id)
                })
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        $(this.$refs.phone).mask('(00) 0000-0000');

        if (getParam('ulid')) {
            this.getUser(getParam('ulid')).then((user_login_id) => {
                this.getReferral(user_login_id)
            })
        }
    },
}).mount('#app')