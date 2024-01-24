<div class="row d-flex justify-content-center align-items-center vh-100" id="app">
  <div class="col-11 col-md-4 col-xl-3">
    <div class="card shadow-lg border-radius-2xl">
      <div class="card-header animate__animated animate__bounceInDown">
          <div class="row justify-content-center">
              <div class="col-4 col-xl-6">
                  <img src="../../src/img/logo-horizontal-dark.svg" class="img-fluid"/>
              </div>
          </div>
      </div>
      <div class="card-body">
        <div class="form-floating mb-3">
          <input 
            :autofocus="true"
            :class="isValidMail ? 'is-valid' : ''"
            @keydown.enter.exact.prevent="$refs.password.focus()"
            ref="email"
            v-model="user.email"
            type="email" class="form-control" placeholder="name@example.com">
          <label for="email">Correo electrónico</label>
        </div>

        <div class="form-floating mb-3">
          <input 
            :type="fieldPasswordType"
            :class="user.password ? 'is-valid' : ''"
            @keydown.enter.exact.prevent="doLogin"
            ref="password" 
            v-model="user.password" 
            type="password" class="form-control" placeholder="Password">
          <label for="password">Contraseña</label>
        </div>

        <div v-show="feedback" class="alert alert-secondary text-white alert-dismissible fade show" role="alert">
            {{ feedback }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      
        <button 
          :disabled="!userComplete" 
          @click="doLogin"
          class="btn btn-dark w-100 btn-block btn-lg badge-pill mb-0" type="button">Ingresar Infinity.site</button>
      </div>
    </div>        
  </div>
</div>