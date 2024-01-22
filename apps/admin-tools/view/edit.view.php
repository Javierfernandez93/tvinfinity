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
                            <div class="small">Herramientas</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Título</label>
                            <input 
                                :autofocus="true"
                                :class="tool.title ? 'is-valid' : ''"
                                @keydown.enter.exact.prevent="$refs.description.focus()"
                                v-model="tool.title"
                                ref="title"
                                type="text" class="form-control" placeholder="Título">
                        </div>
                        <div class="col-4">
                            <label>Archivo</label>
                            <div>
                                <button @click="openFileManager" class="btn btn-primary">
                                    <span v-if="tool.source">
                                        Cambiar archivo
                                    </span>
                                    <span v-else>
                                        Subir archivo
                                    </span>
                                </button>
                            </div>

                            <input class="upload-file d-none" ref="file" @change="uploadToolFile($refs.uploadFile).then((res)=>{ })" capture="filesystem" type="file" accept=".rar, .zip, .txt, .pdf, .pptx, .doc, .docx, .png, .jpg, .jpeg, .gif" />
                        </div>
                        <div class="col-4">
                            <label>Tipo de herramienta</label>

                            <select 
                                :class="tool.catalog_tool_id ? 'is-valid' : ''"
                                class="form-select" v-model="tool.catalog_tool_id" aria-label="Selecciona tua opción">
                                <option>Selecciona una categoría</option>
                                <option v-for="catalog_tool in catalog_tools" v-bind:value="catalog_tool.catalog_tool_id">
                                    {{ catalog_tool.tool }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>
                        
                        <div id="editor">
                        </div>
                    </div>


                    <button 
                        :disabled="!toolComplete"
                        ref="button"
                        class="btn btn-primary" @click="updateTool">Actualizar herramienta</button>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-xl">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto">
                            <span class="badge bg-primary"><i class="bi bi-tools"></i></span>
                        </div>
                        <div class="col fw-semibold">
                            {{tool.title}}
                        </div>
                    </div>
                </div>
                <div v-if="tool.route" class="text-center"> 
                    <div v-if="tool.route.isImage()">
                        <img :src="tool.route" class="img-fluid"> 
                    </div>
                    <div v-else-if="tool.route.isFile()">
                        <i class="bi bi-cloud fs-1"></i>
                    </div>
                </div>

                <div v-if="tool.description" class="card-footer">
                    <div v-html="tool.description">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>