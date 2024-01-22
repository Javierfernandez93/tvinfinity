<div class="container-fluid py-4" id="app">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <div class="col fw-semibold text-dark">
                            <div class="small">Noticias</div>
                        </div>
                        <div class="col-auto text-end">
                            <div><a href="../../apps/admin-news/add" type="button" class="btn btn-success btn-sm">Añadir noticia</a></div>
                            <div><span class="badge bg-secondary">Total de noticias {{Object.keys(notices).length}}</span></div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar..." />
                </div>
                <div
                    v-if="Object.keys(notices).length > 0" 
                    class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="align-items-center">
                                    <th @click="sortData(columns.notice_id)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.notice_id">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">ID</u>
                                    </th>
                                    <th 
                                        @click="sortData(columns.title)"
                                        class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.title.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Título</u>
                                    </th>
                                    <th 
                                        @click="sortData(columns.notice)"
                                        class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.notice.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Tipo</u>
                                    </th>
                                    <th 
                                        @click="sortData(columns.create_date)"
                                        class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.create_date">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Fecha</u>
                                    </th>
                                    <th 
                                        @click="sortData(columns.create_date)"
                                        class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.create_date">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Fecha de visualización</u>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="notice in notices">
                                    <td class="align-middle text-center text-sm">
                                        <p class="font-weight-bold mb-0">{{notice.notice_id}}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div>
                                                    <span class="badge bg-gradient-success text-xxs" v-if="notice.status == '1'">Publicada</span>
                                                    <span class="badge bg-gradient-secondary text-xxs" v-else-if="notice.status == '0'">Sin publicar</span>
                                                </div>

                                                <h6 class="mb-0 text-sm">{{notice.title}}</h6>
                                                <h6 class="mb-0 text-xs text-secondary">Por {{notice.names}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span 
                                            :class="notice.catalog_notice_id == 1 ? 'bg-primary' : 'bg-success'"
                                            class="badge badge-sm ">
                                            {{notice.notice}}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs text-dark mb-0">{{notice.create_date.formatDate()}}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <div v-if="notice.start_date && notice.end_date">
                                            <span class="badge bg-primary">{{notice.start_date.formatDate()}}</span>
                                            al
                                            <span class="badge bg-primary">{{notice.end_date.formatDate()}}</span>
                                        </div>
                                        <div v-else>
                                            N/A
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                <?php if($UserSupport->hasPermission('edit_notice')) { ?>
                                                    <li><button class="dropdown-item" @click="goToEdit(notice.notice_id)">Editar</button></li>
                                                <?php } ?>
                                                <?php if($UserSupport->hasPermission('publish_notice')) { ?>
                                                    <li v-if="notice.status == '0'"><button class="dropdown-item" @click="publishNotice(notice.notice_id)">Publicar noticia</button></li>
                                                <?php } ?>
                                                <?php if($UserSupport->hasPermission('unpublish_notice')) { ?>
                                                    <li v-if="notice.status == '1'"><button class="dropdown-item" @click="unpublishNotice(notice.notice_id)">Despublicar noticia</button></li>
                                                <?php } ?>
                                                <?php if($UserSupport->hasPermission('delete_notice')) { ?>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><button class="dropdown-item" @click="deleteNotice(notice.notice_id)">Eliminar</button></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else
                    class="card-body">
                    <div class="alert alert-secondary text-white text-center">
                        <div>No tenemos noticias aún</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>