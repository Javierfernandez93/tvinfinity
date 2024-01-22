import { UserSupport } from '../../src/js/userSupport.module.js?t=6'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            administratorComplete: false,
            UserSupport: null,
            feedback: null,
            administrator: {
                names: null,
                password: null,
                email: null,
                permissions: {},
            },
        }
    },
    watch: {
        administrator:
        {
            handler() {
                this.administratorComplete = this.administrator.names != null && this.administrator.email != null && this.administrator.password != null
            },
            deep: true
        }
    },
    methods: {
        saveAdministrator: function () {
            this.feedback = null
            this.UserSupport.saveAdministrator({administrator:this.administrator}, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Guardado"
                } else if (response.r == 'MAIL_ALREADY_EXISTS') {
                    this.feedback = 'El correo proporcionado ya está registrado'
                }
            })
        },
        getAdministratorPermissions: function () {
            this.UserSupport.getAdministratorPermissions({user:this.user}, (response) => {
                if (response.s == 1) {
                    Object.assign(this.administrator.permissions, response.permissions)
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        this.getAdministratorPermissions();
    },
}).mount('#app')