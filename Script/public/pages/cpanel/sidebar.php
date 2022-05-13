<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a href="<?php buildURIPath('cpanel'); ?>" class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
            <img src="<?php buildResourceURI('assets/cpanel/img/logo-white.png'); ?>" alt="cloud monster" class="navbar-brand-image">
        </a>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/dashboard'); ?>" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <polyline points="5 12 3 12 12 3 21 12 19 12" />
                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  Dashboard
                  </span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-extra"  data-toggle="dropdown" role="button" aria-expanded="false">
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                        <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  My Buckets
                  </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php buildURIPath('cpanel/buckets/new'); ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add new bucket
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php buildURIPath('cpanel/buckets/list'); ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <path d="M3.5 5.5l1.5 1.5l2.5 -2.5"></path>
                                    <path d="M3.5 11.5l1.5 1.5l2.5 -2.5"></path>
                                    <path d="M3.5 17.5l1.5 1.5l2.5 -2.5"></path>
                                    <line x1="11" y1="6" x2="20" y2="6"></line>
                                    <line x1="11" y1="12" x2="20" y2="12"></line>
                                    <line x1="11" y1="18" x2="20" y2="18"></line>
                                </svg>
                                View bucket list
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-layout" data-toggle="dropdown" role="button" aria-expanded="false" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
                        <path d="M11 13v2m0 3v2m4 -5v2m0 3v2"></path>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  Cloud Drives
                  </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php buildURIPath('cpanel/drives/new'); ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add new drive
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php buildURIPath('cpanel/drives/list'); ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                    <path d="M4 13h3l3 3h4l3 -3h3"></path>
                                </svg>
                                View drives list
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/files/list'); ?>" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-12"></path>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  Cloud Files
                  </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/analytics'); ?>"  >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <polyline points="4 19 8 13 12 15 16 10 20 14 20 19 4 19"></polyline>
                        <polyline points="4 12 7 8 11 10 16 4 20 8"></polyline>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  Analytics
                  </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/tracker'); ?>" target="_blank" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <polyline points="21 12 17 12 14 20 10 4 7 12 3 12"></polyline>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  UP Tracker
                  </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/process'); ?>"  >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M3 9l4-4l4 4m-4 -4v14"></path>
                        <path d="M21 15l-4 4l-4-4m4 4v-14"></path>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  System Process
                  </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php buildURIPath('cpanel/settings'); ?>"  >
                  <span class="nav-link-icon d-md-none d-lg-inline-block mr-1">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                     </svg>
                  </span>
                        <span class="nav-link-title">
                  Settings
                  </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>