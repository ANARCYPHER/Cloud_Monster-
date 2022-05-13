<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?php _e($this->pageTitle); _e(' | ' . CloudMonster\App::getConfig('site_name')); ?></title>


    <link rel="icon" href="<?php buildResourceURI('assets/cpanel/img/favicon.ico'); ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php buildResourceURI('assets/cpanel/img/favicon.ico'); ?>" type="image/x-icon"/>

    <!-- CSS files -->
    <link href="<?php buildResourceURI('assets/cpanel/libs/selectize/dist/css/selectize.css'); ?>" rel="stylesheet"/>
    <link href="<?php buildResourceURI('assets/cpanel/libs/jqvmap/dist/jqvmap.min.css'); ?>" rel="stylesheet"/>
    <link href="<?php buildResourceURI('assets/cpanel/libs/daterangepicker/dist/daterangepicker.css'); ?>" rel="stylesheet"/>
    <link href="<?php buildResourceURI('assets/cpanel/libs/datatable/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet"/>

    <link href="<?php buildResourceURI('assets/cpanel/css/app.min.css'); ?>" rel="stylesheet"/>

    <style>
        body {
            display: none;
        }
    </style>

</head>

<body class="antialiased theme-dark">

<!-- Include sidebar -->
<?php include_once ROOT . '/' . TEMPLATE_DIR . '/pages/cpanel/sidebar.php'; ?>


<div class="page">

    <header class="navbar navbar-expand-md navbar-light">

        <div class="container-xl">

            <div class="navbar-nav flex-row order-md-last">


                <div class="nav-item dropdown d-none d-md-flex mr-3">
                    <a href="<?php buildURIPath('cpanel/tracker'); ?>" target="_blank" class="nav-link px-0"  data-toggle="tooltip" data-placement="bottom" title="View UP Tracker">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon tb-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <polyline points="21 12 17 12 14 20 10 4 7 12 3 12"></polyline>
                        </svg>
                        <?php if(\CloudMonster\Services\CloudUpload::isActive()): ?>
                            <span class="badge bg-success"></span>
                        <?php else: ?>
                            <span class="badge bg-secondary"></span>
                        <?php endif; ?>
                    </a>
                </div>
                <!-- /.nav-item -->

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown">
                        <span class="avatar" style="background-image: url( <?php imgUri('avatars/my-profile.jpg'); ?> )"></span>
                        <div class="d-none d-xl-block pl-2">
                            <div><?php _e(\CloudMonster\App::getConfig('real_monster_name')); ?></div>
                            <div class="mt-1 small text-muted">The Real Monster</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item text-danger font-weight-bold" href="<?php buildURIPath('cpanel/logout'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <line x1="4" y1="12" x2="14" y2="12"></line>
                                <line x1="4" y1="12" x2="8" y2="16"></line>
                                <line x1="4" y1="12" x2="8" y2="8"></line>
                                <line x1="20" y1="4" x2="20" y2="20"></line>
                            </svg>
                            Logout
                        </a>
                        <!-- /.dropdown-item -->
                    </div>
                    <!-- /.dropdown-menu -->
                </div>
                <!-- /.nav-item -->

            </div>
            <!-- /.navbar-nav (right) -->

            <div class="collapse navbar-collapse" id="navbar-menu">

                <div class="position-relative">

                    <form action="/" method="get">
                        <div class="input-icon">
                              <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z"></path>
                                  <circle cx="10" cy="10" r="7"></circle>
                                  <line x1="21" y1="21" x2="15" y2="15"></line>
                                </svg>
                              </span>
                              <input type="text" class="form-control search-input" id="main-search-input" placeholder="Search...">
                        </div>
                        <!-- /. input -->
                    </form>
                    <!-- /. form -->

                    <div class="search-results-wrap" style="display:none;">
                        <ul class="list-group list-group-flush"  >


                        </ul>

                        <div class="mx-3 py-3 no-results-msg" >Results not found</div>

                    </div>
                </div>
            </div>

        </div>
    </header>
    <!-- /.header-->


    <div class="content">

        <div class="container-xl">
