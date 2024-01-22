<div class="container-fluid py-4" id="app">
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <div class="col fw-semibold text-dark">
                            <div class="small">Noticias</div>
                        </div>
                        <div class="col-auto text-muted text-sm">
                            <div v-if="notice.limit_dates">
                                Será visible del
                                <span class="badge bg-secondary">{{notice.start_date}}</span>
                                al
                                <span class="badge bg-secondary">{{notice.end_date}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Título</label>
                            <input :autofocus="true" :class="notice.title ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.description.focus()" v-model="notice.title" ref="title" type="text" class="form-control" placeholder="Título">
                        </div>
                        <div class="col-4">
                            <label>Tipo de noticia</label>

                            <select :class="notice.catalog_notice_id ? 'is-valid' : ''" class="form-select" v-model="notice.catalog_notice_id" aria-label="Selecciona tu país">
                                <option>Selecciona una categoría</option>
                                <option v-for="catalog_notice in catalog_notices" v-bind:value="catalog_notice.catalog_notice_id">
                                    {{ catalog_notice.notice }}
                                </option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label>Prioridad</label>

                            <select :class="notice.catalog_priority_id ? 'is-valid' : ''" class="form-select" v-model="notice.catalog_priority_id" aria-label="Selecciona tu país">
                                <option>Selecciona una categoría</option>
                                <option v-for="catalog_priority in catalog_priorities" v-bind:value="catalog_priority.catalog_priority_id">
                                    {{ catalog_priority.priority }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>

                        <div id="editor">
                            <div v-html="notice.description">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input v-model="notice.limit_dates" class="form-check-input" type="checkbox" id="limit_dates">
                            <label class="form-check-label" for="limit_dates">Limitar fechas</label>
                        </div>
                        <div v-if="notice.limit_dates">
                            <div class="row">
                                <div class="col">
                                    <input v-model="notice.start_date" class="form-control" type="date">
                                </div>
                                <div class="col">
                                    <input v-model="notice.end_date" class="form-control" type="date">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button :disabled="!noticeComplete" ref="button" class="btn btn-primary" @click="updateNotice">Actualizar</button>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="col fw-semibold text-dark">
                            <div class="small">Previsualización</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-if="notice.catalog_notice_id == 2">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-zoom py-3 cursor-pointer">
                                <div class="row align-items-center">
                                    <div class="col-auto h5">
                                        <span v-if="notice.catalog_priority_id == 1" class="item-rounded bg-gradient-success">
                                            <i class="bi bi-inbox text-white"></i>
                                        </span>
                                        <span v-if="notice.catalog_priority_id == 2" class="item-rounded bg-gradient-warning">
                                            <i class="bi bi-inbox-fill text-white"></i>
                                        </span>
                                        <span v-if="notice.catalog_priority_id == 3" class="item-rounded bg-gradient-danger">
                                            <i class="bi bi-inbox-fill text-white"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-semibold text-dark">{{notice.title}}</div>
                                        <div class="text-sm text-secondary" v-html="notice.description"></div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-xxs text-secondary">Justo ahora</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else-if="notice.catalog_notice_id == 1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalToggleLabel2">{{notice.title}}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div v-html="notice.description">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Finalizar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>