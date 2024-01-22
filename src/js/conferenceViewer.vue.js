import { User } from '../../src/js/user.module.js?v=2.1.9'   

const ConferenceViewer = {
    name : 'conference-viewer',
    data() {
        return {
            User : new User,
            timezoneConfigurated: null,
            conferences: null
        }
    },
    methods: {
        openLink(conference) {
            conference.loading = true
            window.location.href = conference.link
        },
        getConferences() {
            return new Promise((resolve,reject) => {
                this.User.getConferences({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.conferences,response.timezoneConfigurated)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {   
        this.getConferences().then((conferences,timezoneConfigurated)=>{
            this.conferences = conferences
            this.timezoneConfigurated = timezoneConfigurated
        })
    },
    template : `
        <div v-if="conferences" class="row mt-3">
            <div class="col-12">
                <div v-if="!timezoneConfigurated" class="alert alert-light text-dark text-center">
                    Por favor, configura tu zona horaria <a class="text-decoration-underline fw-sembold" href="../../apps/backoffice/profile?e=profile">aquí</a>, así podrás ver los horarios de nuestras conferencias en tu uso horario.
                </div>
            </div>
            <div v-for="(day,index) in conferences"
                :class="day.conferences ? 'col-12 col-xl' : 'col-12 col-xl-1 opacity-4'">
                <div class="line-through text-secondary py-3 fw-sembold text-center">
                    <div v-if="index == new Date().getDay()-1">
                        <span class="badge bg-success">HOY</span>
                    </div>
                    <div>{{day.day}}</div>
                </div>

                <div class="card">
                    <div v-if="day.conferences">
                        <ul class="list-group list-group-flush border-0">
                            <li v-for="conference in day.conferences" class="list-group-item f-zoom-element py-3 position-relative">
                                <div v-if="!conference.loading">
                                    <div class="avatar">
                                        <img :src="conference.image" class="avatar rounded-circle" alt="user" style="object-fit:cover;"/>
                                    </div>
                                    <div class="position-absolute top-0 end-0 mt-2 me-2 text-end">
                                        <div class="mb-n2">
                                            <span class="badge text-xs p-0 text-primary"><i class="bi bi-clock me-1"></i> {{conference.time_formatted }}</span>
                                        </div>
                                        <div class="mb-n2">
                                            <span class="badge text-xs p-0 text-primary text-xxs"> {{conference.timezone }}</span>
                                        </div>
                                        <div>
                                            <span class="badge text-success text-xxs p-0">{{conference.catalog_conference_title}}</span>
                                        </div>
                                    </div>
                                    <div class="row cursor-pointer" @click="openLink(conference)">
                                        <div class="col">
                                            <div class="pb-3"><i class="bi bi-megaphone-fill"></i> <span class="text-dark fw-sembold text-decoration-underline">{{conference.title}}</span></div>
                                            
                                            <div class="row">
                                                <div class="col">
                                                    <i class="bi bi-person-circle"></i> <span class="text-capitalize text-xs">{{conference.name}}</span>
                                                </div>
                                                <div v-if="index == new Date().getDay()-1" class="col-auto">
                                                    <span v-if="conference.time.inTimeInProgress()" class="badge text-xs bg-danger">   
                                                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> LIVE
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-grow" style="width: 1.5rem; height: 1.5rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ConferenceViewer } 