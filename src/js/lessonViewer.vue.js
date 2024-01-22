import { User } from '../../src/js/user.module.js?v=2.1.9'   

const LessonViewer = {
    name : 'lesson-viewer',
    data() {
        return {
            User: new User,
            fullMode: false,
            sessions: null,
            course: null,
            STATUS : {
                UNPUBLISHED: 0,
                PUBLISHED: 1
            },
            CATALOG_MULTIMEDIA : {
                TEXT: 1,
                AUDIO: 2,
                VIDEO: 3,
                HTML: 4,
            }
        }
    },
    methods: {
        filterData() {
            this.courses = this.campaignsAux

            this.courses = this.courses.filter((campaign) => {
                return campaign.name.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        getSessionPerCourse(session_take_by_user_per_course_id) {
            return new Promise((resolve,reject) => {
                this.User.getSessionPerCourse({session_take_by_user_per_course_id:session_take_by_user_per_course_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.course)
                    }

                    reject()
                })
            })
        },
        getSession(order) {
            return this.sessions.filter(session => {
                return session.order == order
            })[0]
        },
        selectSession(session) {
            this.course.session = session

            if(this.course.session.catalog_multimedia_id == this.CATALOG_MULTIMEDIA.VIDEO)
            {
                this.$refs.video.load();
            }
        },
        nextSesssion() {
            this.setSessionAsTaked(this.course.session).then((sessionTaked) => {
                console.log(sessionTaked)

                if(sessionTaked)
                {
                    let session = this.getSession(this.course.session.order)
                    session.sessionTaked = sessionTaked
                }

                const nextOrder = this.course.order+1 <= this.sessions.length ? this.course.order+1 : this.course.order

                this.selectSession(this.getSession(nextOrder))
            })
        },
        setSessionAsTaked(session) {
            return new Promise((resolve) => {
                this.User.setSessionAsTaked({session_per_course_id:session.session_per_course_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.sessionTaked)
                    } else {
                        resolve(false)
                    }
                })
            })
        },
        getSessionsCourse(course_id) {
            return new Promise((resolve,reject) => {
                this.User.getSessionsCourse({course_id:course_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.sessions)
                    }

                    reject()
                })
            })
        },
        getCourse(course_id) {
            return new Promise((resolve,reject) => {
                this.User.getCourse({course_id:course_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.course)
                    }

                    reject()
                })
            })
        },
        getLastOrder() {
            let order = 1
            this.sessions.map((session) => {
                if(session.sessionTaked)
                {
                    order = session.order
                }
            })

            return order
        },
    },
    mounted() {
        this.getCourse(getParam("cid")).then((course)=>{
            this.course = course

            this.getSessionsCourse(course.course_id).then((sessions)=>{
                this.sessions = sessions

                this.course.order = this.getLastOrder()
                this.selectSession(this.getSession(this.course.order))
            })
        })
    },
    template : `
        <div v-if="course" class="row align-items-top">
            <div class="col-12"
                :class="fullMode ? 'mb-3' : 'col-xl-8'">
                <div class="card overflow-hidden border-radius-xl mb-3 mb-xl-0">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-auto fs-4">
                                <span class="badge border border-dark text-dark"><i class="bi bi-easel"></i></span>
                            </div>
                            <div class="col">
                                <span class="fw-semibold text-xs text-secondary">
                                    {{course.title}}
                                </span>
                                <div v-if="course.session" class="fs-5 text-primary">
                                    {{course.session.title}}
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto" v-if="course.session.aviable">
                                <button @click="fullMode = !fullMode" class="btn btn-light mt-3 mt-xl-0 me-2 shadow-none mb-0">
                                    <span v-if="fullMode">
                                        <i class="bi bi-fullscreen-exit"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-fullscreen"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-if="course.session" class="card-body">
                        <div v-if="course.session.aviable">
                            <div v-if="course.session.catalog_multimedia_id == CATALOG_MULTIMEDIA.TEXT">
                                TEXT
                            </div>
                            <div v-else-if="course.session.catalog_multimedia_id == CATALOG_MULTIMEDIA.AUDIO">
                                AUDIO
                            </div>
                            <div v-else-if="course.session.catalog_multimedia_id == CATALOG_MULTIMEDIA.VIDEO">
                                <span v-html="course.session.course"></span>
                            </div>
                            <div v-else-if="course.session.catalog_multimedia_id == CATALOG_MULTIMEDIA.HTML">
                                <div v-html="course.session.course">
                                </div>
                            </div>
                        </div>
                        <div v-else class="fs-5 text-secondary fw-semibold text-center">
                            <div class="fs-4"><i class="bi bi-clock"></i></div>
                            Esta lección estará disponible próximamente
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="sessions" class="col-12 col-xl-4">
                <div class="card overflow-scroll border-radius-xl" style="height:50rem">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col text-secondary text-xs">
                                Videos
                            </div>
                            <div class="col-auto">
                                <span class="badge border-secondary border text-secondary text-xxs">
                                    Total {{sessions.length}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush" 
                        v-if="course.session">
                        <li v-for="session in sessions" class="list-group-item py-3 list-group-item-action cursor-pointer"
                            :class="course.session.session_per_course_id == session.session_per_course_id ? 'bg-gradient-dark text-white': ''">
                            <div @click="selectSession(session)" class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge fs-5 border"
                                        :class="course.session.session_per_course_id == session.session_per_course_id ? 'text-white border-white': 'text-primary border-primary'"
                                        ><i class="bi bi-collection-play"></i></span>
                                </div>
                                <div class="col">
                                    <div v-if="session.order > 0" class="fs-6 fw-semibold">
                                        <span class="badge p-0"
                                            :class="course.session.session_per_course_id == session.session_per_course_id ? 'border-white text-white': 'border-primary text-primary'">Módulo {{session.order}}</span>
                                        
                                        <span v-if="!session.aviable" class="badge border text-xxs ms-2"
                                            :class="course.session.session_per_course_id == session.session_per_course_id ? 'border-white text-white': 'border-warning text-warning'">Próximamente</span>
                                    </div>
                                    <div class="fs-6 fw-semibold">
                                        <span v-if="session.sessionTaked" class="text-success">
                                            <i class="bi bi-check-circle"></i>
                                        </span>
                                        {{session.title}}
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `,
}

export { LessonViewer } 