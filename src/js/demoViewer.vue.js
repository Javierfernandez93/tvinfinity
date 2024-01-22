import { Guest } from '../../src/js/guest.module.js?v=2.1.9'   

const DemoViewer = {
    name : 'demo-viewer',
    data() {
        return {
            Guest: new Guest,
            busy : false,
            status : {
                success : false
            },
            visitorInfo : null,
            client : {
                name: 'usuario',
                phoneCode: null,
                adult: 1,
                whatsapp: '',
                demo: {
                    enabled: true
                }
            },
            STATUS : {
                HAS_DEMO : {
                    text: 'Ya tiene una demo solicitada',
                    _class: 'bg-danger',
                    success: false,
                },
                INVALID_PHONE : {
                    text: 'El número proporcionado es incorrecto, por favor escribe tus 10 dígitos',
                    _class: 'invalid-feedback',
                    success: false,
                },
                VALID_PHONE : {
                    text: 'El número proporcionado es correcto',
                    _class: 'valid-feedback',
                    success: true,
                }
            }
        }
    },
    watch: {
        'client.whatsapp': {
            handler() {
                this.status = null 

                if(this.client.whatsapp.isValidPhoneLength()) 
                {
                    this.status = this.STATUS.VALID_PHONE
                } else {
                    this.status = this.STATUS.INVALID_PHONE
                }
            },
            deep: true
        },
    },
    methods: {
        getLicences() {
            return new Promise((resolve,reject)=> {
                this.Guest.getLicences({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.licences)
                    }

                    reject()
                })

            })
        },
        requestDemo() {
            this.busy = true
            this.Guest.requestDemo(this.client, (response) => {
                this.busy = false
                if (response.s == 1) {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `<div class="h3 text-white">¡Bienvenido!</div> <div>Hemos enviado los datos de tu demo a tu WhatsApp</div>`,
                        size: 'modal-md',
                        _class:'bg-gradient-success text-white'
                    },500)
                }
            })
        },
        getIpInfoVisitor() {
            return new Promise((resolve,reject)=> {
                this.Guest.getIpInfoVisitor({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.visitorInfo)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {      
        this.getIpInfoVisitor().then((visitorInfo) => {
            $('#whatsapp').mask('0000-0000')
            
            this.visitorInfo = visitorInfo
            this.client.phoneCode = visitorInfo.phone_code
        })
    },
    template : `
        <div v-if="visitorInfo" class="card">
            <div class="card-header  text-center">
                <h3>Solicita tu demo</h3>
                <h4>MoneyTv te invita a probar una demo</h4>
                <p>Ingresa tu WhatsApp (10 dígitos) y después da clic en Solicitar demo</p>
            </div>
            <div class="card-body w-100">
                
                <div class="input-group mb-3">
                    <span class="input-group-text fs-4" id="basic-addon1">
                        <img :src="visitorInfo.country_id.getCoutryImage()" class="avatar avatar-sm me-2"/>
                        + {{visitorInfo.phone_code}}
                    </span>

                    <input :class="client.whatsapp.isValidPhoneLength() ? 'is-valid' : 'is-invalid'" :autofocus="true" type="text" id="whatsapp" v-model="client.whatsapp" class="form-control px-3 fs-4" placeholder="Número de teléfono" aria-label="Número de teléfono" aria-describedby="basic-addon1">
                    
                    <div v-if="status" id="validationServer03Feedback" :class="status._class">
                        {{status.text}}
                    </div>
                </div>
            </div>
            <div class="card-footer w-100 d-grid">
                <button :disabled="!status.success || busy" @click="requestDemo" type="button" class="btn fs-4 btn-success mb-0 shadow-none btn-lg">
                    
                    <span v-if="busy">
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    </span>
                    <span v-else>
                        Solicitar demo
                    </span>
                </button>
            </div>
        </div>
    `,
}

export { DemoViewer } 