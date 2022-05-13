<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="<?php buildResourceURI('assets/cpanel/img/favicon.ico'); ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php buildResourceURI('assets/cpanel/img/favicon.ico'); ?>" type="image/x-icon"/>
    <link href="<?php buildResourceURI('assets/cpanel/css/app.css'); ?>" rel="stylesheet"/>
    <title><?php _e($this->pageTitle); ?></title>
</head>
<body class="antialiased theme-dark">
<div class="page">
    <!-- START MAIN CONTENT -->
    <div class="content">
        <div class="container-xl">
            <div class="row mt-5">
                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php _e($this->pageTitle); ?></h3>
                            <a href="<?php buildURIPath('cplogin'); ?>" class="navbar-brand text-right w-100 justify-content-end">
                                <img src="<?php buildResourceURI('assets/cpanel/img/logo-white.png'); ?>" alt="cloud monster logo" class="navbar-brand-image">
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
                            <form action="<?php postReq(); ?>" method="post" >
                                <div class="form-group mb-3 ">
                                    <div>
                                        <div class="input-icon mb-3">
                                       <span class="input-icon-addon">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                             <path stroke="none" d="M0 0h24v24H0z"></path>
                                             <circle cx="12" cy="7" r="4"></circle>
                                             <path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path>
                                          </svg>
                                       </span>
                                            <input type="text" name="username" class="form-control" placeholder="Username">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 ">
                                    <div>
                                        <div class="input-icon mb-3">
                                       <span class="input-icon-addon">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="icon " width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                             <path stroke="none" d="M0 0h24v24H0z"></path>
                                             <circle cx="8" cy="15" r="4"></circle>
                                             <line x1="10.85" y1="12.15" x2="19" y2="4"></line>
                                             <line x1="18" y1="5" x2="20" y2="7"></line>
                                             <line x1="15" y1="8" x2="17" y2="10"></line>
                                          </svg>
                                       </span>
                                            <input type="password" name="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                                <label class="form-check">
                                    <input class="form-check-input" name="remember_me" type="checkbox" >
                                    <span class="form-check-label">Remember me</span>
                                </label>
                                <div class="form-footer mt-3 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <line x1="20" y1="12" x2="10" y2="12"></line>
                                            <line x1="20" y1="12" x2="16" y2="16"></line>
                                            <line x1="20" y1="12" x2="16" y2="8"></line>
                                            <line x1="4" y1="4" x2="4" y2="20"></line>
                                        </svg>
                                        &nbsp;Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>