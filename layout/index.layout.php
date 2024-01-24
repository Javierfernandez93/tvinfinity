<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>{{title}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Infinity | Marketing profesional" name="author" />
    <meta content="Herramientas de Marketing profesional" name="description" />

    <meta name="theme-color" content="#2D2250">

    <meta name="keywords" content="marketing digital, marketing profesional, herramientas, potenciar negocio, realidad, whatsappsender, whatssender, que es marketing, herramienta marketing digital mas usada">
    <meta name="robots" value="index, follow">

    <meta name="googlebot" content="index, follow">
    <meta name="googlebot-news" content="index, follow">

    <meta property="og:site_name" content="Infinity | Marketing profesional">
    <meta property="og:title" content="Infinity | Marketing profesional" />
    <meta property="og:description" content="Herramientas de Marketing profesional" />
    <meta property="og:image" itemprop="image" content="https://www.Infinity.site/src/img/logo.png">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="300">
    <meta property="og:image:height" content="300">

    <meta property="og:type" content="website" />
    <meta property="og:updated_time" content="1664070388" />
    <meta property="og:url" content="http://www.Infinity.site">


    <!-- App favicon -->
    <link rel="icon" type="image/png" href="../../src/img/favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link rel="stylesheet" href="../../src/css/nucleo-icons.css" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- Font Awesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../src/css/general.css" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />

    <!-- plugin css -->
    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.css?af=1" rel="stylesheet" />
</head>

<body class="bg-dark">
    <nav class="navbar navbar-expand-lg shadow-none navbar-dark bg-transparent position-fixed w-100 z-index-3">
        <div class="container">
            <button class="navbar-toggler border fs-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="#">
                <img src="../../src/img/logo-horizontal-white.svg" alt="Logo" class="w-50">
            </a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fs-5 active fw-sembold" aria-current="page" href="../../apps/home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-sembold" aria-current="page" href="#services">Nuestros servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 fw-sembold" href="#target">¿Para quién es?</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active text-white fw-sembold btn btn-dark fs-5 px-3 shadow-none me-0 me-xl-2 mb-3 mb-xl-0" aria-current="page" href="../../apps/signup">Regístrate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-sembold  btn btn-outline-dark fs-5 px-3 shadow-none mb-0" href="../../apps/login">Ingresa a tu cuenta</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{content}}

    <!--   Core JS Files   -->
    <script src="../../src/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../src/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../../src/js/plugins/chartjs.min.js"></script>
    <script src="../../src/js/42d5adcbca.js" type="text/javascript"></script>

    <script src="../../src/js/constants.js?v=2.1.9" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.js?v=2.1.9" type="text/javascript"></script>
    <script src="../../src/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?m=2" type="text/javascript"></script>

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

    <script async defer src="../../src/js/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../../src/js/soft-ui-dashboard.min.js?v=2.1.9"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="../../src/js/vue.js"></script>

    {{js_scripts}}
    {{css_scripts}}
</body>

</html>