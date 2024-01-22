import { User } from '../../src/js/user.module.js?v=2.1.9'   

Vue.createApp({
    components : { 
        
    },
    data() {
        return {
            user: {
                email: null,
                phone: null,
                names: null,
                country_id: 159, // loads by default México
                passwordAgain: null,
                password: null,
                referral: {
                    user_login_id: 0,
                    names: '',
                    image : ''
                },
                utm: false,
            },
            countries : {},
            loading : false,
            User : null,
            feedback : false,
            isValidMail : false,
            passwordsMatch : null,
            fieldPasswordType : 'password',
            userComplete : false,
        }
    },
    watch : {
        user : {
            handler() {
                this.checkEmail()
                this.checkFields()
                this.checkPasswords()
            },
            deep: true
        },
    },
    methods: {
        getReferral(user_login_id) {
            this.feedback = false

            this.User.getReferral({user_login_id:user_login_id,utm:this.user.utm},(response)=>{
                if(response.s == 1)
                {
                   Object.assign(this.user.referral,response.referral)
                } else if(response.r == "NOT_DATA") {
                    this.feedback = "No encontramos información del link de referido proporcionado"
                }
            })
        },
        toggleFieldPasswordType() {
            this.fieldPasswordType = this.fieldPasswordType == 'password' ? 'text' : 'password'
        },
        doSignup() {
            this.loading = true
            this.feedback = false
            
            this.User.doSignup(this.user,(response)=>{
                this.loading = false

                if(response.s == 1)
                {
                    window.location.href = '../../apps/backoffice'
                } else if(response.r == "MAIL_ALREADY_EXISTS") {
                    this.feedback = 'El correo proporcionado ya existe'
                }
            })
        },
        getCountries() {
            this.User.getCountries(this.user,(response)=>{
                if(response.s == 1)
                {
                    this.countries = response.countries
                }
            })
        },
        checkEmail() {
            this.isValidMail = isValidMail(this.user.email)
        },
        getUtm() {
            if(window.location.pathname.split('/').inArray('join') != -1) {
                this.user.utm = 'join'
            }
        },
        checkPasswords() {
            if(this.user.password != null && this.user.passwordAgain != null)
            {
                if(this.user.passwordAgain != this.user.password)   
                {
                    this.passwordFeedback = `<span class="text-danger fw-bold"><i class="bi bi-patch-exclamation"></i> Las contraseñas no coinciden</span>`
                } else {
                    this.passwordFeedback = '<span class="text-success fw-bold"><i class="bi bi-patch-check"></i> Las contraseñas coinciden</span>'
                }
            }
        },
        checkFields() {
            this.userComplete = this.isValidMail && this.user.password && this.user.phone && this.user.names
        }
    },
    mounted() 
    {
        this.User = new User

        $(this.$refs.phone).mask('(00) 0000-0000');

        this.getCountries()
        this.getUtm() // getting campaign

        if(getParam('uid'))
        {
            this.getReferral(getParam('uid'))
        }

    },
}).mount('#app')