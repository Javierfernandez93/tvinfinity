<div class="row d-flex justify-content-center align-items-center vh-100" id="app">
    <div class="mask bg-dark"></div>
    <div class="col-11 col-xl-3">
        <div v-if="redirection.page" class="card mb-3">
            <div class="card-body">
                Ingresa a tu cuenta para continuar a: 
                <div><b>{{ redirection.route_name }}</b></div>
                <div><b>{{ redirection.page }}</b></div>
            </div>
        </div>

        <div class="card p-3 text-start cards-plain shadow-none bg-white">
            <div class="">
                <div class="row justify-content-center">
                    <div class="col-4 col-xl-6">
                        <img src="../../src/img/logo-horizontal-dark.svg" class="img-fluid"/>
                    </div>
                </div>
            </div>

            <div class="card-header pb-0 text-center bg-transparent">
                <h3 class="font-weight-bolder text-dark text-gradient">¡Bienvenido de nuevo!</h3>
            </div>

            <div class="card-body">
                <form role="form">
                    <label>Correo electrónico</label>
                    <div class="mb-3">
                        <input 
                            :autofocus="true"
                            :class="isValidMail ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.password.focus()"
                            type="email" ref="email" v-model="user.email" class="form-control" placeholder="Corrreo electrónico" aria-label="Corrreo electrónico" aria-describedby="email-addon">
                    </div>
                    <label>Contraseña</label>
                    <div class="input-group mb-3">
                        <input 
                            :type="fieldPasswordType"
                            :class="user.password ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="doLogin"
                            style="height:41px"
                            type="password" ref="password" v-model="user.password" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="password-addon">
                        <button class="btn btn-secondary" type="button" id="button-addon2" @click="toggleFieldPasswordType">
                            <i v-if="fieldPasswordType == 'password'" class="bi bi-eye"></i>
                            <i v-else class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" v-model="user.rememberMe" id="rememberMe" checked="">
                                <label class="form-check-label" for="rememberMe">Recordarme</label>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <a class="small" href="../../apps/login/forgotPassword">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <div v-show="feedback" class="alert alert-light shadow fw-semibold border-0 alert-dismissible fade show" role="alert">
                        <strong>Aviso</strong>
                        {{ feedback }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="text-center mt-3 d-grid">
                        <button
                            :disabled="!userComplete" 
                            @click="doLogin"
                            type="button" class="btn bg-danger text-white shadow-none btn-lg">Ingresar a mi cuenta</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                <p class="mb-4 text-sm mx-auto">
                    ¿No tienes una cuenta?
                    <a href="../../apps/signup" class="text-info text-warning font-weight-bold">Regístrate aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>