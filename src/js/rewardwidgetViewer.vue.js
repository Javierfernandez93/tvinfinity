import { User } from '../../src/js/user.module.js?v=2.1.9'   

const RewardwidgetViewer = {
    name : 'rewardwidget-viewer',
    data() {
        return {
            User: new User
        }
    },
    methods: {
    },
    mounted() 
    {     
    },
    template : `
        <div class="card mt-3">
            <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('../../assets/img/ivancik.jpg')">
                <span class="mask bg-gradient-danger"></span>
                <div class="card-body position-relative z-index-1 h-100 p-3">
                <h4 class="text-white font-weight-bolder mb-3">¡Gana premios y dinero!</h4>
                <p class="text-white mb-3">
                    Conoce las recompensas por vender IPTV y alcanzar objetivos medibles todos los meses
                </p>
                <a class="btn btn-round btn-outline-white mb-0" href="../../apps/rewards/">
                    Ver programa de recompensas
                    <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i
                ></a>
                </div>
            </div>
        </div>
    `,
}

export { RewardwidgetViewer } 