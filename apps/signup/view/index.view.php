<div class="row d-flex justify-content-center align-items-center vh-100" id="app">
    <div class="mask bg-dark"></div>
    <div class="col-12 col-xl-3">
        <div class="row justify-content-center">
            <div class="col-12">
                <div
                    v-if="user.referral.user_login_id"
                    class="card blur shadow-blur overflow-hidden mb-3 bg-gradient-warning shadow-none">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-xl overflow-hidden img-upload position-relative">
                                    <img class="w-100 border-radius-lg shadow-sm" :src="user.referral.image"/>
                                </div>
                            </div>
                            <div class="col">
                                <span class="badge text-white p-0">Referido por:</span>
                                <div>
                                    <div class="fw-semibold text-white fs-5">{{user.referral.names}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card text-start shadow p-3">
                    <div class="">
                        <div class="row justify-content-center">
                            <div class="col-4 col-xl-6">
                                <img src="../../src/img/logo-horizontal-dark-letters.svg" class="img-fluid"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-header pb-0 text-left bg-transparent text-center">
                        <h3 class="font-weight-bolder">¡Bienvenido!</h3>
                    </div>
                    <div class="card-body">
                        <label>Nombre</label>
                        <div class="mb-3">
                            <input 
                                :class="user.names ? 'is-valid' : ''"
                                :autofocus="true" type="text" ref="names" v-model="user.names" class="form-control" @keydown.enter.exact.prevent="$refs.phone.focus()" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1">
                        </div>

                        <label>Teléfono</label>
                        <div class="row">
                            <div class="col">
                                <select class="form-select" v-model="user.country_id" aria-label="Selecciona tu país">
                                    <option>Selecciona tu país</option>
                                    <option v-for="country in countries" v-bind:value="country.country_id">
                                        {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                                    </option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <div class="mb-3">
                                    <input 
                                        :class="user.phone ? 'is-valid' : ''"
                                        type="text" ref="phone" v-model="user.phone" class="form-control" @keydown.enter.exact.prevent="$refs.email.focus()" placeholder="Teléfono" aria-label="Teléfono" aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>
                        
                        <label>Correo electrónico</label>
						<div class="mb-3">
                            <input 
                                :class="isValidMail ? 'is-valid' : ''"
                                type="email" ref="email" v-model="user.email" class="form-control" @keydown.enter.exact.prevent="$refs.password.focus()" placeholder="Correo electrónico" aria-label="Correo electrónico" aria-describedby="basic-addon1">
                        </div>

                        <label>Contraseña</label>
                        <div class="input-group">
                            <input 
                                :class="user.password ? 'is-valid' : ''"
                                :type="fieldPasswordType" 
                                ref="password" 
                                @keydown.enter.exact.prevent="$refs.passwordAgain.focus()" 
                                v-model="user.password" 
                                style="height:41px;" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1">
                            <button class="btn btn-secondary" type="button" id="button-addon2" @click="toggleFieldPasswordType">
                                <i v-if="fieldPasswordType == 'password'" class="bi bi-eye"></i>
                                <i v-else class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        
                        <label>Contraseña de nuevo</label>
                        <div class="mb-3">
                            <div class="input-group">
                                <input 
                                    :class="user.password != null && user.password == user.passwordAgain ? 'is-valid' : 'is-invalid'"
                                    :type="fieldPasswordType" 
                                    ref="passwordAgain" 
                                    @keydown.enter.exact.prevent="doSignup" 
                                    v-model="user.passwordAgain" 
                                    style="height:41px;" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1">
                                <button class="btn btn-secondary" type="button" id="button-addon2" @click="toggleFieldPasswordType">
                                    <i v-if="fieldPasswordType == 'password'" class="bi bi-eye"></i>
                                    <i v-else class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <small v-if="passwordFeedback != null" class="form-text text-muted" v-html="passwordFeedback">
                            </small>
                        </div>

                        <div v-show="feedback" class="alert alert-light shadow fw-semibold alert-dismissible fade show" role="alert">
                            <div><strong>Aviso</strong></div>
                            {{ feedback }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
    
                        <button :disabled="!userComplete || loading" class="btn bg-danger text-white shadow-none btn-lg w-100 mt-4 mb-0" @click="doSignup" id="button">
                            <span v-if="!loading">
                                Crear mi cuenta
                            </span>
                            <span v-else>
                                <div class="spinner-border" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </span>
                        </button>

                    </div>    
                    <div class="card-footer text-center pt-0 px-lg-2 px-1">
                        <p class="mb-4 text-sm mx-auto">
                            ¿Ya tienes una cuenta?
                            <a href="../../apps/login" class="text-warning text-gradient font-weight-bold">Ingresa aquí</a>
                        </p>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>