import { User } from '../../src/js/user.module.js?v=2.1.9'   

const IptvclientsViewer = {
    name : 'iptvclients-viewer',
    data() {
        return {
            User: new User,
            clients: null,
            credits: 0,
            clientsAux: null,
            feedBack: null,
            query: null,
            kind: -1,
            KINDS: {
                ALL: { 
                    code: -1,
                    text: 'Todos',
                    _class : 'bg-dark',
                },
                DEMO: { 
                    code: 0,
                    text: 'Demo',
                    _class : 'bg-dark',
                },
                SERVICE: { 
                    code: 1,
                    text: 'Servicio',
                    _class : 'bg-dark',
                },
            },
            STATUS_DEMO: {
                DELETE: { 
                    code: -1,
                    text: 'Eliminada',
                    _class : 'bg-dark',
                },
                FOR_ACTIVATE: { 
                    code: 0,
                    text: 'Esperando demo',
                    _class : 'bg-secondary',
                },
                IN_USE: { 
                    code: 1,
                    text: 'En uso',
                    _class : 'bg-success',
                },
                EXPIRED: { 
                    code: 2,
                    text: 'Expirado',
                    _class : 'bg-warning',
                },
            },
            STATUS: {
                NOT_ENOUGH_CREDITS: {
                    _class: "alert-light text-center fw-semibold",
                    code: 0,
                    html: `<strong>Aviso importante</strong>. No tienes suficientes créditos`
                },
                CLIENT_EXIST: {
                    _class: "alert-light text-center fw-semibold",
                    code: 1,
                    html: `<strong>Aviso importante</strong>. El cliente ya exíste en tu cartera de clientes`
                },
            },
            STATUS_SERVICE: {
                DELETE: { 
                    code: -1,
                    text: 'Eliminada',
                    _class : 'bg-dark',
                },
                FOR_ACTIVATE: { 
                    code: 0,
                    text: 'Esperando servicio',
                    _class : 'bg-secondary',
                },
                IN_USE: { 
                    code: 1,
                    text: 'En uso',
                    _class : 'bg-success',
                },
                EXPIRED: { 
                    code: 2,
                    text: 'Expirado',
                    _class : 'bg-warning',
                },
            }
        }
    },
    watch: {
        query: {
            handler() {
                this.filterData()
            },
            deep: true
        },
        kind: {
            handler() {
                this.filterDataByKind()
            },
            deep: true
        },
    },
    methods: {
        filterData() {
            this.clients = this.clientsAux
            this.clients = this.clients.filter((client) => {
                return client.name.toLowerCase().includes(this.query.toLowerCase()) || client.email.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        filterDataByKind() {
            this.clients = this.clientsAux
            this.clients = this.clients.filter((client) => {
                if(this.kind == this.KINDS.DEMO.code)
                {
                    return client.demo 
                } else if(this.kind == this.KINDS.SERVICE.code) {
                    return client.service
                } else {
                    return client
                }
            })
        },
        sendCredentialsForWhatsApp(client) {
            window.open(client.whatsapp.sendWhatsApp(encodeURIComponent(`*¡Hola!* te enviamos tus datos de acceso a *Infinity*: \n\n Usuario : *${client.user_name}* \n Contraseña : *${client.client_password}*\n\nSi necesitas ayuda para el correcto funcionamiento de las apps por favor da clic en: https://zuum.link/AyudaInfinity`)))
        },
        enableAutoRenew(client) {
            client.busy = true
            this.User.enableAutoRenew({client_id:client.client_id},(response)=>{
                client.busy = false
                
                if(response.s == 1)
                {
                    client.service.autorenew = true

                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `<div class="h3 text-white">¡Gracias!</div> Hemos habilitado la autorenovación para éste cliente</div>`,
                        size: 'modal-md',
                        _class:'bg-gradient-success text-white'
                    },500)
                }
            })
        },
        disableAutoRenew(client) {
            this.User.disableAutoRenew({client_id:client.client_id},(response)=>{
                if(response.s == 1)
                {
                    client.service.autorenew = false

                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `<div class="h3 text-white">¡Gracias!</div> Hemos deshabilitado la autorenovación para éste cliente</div>`,
                        size: 'modal-md',
                        _class:'bg-gradient-success text-white'
                    },500)
                }
            })
        },
        existUser(client) {
            return new Promise((resolve, reject)=>{
                this.User.existUser({external_client_id:client.external_client_id},(response)=>{
                    if(response.s == 1)
                    {
                        resolve()
                    }

                    reject()
                })
            })
        },
        requestRenovation(client) {
            this.feedBack = null
            if(this.credits > 0)
            {
                client.busy = true
                
                this.existUser(client).then(()=>{
                    client.busy = false

                    let alert = alertCtrl.create({
                        title: "Aviso",
                        subTitle: `<div class="h5">¿Estás seguro de renovar el servicio para <strong>${client.name}?</strong></div>`,
                        buttons: [
                            {
                                text: "Sí, pedir renovación",
                                class: 'btn-success',
                                role: "cancel",
                                handler: (data) => {
                                    client.busy = true

                                    this.User.requestRenovation(client,(response)=>{
                                        if(response.s == 1)
                                        {
                                            client.demo = null
                                            client.service = {
                                                status : this.STATUS_SERVICE.FOR_ACTIVATE
                                            }
    
                                            this._getIptvCredits()
    
                                            alertInfo({
                                                icon:'<i class="bi bi-ui-checks"></i>',
                                                message: `<div class="h3 text-white">¡Gracias!</div> <div>Hemos renovado el servicio a tu cliente <strong>${client.name}</strong></div>`,
                                                size: 'modal-md',
                                                _class:'bg-gradient-success text-white'
                                            },500)
                                        }
                                    })
                                },
                            },
                            {
                                text: "Cancelar",
                                role: "cancel",
                                handler: (data) => {
                                },
                            },
                        ],
                    })
        
                    alertCtrl.present(alert.modal);
                }).catch(error => {
                    client.busy = false

                    alertInfo({
                        icon:'<i class="bi bi-x"></i>',
                        message: `No encontramos a tu cliente ${client.name}. Si el cliente tiene servicio expirado de más de 1 día es necesario darlo de alta de nuevo.`,
                        size: 'modal-md',
                        _class:'bg-gradient-warning text-white'
                    },500)
                })
            } else {
                client.busy = false

                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
        },
        requestService(client) {
            this.feedBack = null

            if(this.credits > 0)
            {
                client.busy = true
                
                this.existUser(client).then(()=>{
                    client.busy = false

                    let alert = alertCtrl.create({
                        title: "Aviso",
                        subTitle: `<div class="h5">¿Estás seguro de pedir el servicio para <strong>${client.name}?</strong></div>`,
                        buttons: [
                            {
                                text: "Sí, pedir servicio",
                                class: 'btn-success',
                                role: "cancel",
                                handler: (data) => {
                                    client.busy = false

                                    this.User.requestClientService(client,(response)=>{
                                        if(response.s == 1)
                                        {
                                            client.demo = null
                                            client.service = {
                                                status : this.STATUS_SERVICE.FOR_ACTIVATE
                                            }
    
                                            alertInfo({
                                                icon:'<i class="bi bi-ui-checks"></i>',
                                                message: `<div class="h3 text-white">¡Gracias!</div> <div>Pronto enviaremos las credenciales para tu cliente <strong>${client.name}</strong></div>`,
                                                size: 'modal-md',
                                                _class:'bg-gradient-success text-white'
                                            },500)
                                        }
                                    })
                                },
                            },
                            {
                                text: "Cancelar",
                                role: "cancel",
                                handler: (data) => {
                                },
                            },
                        ],
                    })
        
                    alertCtrl.present(alert.modal);
                }).catch(error => {
                    client.busy = false

                    alertInfo({
                        icon:'<i class="bi bi-x"></i>',
                        message: `No encontramos a tu cliente ${client.name}. Si el cliente tiene servicio expirado de más de 1 día es necesario darlo de alta de nuevo.`,
                        size: 'modal-md',
                        _class:'bg-gradient-warning text-white'
                    },500)
                })
            } else {
                client.busy = false

                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
        },
        copyToClipboard(client,event) {
            const text = `usuario ${client.user_name} contraseña ${client.client_password}`
            navigator.clipboard.writeText(text).then(() => {
                event.target.innerText = 'Copiado'
            });
        },
        getIptvClients() {
            return new Promise((resolve,reject)=> {
                this.User.getIptvClients({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.clients)
                    }

                    reject()
                })
            })
        },
        _getIptvCredits() {
            this.getIptvCredits().then((credits)=>{
                this.credits = credits
            }).catch(() => this.credits = 0)
        },
        getIptvCredits() {
            return new Promise((resolve,reject)=> {
                this.User.getIptvCredits({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.credits)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {       
        this._getIptvCredits()
        this.getIptvClients().then((clients)=>{
            this.clients = clients
            this.clientsAux = clients
        }).catch(() => this.clients = false)
    },
    template : `
    <div
        v-if="clientsAux"
            class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                    <div ><span class="badge p-0 text-dark">Total {{clients.length}}</span></div>
                        <div class="fw-semibold fs-4 text-primary">Lista de clientes</div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge p-0 text-dark">Créditos</span>
                        <div class="fs-4 fw-bold">
                            <a href="../../apps/store/credit">{{credits.numberFormat(2)}}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <input type="search" class="form-control" v-model="query" placeholder="buscar por nombre o correo"/>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" v-model="kind" aria-label="Filtro">
                            <option v-for="_kind in KINDS" v-bind:value="_kind.code">
                                {{ _kind.text }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="feedBack" class="card-body">
                <div class="alert" :class="feedBack._class">
                    <span v-html="feedBack.html"></span>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr class="font-bold text-center text-secondary text-xs text-uppercase opacity-7">
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Acceso</th>
                                <th>Configuración</th>
                                <th>Estatus</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients">
                                <td class="align-middle text-xs text-center">
                                    {{client.client_id}}
                                </td>
                                <td class="text-center fw-semibold text-xs text-secondary">
                                    <div>{{client.name}}</div>
                                    <div>{{client.whatsapp}}</div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.user_name && client.client_password" class="row align-items-center">
                                        <div class="mb-1">
                                            <div class="text-xs">Usuario </div>
                                            <strong>{{client.user_name}}</strong>
                                        </div>
                                        <div>
                                            <div class="text-xs">Contraseña </div>
                                            <strong>{{client.client_password}}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.demo">
                                        <div><span class="badge mb-1 d-grid bg-primary">DEMO</span></div>
                                        <div><span class="badge mb-1 d-grid bg-danger" v-if="client.demo.adult">Canales +18</span></div>

                                        <div>
                                            <span class="badge mb-1 d-grid" v-if="client.demo.status == STATUS_DEMO.DELETE.code" :class="STATUS_DEMO.DELETE._class">
                                                {{STATUS_DEMO.DELETE.text}}
                                            </span>
                                            <span class="badge mb-1 d-grid" v-else-if="client.demo.status == STATUS_DEMO.FOR_ACTIVATE.code" :class="STATUS_DEMO.FOR_ACTIVATE._class">
                                                {{STATUS_DEMO.FOR_ACTIVATE.text}}
                                            </span>
                                            <span class="badge mb-1 d-grid" v-else-if="client.demo.status == STATUS_DEMO.IN_USE.code" :class="STATUS_DEMO.IN_USE._class">
                                                {{STATUS_DEMO.IN_USE.text}}
                                            </span>
                                            <span class="badge d-grid" v-else-if="client.demo.status == STATUS_DEMO.EXPIRED.code" :class="STATUS_DEMO.EXPIRED._class">
                                                {{STATUS_DEMO.EXPIRED.text}}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else-if="client.service">
                                        <div><span class="badge mb-1 d-grid bg-danger" v-if="client.service.adult">Canales +18</span></div>
                                        <div><span class="badge mb-1 d-grid bg-primary">SERVICIO</span></div>

                                        <div>
                                            <span class="badge mb-1 d-grid" v-if="client.service.status == STATUS_SERVICE.DELETE.code" :class="STATUS_SERVICE.DELETE._class">
                                                {{STATUS_SERVICE.DELETE.text}}
                                            </span>
                                            <span class="badge mb-1 d-grid" v-else-if="client.service.status == STATUS_SERVICE.FOR_ACTIVATE.code" :class="STATUS_SERVICE.FOR_ACTIVATE._class">
                                                {{STATUS_SERVICE.FOR_ACTIVATE.text}}
                                            </span>
                                            <span class="badge mb-1 d-grid" v-else-if="client.service.status == STATUS_SERVICE.IN_USE.code" :class="STATUS_SERVICE.IN_USE._class">
                                                {{STATUS_SERVICE.IN_USE.text}}
                                            </span>
                                            <span class="badge mb-1 d-grid" v-else-if="client.service.status == STATUS_SERVICE.EXPIRED.code" :class="STATUS_SERVICE.EXPIRED._class">
                                                {{STATUS_SERVICE.EXPIRED.text}}
                                            </span>
                                        </div>

                                        <div v-if="client.service.autorenew">
                                            <span class="badge d-grid bg-secondary">
                                                Autorenovación
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <span class="badge bg-secondary">Sin información</span>
                                    </div>
                                </td>
                                <td class="text-center text-xs fw-semibold">
                                    <div v-if="!client.busy">
                                        <div v-if="client.demo">
                                            <span v-if="client.demo.status != STATUS_DEMO.FOR_ACTIVATE.code">
                                                <span v-if="client.demo.left">
                                                    <span v-if="client.demo.left.active">
                                                        <div class="text-left text-xs pb-2">Quedan {{client.demo.left.minutes}} minutos(s)</div>
                                                        <div class="progress w-100">
                                                            <div style="height:0.5rem":style="{width: client.demo.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </span>
                                                    <span v-else class="badge bg-danger">
                                                        Expirada
                                                    </span>
                                                </span>
                                            </span>
                                        </div>
                                        <div v-else-if="client.service">
                                            <span v-if="client.service.status != STATUS_SERVICE.FOR_ACTIVATE.code">
                                                <span v-if="client.service.left">
                                                    <span v-if="client.service.left.active">
                                                        <div class="text-left text-xs pb-2">Quedan {{client.service.left.days}} dias(s)</div>
                                                        <div class="progress w-100">
                                                            <div style="height:0.5rem":style="{width: client.service.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </span>
                                                    <span v-else class="badge bg-danger">
                                                        Expirada
                                                    </span>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-primary px-3 btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                        </button>
                                        <ul class="dropdown-menu shadow">
                                            <div v-if="client.service">
                                                <div v-if="client.service.status == STATUS_SERVICE.EXPIRED.code">
                                                    <li><button class="dropdown-item" :disabled="client.service.request_renovation" @click="requestRenovation(client)">Renovar suscripción</button></li>
                                                </div>
                                            </div>
                                            <div v-if="client.demo">
                                                <li><button class="dropdown-item" @click="requestService(client)" >Pedir servicio </button></li>
                                            </div>
                                            <div v-if="client.user_name && client.client_password">
                                                <div v-if="client.service.autorenew">
                                                    <li><button class="dropdown-item" @click="disableAutoRenew(client)">Desactivar autorenovación</button></li>
                                                </div>
                                                <div v-else>
                                                    <li><button class="dropdown-item" @click="enableAutoRenew(client)">Activar autorenovación</button></li>
                                                </div>
                                            </div>
                                            <li>
                                                <button @click="client.viewClient = !client.viewClient" class="dropdown-item">
                                                    <span v-text="client.viewClient ? 'Ocultar datos': 'Ver datos'"></span>
                                                </button>
                                            </li>
                                            <li>
                                                <button @click="copyToClipboard(client,$event)" class="dropdown-item">
                                                    Copiar datos de acceso
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-secondary">Cantidad de clientes {{clients.length}}</span></div>
                </div>
            </div>
        </div>
        <div v-else-if="clients == false">
            <div class="alert alert-light text-center">
                <div>Comienza a generar clientes para tu negocio</div>
                <div class="fw-semibold fs-5">Puedes comenzar entrando a nuestra educación <a class="text-white" href="../../apps/academy/"><u>Ver educación</u></a></div>
            </div>
        </div>
    `,
}

export { IptvclientsViewer } 