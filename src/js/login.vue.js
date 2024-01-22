import { User } from '../../src/js/user.module.js?v=2.1.9'   

Vue.createApp({
    components : { 
        
    },
    data() {
        return {
            User : new User,
            user: {
                email: null,
                password: null,
            },
            redirection: {
                page: null,
                route_name: null
            },
            feedback : false,
            isValidMail : false,
            fieldPasswordType : 'password',
            userComplete : false,
        }
    },
    watch : {
        user : {
            handler() {
                this.checkFields()
                this.checkEmail()
            },
            deep: true
        },
    },
    methods: {
        toggleFieldPasswordType : function() {
            this.fieldPasswordType = this.fieldPasswordType == 'password' ? 'text' : 'password'
        },
        doLogin : function() {
            this.feedback = false

            // dinamicLoader.showLoader($("#button"))
            
            this.User.doLogin(this.user,(response)=>{
                if(response.s == 1)
                {
                    if(this.redirection.page)
                    {
                        window.location.href = this.redirection.page
                    } else {
                        window.location.href = '../../apps/backoffice'
                    }
                } else if(response.r == "INVALID_PASSWORD") {
                    this.feedback = "Las contraseña indicada no es correcta. Intente nuevamente"
                } else if(response.r == "INVALID_CREDENTIALS") {
                    this.feedback = "Las credenciales proporcionadas no son correctas, intente nuevamente"
                }
            })
        },
        checkEmail : function() {
            this.isValidMail = isValidMail(this.user.email)
        },
        checkFields : function() {
            this.userComplete = this.user.email && this.user.password
        }
    },
    mounted() {
        if(getParam('page'))
        {
            this.redirection.page = getParam('page')
            this.redirection.route_name = getParam('route_name')
        }
    }
}).mount('#app')