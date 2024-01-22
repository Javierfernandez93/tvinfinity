import { UserSupport } from '../../src/js/userSupport.module.js?t=6'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            userComplete: false,
            UserSupport: null,
            feedback: null,
            countries: {},
            user: {
                names: null,
                password: null,
                email: null,
                phone: null,
                country_id: 159,
                referral: {
                    user_login_id: null
                }
            },
        }
    },
    watch: {
        user:
        {
            handler() {
                this.userComplete = this.user.names != null && this.user.email != null && this.user.phone != null && this.user.password != null
            },
            deep: true
        }
    },
    methods: {
        saveUser: function () {
            this.feedback = null
            this.UserSupport.saveUser({user:this.user}, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Actualizado"
                } else if (response.r == 'MAIL_ALREADY_EXISTS') {
                    this.feedback = 'El correo proporcionado ya está registrado'
                }
            })
        },
        getCountries: function () {
            this.UserSupport.getCountries({  }, (response) => {
                if (response.s == 1) {
                    Object.assign(this.countries, response.countries)
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        $(this.$refs.phone).mask('(00) 0000-0000');

        this.getCountries()
    },
}).mount('#app')