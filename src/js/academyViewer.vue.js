import { User } from '../../src/js/user.module.js?v=2.1.9'   

const AcademyViewer = {
    name : 'academy-viewer',
    data() {
        return {
            User: new User,
            query: null,
            courses: null,
            coursesAux: null,
            STATUS : {
                UNPUBLISHED: 0,
                PUBLISHED: 1
            },
            CATALOG_COURSE_TYPE : {
                STANDAR: 1,
                ELITE: 2,
                AGENCY: 3,
            }
        }
    },
    watch : {
        // query : {
        //     handler() {
        //         this.filterData()
        //     },
        //     deep: true
        // }
    },
    methods: {
        filterData: function () {
            this.courses = this.campaignsAux

            this.courses = this.courses.filter((campaign) => {
                return campaign.name.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        goToSessions: function (course_id) {
            window.location.href = `../../apps/academy/lesson?cid=${course_id}`
        },
        getCoursesList: function () {
            return new Promise((resolve,reject) => {
                this.User.getCoursesList({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.courses)
                    }

                    reject()
                })
            })
        },
        enrollInCourse: function (course_id) {
            this.User.enrollInCourse({course_id:course_id}, (response) => {
                if (response.s == 1) {
                    this.goToSessions(course_id)
                }
            })
        },
    },
    mounted() {
        this.getCoursesList().then((courses)=>{
            this.courses = courses
        })
    },
    template : `
        <div v-for="course in courses" class="card mb-3 overflow-hidden">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-auto">
                        <div class="avatar avatar-xl">
                            <img :src="course.image" class="avatar avatar-xl" alt="Imagen"/> 
                        </div>
                    </div>
                    <div class="col-12 col-xl">
                        <div class="row position-relative" style="z-index:2">
                            <div class="col">
                                <div class="h4">
                                    {{course.title}}
                                </div>
                                <div class="fw-semibold"><i class="bi bi-person-circle"></i> {{course.names}}</div>
                            </div>
                            <div class="col-auto d-flex align-items-end">
                                <div class="row">
                                    <div class="col-12">
                                        <button @click="enrollInCourse(course.course_id)" class="btn shadow-none btn-primary mb-0">Ver</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="course.hasLessonTaked" class="row pt-3">
                                <div class="col-12 text-light-50 text-center">
                                    Ultima lección tomada <span class="text-white fw-semibold">{{course.lastCourse.title}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AcademyViewer } 