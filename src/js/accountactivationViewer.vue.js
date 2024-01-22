import { User } from '../../src/js/user.module.js?v=2.1.9'   

const AccountactivationViewer = {
    name : 'accountactivation-viewer',
    data() {
        return {
            User: new User,
            active : null
        }
    },
    methods: {
        getAccountActivation() {
            this.User.getAccountActivation({},(response)=>{
                if(response.s == 1)
                {
                    this.active = response.active
                }
            })
        }
    },
    mounted() 
    {   
        this.getAccountActivation()
    },
    template : `
        <div v-if="active" class="alert alert-success text-white text-center">
            <strong>Felicidades</strong>
            Estas activo en MoneyTv
        </div>
        <div v-else-if="active == false" class="alert alert-danger text-center text-white">
            <strong>Importante</strong>
            No estas activo, debes de activar para poder continuar con el acceso a MoneyTv

            <div class="mt-3">
                <a href="../../apps/store/package" class="btn btn-outline-light">Activarme ahora mismo</a>
            </div>
        </div>
    `,
}

export { AccountactivationViewer } 