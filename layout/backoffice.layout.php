<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../src/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../src/img/favicon.png">
    <title>
        {{title}}
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link rel="stylesheet" href="../../src/css/nucleo-icons.css" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;900&display=swap" rel="stylesheet">

    <link href="../../src/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/z-loader.css" />
    <link rel="stylesheet" href="../../src/css/general.css?v=1.5.7" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- CSS Files -->

    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.css?v=1.5.7" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-light">
    <aside class="sidenav navbar navbar-vertical m-2 navbar-expand-xs fixed-start" id="sidenav-main">
        <div class="bg-dark rounded pb-5">
            <div class="sidenav-header">
                <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
                <a class="navbar-brand m-0" href=" https://Infinity.site " target="_blank">
                    <img src="../../src/img/logo-horizontal-white.svg" class="w-100" alt="main_logo">
                </a>
            </div>
            <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
                <ul class="navbar-nav">
                    <li class="nav-item d-none mt-3">
                        <div class="row py-3 px-4">
                            <div class="col-auto">
                                <div class="avatar rounded-circle">
                                    <img src="http://localhost:8888/Infinity/src/img/logo-2.png" class="avatar bg-white rounded-circle ">
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-white"><?php echo $UserLogin->_data['user_data']['names']; ?></div>
                                <div>
                                    <span class="badge bg-primary">Activo</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item mt-5">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs opacity-6 text-white">Menú principal</h6>
                    </li>
                    <?php if ($UserLogin->logged) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if (in_array($route, [JFStudio\Router::Backoffice, JFStudio\Router::Notifications, JFStudio\Router::AddFunds])) { ?>active<?php } ?>" href="../../apps/backoffice">
                                <i class="bi bi-cup-fill"></i>
                                <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::Backoffice); ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (in_array($route, [JFStudio\Router::StoreMarketing])) { ?>active<?php } ?>" href="../../apps/store/package">
                                <i class="bi bi-cart-fill"></i>
                                <span class="nav-link-text ms-1">Activación</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (in_array($route, [JFStudio\Router::StoreMarketing])) { ?>active<?php } ?>" href="../../apps/store/invoices">
                                <i class="bi bi-cart-fill"></i>
                                <span class="nav-link-text ms-1">Mis compras</span>
                            </a>
                        </li>
                        <?php if($UserLogin->isActiveOnPackage(1)) { ?>
                            <li class="nav-item d-none">
                                <a class="nav-link <?php if (in_array($route, [JFStudio\Router::Academy,JFStudio\Router::Academy, JFStudio\Router::AcademyLesson])) { ?>active<?php } ?>" href="../../apps/academy">
                                    <i class="bi bi-magic"></i>
                                    <span class="nav-link-text ms-1">Educación</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a data-bs-toggle="collapse" href="#pagesUsers" class="nav-link collapsed <?php if (in_array($route,[JFStudio\Router::Referrals])) { ?>active<?php } ?>" aria-controls="pagesUsers" role="button" aria-expanded="false">
                                    <i class="bi bi-people"></i>
                                    <span class="nav-link-text ms-1">Referidos</span>
                                </a>
                                <div class="collapse" id="pagesUsers">
                                    <ul class="nav ms-4">
                                        <li class="nav-item ">
                                            <a class="nav-link" href="../../apps/referrals">
                                                <span class="sidenav-mini-icon"> F7 </span>
                                                <span class="sidenav-normal"> Mis referidos </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($route == JFStudio\Router::Wallet) { ?>active<?php } ?>" href="../../apps/ewallet/">
                                <i class="bi bi-wallet2"></i>
                                <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::Wallet); ?></span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($UserLogin->isActiveOnPackage(1)) { ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#pagesCredits" class="nav-link collapsed <?php if (in_array($route,[JFStudio\Router::StoreCredit,JFStudio\Router::Iptv,JFStudio\Router::IptvAddClient])) { ?>active<?php } ?>" aria-controls="pagesCredits" role="button" aria-expanded="false">
                            <i class="bi bi-tv"></i>
                            <span class="nav-link-text ms-1">IPTV</span>
                        </a>
                        <div class="collapse" id="pagesCredits">
                            <ul class="nav ms-4">
                                <li class="nav-item ">
                                    <a class="nav-link " href="../../apps/store/credit">
                                        <span class="sidenav-mini-icon"> F7 </span>
                                        <span class="sidenav-normal"> Comprar créditos</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="../../apps/store/creditPackage">
                                        <span class="sidenav-mini-icon"> F7 </span>
                                        <span class="sidenav-normal"> Paquetes de créditos</span>
                                    </a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link " href="../../apps/store/invoices">
                                        <span class="sidenav-mini-icon"> P </span>
                                        <span class="sidenav-normal"> Ver compras </span>
                                    </a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link " href="../../apps/iptv/demo">
                                        <span class="sidenav-mini-icon"> P </span>
                                        <span class="sidenav-normal"> Solicitar Demo IPTV </span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link " href="../../apps/iptv/">
                                        <span class="sidenav-mini-icon"> P </span>
                                        <span class="sidenav-normal"> Mis clientes </span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link " href="../../apps/iptv/add">
                                        <span class="sidenav-mini-icon"> P </span>
                                        <span class="sidenav-normal"> Dar de alta cliente </span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link " href="https://zuum.link/AyudaFunnelMillonario" target="_blank">
                                        <span class="sidenav-mini-icon"> P </span>
                                        <span class="sidenav-normal"> Ayuda configuración </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php } ?>
                    
                    <li class="nav-item d-none">
                        <a data-bs-toggle="collapse" href="#pagesGames" class="nav-link collapsed <?php if (in_array($route,[JFStudio\Router::Game,JFStudio\Router::GameGuide])) { ?>active<?php } ?>" aria-controls="pagesGames" role="button" aria-expanded="false">
                            <i class="bi bi-controller"></i>
                            <span class="nav-link-text ms-1">Juegos</span>
                        </a>
                        <div class="collapse" id="pagesGames">
                            <ul class="nav ms-4">
                                <li class="nav-item ">
                                    <a class="nav-link " href="../../apps/store/game">
                                        <span class="sidenav-mini-icon"> Games </span>
                                        <span class="sidenav-normal"> Comprar juegos</span>
                                    </a>
                                </li>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,6)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/snes">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración SNES </span>
                                        </a>
                                    </li>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/academy/lesson?cid=5">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Tutorial SNES </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,7)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/n64">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración N64 </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,8)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/turbografx">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración Turbografx </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,9)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/gameboy">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración GameBoy </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,10)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/atari">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración Atari </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if((new Infinity\BuyPerUser)->hasPackageBuy($UserLogin->company_id,11)) { ?>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/game/sega">
                                            <span class="sidenav-mini-icon"> P </span>
                                            <span class="sidenav-normal"> Configuración Sega </span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </li>

                    
                    <li class="nav-item d-none">
                        <a class="nav-link <?php if (in_array($route,[JFStudio\Router::ImagesBank])) { ?>active<?php } ?>" href="../../apps/backoffice/images">
                            <i class="bi bi-card-image"></i>
                            <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::ImagesBank); ?></span>
                        </a>
                    </li>

                    <?php if($UserLogin->isActiveOnPackage(1)) { ?>
                        <li class="nav-item d-none">
                            <a class="nav-link <?php if (in_array($route,[JFStudio\Router::Landing])) { ?>active<?php } ?>" href="../../apps/backoffice/landings">
                                <i class="bi bi-card-image"></i>
                                <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::Landing); ?></span>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route,[JFStudio\Router::Help])) { ?>active<?php } ?>" href="../../apps/ticket/">
                            <i class="bi bi-chat-left-heart-fill"></i>
                            <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::Help); ?></span>
                        </a>
                    </li>

                    <?php if($UserLogin->isActiveOnPackage(1)) { ?>
                        <li class="nav-item d-none">
                            <a class="nav-link <?php if (in_array($route,[JFStudio\Router::Movies])) { ?>active<?php } ?>" href="../../apps/movies/">
                                <i class="bi bi-gift"></i>
                                <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::Movies); ?></span>
                            </a>
                        </li>
                    <?php } ?>
                    
                    <?php if ($UserLogin->logged) { ?>
                        <li class="nav-item mt-5">
                            <h6 class="ps-4 ms-2 text-uppercase text-xs opacity-6 text-white">Ajustes de cuenta</h6>
                        </li>
                        
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#profilePages" class="nav-link collapsed <?php if (in_array($route,[JFStudio\Router::Profile])) { ?>active<?php } ?>" aria-controls="profilePages" role="button" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <span class="nav-link-text ms-1">Perfil</span>
                            </a>
                            <div class="collapse" id="profilePages">
                                <ul class="nav ms-4">
                                    <li class="nav-item">
                                        <a class="nav-link " href="../../apps/backoffice/profile">
                                            <i class="bi bi-person-circle"></i>
                                            <span class="sidenav-normal"> <?php echo JFStudio\Router::getName(JFStudio\Router::ProfileSetting); ?> </span>
                                        </a>
                                    </li>
                                    <li class="nav-item ">
                                        <a class="nav-link " href="../../apps/backoffice/?logout=true">
                                            <i class="bi bi-door-closed-fill"></i>
                                            <span class="sidenav-normal"> Cerrar sesión </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                    <?php } ?>
                </ul>
            </div>
            <div class="sidenav-footer d-none mx-3" id="appBannerLeft">
                <bannerleft-viewer></bannerleft-viewer>
            </div>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl <?php if ($floating_nav === true) { ?>bg-transparent position-absolute floating-nav w-100 z-index-2<?php } ?>">
            <div class="container py-3 mb-3">
                <nav aria-label="breadcrumb">
                    <h6 class="h4"><?php echo JFStudio\Router::getName($route); ?></h6>
                </nav>
                <?php if ($UserLogin->logged) { ?>
                    <div class="collapse navbar-collapse me-md-0 me-sm-4 mt-sm-0 mt-2" id="navbar">
                        <div class="ms-md-auto pe-md-3 d-flex align-items-center">

                        </div>
                        <ul class="navbar-nav justify-content-end">
                            <li class="nav-item d-none dropdown px-3 d-flex align-items-center">
                                <a href="../../apps/backoffice/notifications" class="nav-link p-0 fs-3 rounded-circle" id="dropdownMenuButton">
                                    <i class="fa fa-bell cursor-pointer" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li class="nav-item pe-3 align-items-center">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar bg-dark">
                                            <?php echo $UserLogin->getFirsNameLetter();?>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div>
                                            <a class="fw-sembold text-dark" href="../../apps/backoffice"><?php echo $UserLogin->_data['user_data']['names']; ?></a>
                                        </div>
                                        <div><span class="text-xs p-0 text-secondary"><?php echo $UserLogin->email; ?></span></div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item d-xl-none ps-3 pe-0 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link  p-0">
                                </a>
                                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
            </div>
        </nav>
        
        {{content}}

        <footer class="footer fixesd-bottom p-3 row justify-content-center pt-5">
            <div class="col-12 col-xl-11">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © <script>
                                document.write(new Date().getFullYear())
                            </script>,
                            made with <i class="fa fa-heart"></i> by
                            <a href="https://Infinity.site/" class="font-weight-bold" target="_blank"><img src="../../src/img/logo-horizontal-dark.svg" style="width:5rem;"></a>
                            for a better web.
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                                <a href="" class="nav-link text-muted" target="_blank">Infinity</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <!--   Core JS Files   -->
    <script src="../../src/js/plugins/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/smooth-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/chartjs.min.js" type="text/javascript"></script>
    <script src="../../src/js/42d5adcbca.js" type="text/javascript"></script>
    
    <script src="../../src/js/constants.js?v=2.1.9" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.js?v=2.1.9" type="text/javascript"></script>
    <script src="../../src/js/toastCtrl.js?v=2.1.9" type="text/javascript"></script>
    <script src="../../src/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?v=2.1.9" type="text/javascript"></script>
    <!-- Github buttons -->
    
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script src="../../src/js/buttons.js" type="text/javascript"></script>
    <script src="../../src/js/soft-ui-dashboard.min.js?v=2.1.9"></script>
    
    <script src="../../src/js/vue.js?v=2.1.9" type="text/javascript"></script>

    {{js_scripts}}
    {{css_scripts}}
</body>
</html>