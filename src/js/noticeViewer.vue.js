import { User } from '../../src/js/user.module.js?v=2.1.9'   

const POP_UP = 1
const NEW = 2

const NoticeViewer = {
    name : 'notice-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User : new User,
            notices : [],
            showPop : true,
            pops : []
        }
    },
    watch : {
        pops: {
            handler() {
                this.showPop = true
            },
            deep: true
        },
    },
    methods: {
        nextPop : function(index) {
            const max = this.pops.length - 1
            
            this.pops[index].view = false
            
            if(index < max)
            {
                this.pops[index+1].view = true
            } else {
                $(this.$refs.viewerModal).modal('hide')
            }
        },
        setFirstPopAsView : function() {
            this.pops[0].view = true
        },
        getNoticesList : function() {
            this.User.getNoticesList({},(response)=>{
                if(response.s == 1)
                {
                    response.notices.map((notice)=>{
                        if(notice.catalog_notice_id == NEW)
                        {
                            this.notices.push(notice)
                        } else if(notice.catalog_notice_id == POP_UP) {
                            this.pops.push(
                                Object.assign(notice, {aviable:false})
                            )
                        }
                    })
                    
                    if(this.pops.length > 0) 
                    {
                        this.setFirstPopAsView()
                        
                        setTimeout(()=>{
                            $(this.$refs.viewerModal).modal('show')
                        },200)
                    }
                }
            })
        }
    },
    updated() {
    },
    mounted() 
    {   
        this.getNoticesList()
    },
    template : `
        <div
            v-if="notices"
            class="card mt-4 overflow-hidden border-radius-xl">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="text-semibold text-dark">Listado de avisos</div>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success text-xs">
                            {{ notices.length }}
                        </span>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li    
                    v-for="notice in notices" 
                    class="list-group-item list-group-item-zoom py-3 cursor-pointer">
                    <div class="row align-items-center">
                        <div class="col-auto h5 d-none d-sm-block">
                            <span  
                                v-if="notice.catalog_priority_id == 1"
                                class="badge rounded-pill border-0 p-3 fs-4 bg-gradient-success">
                                <i class="bi bi-inbox text-white"></i>
                            </span>
                            <span  
                                v-if="notice.catalog_priority_id == 2"
                                class="badge rounded-pill border-0 p-3 fs-4 bg-gradient-warning">
                                <i class="bi bi-inbox-fill text-white"></i>
                            </span>
                            <span  
                                v-if="notice.catalog_priority_id == 3"
                                class="badge rounded-pill border-0 p-3 fs-4 bg-gradient-danger">
                                <i class="bi bi-inbox-fill text-white"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <span class="fw-semibold text-primary fs-6">{{notice.title}} </span>
                                <span class="badge text-secondary text-xxs ms-1">Hace {{notice.create_date.timeSince()}}</span>
                            </div>
                            <div class="fw-light" v-html="notice.description"></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Modal -->
        <div class="modal fade" ref="viewerModal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div
                v-for="(pop, index) in pops" 
                class="modal-dialog modal-dialog-centered"
                :class="[(pop.view ? '' : 'd-none'), (pop.modal_class ? pop.modal_class : '')]">
                <div class="modal-content border-radius shadow-xl border-0">
                    <div class="modal-header border-0">
                        <div class="row align-items-center w-100">
                            <div class="col h4 text-center fw-sembold">
                                {{pop.title}}
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn-close bg-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div v-html="pop.description"></div>
                    </div>
                    <div class="modal-footer border-0" v-if="pop.button_action">
                        <button class="btn btn-primary" @click="nextPop(index)">
                            <span v-if="index < pops.length-1">
                                Siguiente
                            </span>
                            <span v-else>
                                Cerrar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { NoticeViewer } 