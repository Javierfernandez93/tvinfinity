<div class="row justify-content-center">
  <div class="col-12 col-lg-10 col-xl-8">
  	<div class="header">
	  <!-- Body -->
	  <div class="header-body">
	    <div class="row align-items-end">
	      <div class="col">

	        <!-- Pretitle -->
	        <h6 class="header-pretitle">
	          <t>Mi negocio</t>
	        </h6>

	        <!-- Title -->
	        <h1 class="text-xdark header-title">
	          <t>Proyectos</t></n>
	        </h1>

	      </div>
	      <div class="col-auto">

	      </div>
	    </div> <!-- / .row -->
	  </div> <!-- / .header-body -->
	</div>

	<?php // if(!$UserLogin->getVerifiedMail($user_login) != 0){ ?>
	  <div class="alert alert-primary alert-dimmisible" role="alert">
	    <t><b>¡Advertencia!</b></t> <t>Tu correo no ha sido verificado. Por seguridad verifica tu correo dando</t> <a href="../../apps/verify-mail" class="text-white" target="_blank"><t>clic aquí</t></a>.
	  </div>
	<?php // }?>

  	<?php if($proyects = $Proyect->getAll(1)) { ?>
  		<?php foreach($proyects as $proyect): ?>
		  	<div class="card">
		      <div class="card-body">
		        <div class="row align-items-center">
		          <div class="col-auto">

		            <!-- Avatar -->
		            <a href="../../apps/proyects/view.php?pid=<?php echo $proyect['proyect_id'];?>" class="avatar avatar-lg avatar-4by3">
		              <img src="https://dashkit.goodthemes.co/assets/img/avatars/projects/project-1.jpg" alt="..." class="avatar-img rounded">
		            </a>

		          </div>
		          <div class="col ml-n2">

		            <!-- Title -->
		            <h4 class="mb-1">
		              <a href="../../apps/proyects/view.php?pid=<?php echo $proyect['proyect_id'];?>"><?php echo $proyect['proyect']?></a>
		            </h4>

		            <!-- Text -->
		            <p class="small text-muted mb-1">
		              <time datetime="2018-06-21">Updated 2hr ago</time>
		            </p>

		            <!-- Progress -->
		            <div class="row align-items-center no-gutters">
		              <div class="col-auto">

		                <!-- Value -->
		                <div class="small mr-2">29%</div>

		              </div>
		              <div class="col">

		                <!-- Progress -->
		                <div class="progress progress-sm">
		                  <div class="progress-bar" role="progressbar" style="width: 29%" aria-valuenow="29" aria-valuemin="0" aria-valuemax="100"></div>
		                </div>

		              </div>
		            </div> <!-- / .row -->

		          </div>
		          <div class="col-auto">

		            <!-- Avatar group -->
		            <div class="avatar-group d-none d-md-inline-flex">
		              <a href="profile-posts.html" class="avatar avatar-xs" data-toggle="tooltip" title="" data-original-title="Ab Hadley">
		                <img src="https://dashkit.goodthemes.co/assets/img/avatars/profiles/avatar-5.jpg" class="avatar-img rounded-circle" alt="...">
		              </a>
		              <a href="profile-posts.html" class="avatar avatar-xs" data-toggle="tooltip" title="" data-original-title="Adolfo Hess">
		                <img src="https://dashkit.goodthemes.co/assets/img/avatars/profiles/avatar-5.jpg" class="avatar-img rounded-circle" alt="...">
		              </a>
		              <a href="profile-posts.html" class="avatar avatar-xs" data-toggle="tooltip" title="" data-original-title="Daniela Dewitt">
		                <img src="https://dashkit.goodthemes.co/assets/img/avatars/profiles/avatar-5.jpg" class="avatar-img rounded-circle" alt="...">
		              </a>
		              <a href="profile-posts.html" class="avatar avatar-xs" data-toggle="tooltip" title="" data-original-title="Miyah Myles">
		                <img src="https://dashkit.goodthemes.co/assets/img/avatars/profiles/avatar-5.jpg" class="avatar-img rounded-circle" alt="...">
		              </a>
		            </div>

		          </div>
		          <div class="col-auto">

		            <!-- Dropdown -->
		            <div class="dropdown">
		              <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                <i class="fe fe-more-vertical"></i>
		              </a>
		              <div class="dropdown-menu dropdown-menu-right">
		                <a href="#!" class="dropdown-item">
		                  Action
		                </a>
		                <a href="#!" class="dropdown-item">
		                  Another action
		                </a>
		                <a href="#!" class="dropdown-item">
		                  Something else here
		                </a>
		              </div>
		            </div>

		          </div>
		        </div> <!-- / .row -->
		      </div> <!-- / .card-body -->
		    </div>		
		<?php endforeach ?>
	<?php } ?>
  </div>
</div>

