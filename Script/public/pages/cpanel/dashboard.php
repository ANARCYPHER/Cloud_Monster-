<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                Overview
            </div>
            <h2 class="page-title">
                Dashboard
            </h2>
        </div>
        <!-- /.col -->
        <!-- Page title actions -->
        <div class="col-auto ml-auto d-print-none mt-3 mt-md-0">
         <span class="">
         <a href="<?php use CloudMonster\Helpers\Help;
         buildURIPath('cpanel/buckets/list'); ?>" class="btn btn-secondary">
         Buckets List
         </a>
         </span>
            <a href="<?php buildURIPath('cpanel/buckets/new'); ?>" class="btn btn-primary ml-3 " target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                    <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
                Create Bucket
            </a>
        </div>
        <!-- /.col -->
    </div>
    <!-- ./row -->
</div>
<!--/.page-header -->
<div class="row">
    <div class="col-lg-9">
        <div class="row row-cards row-deck">
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-primary text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                                <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                                <line x1="10" y1="12" x2="14" y2="12"></line>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Total Buckets</div>
                        <h3 class="h2 mt-2 mb-3" ><?php _e($analyticsData['buckets']['total']['count']); ?></h3>
                        <p class="mb-0 text-muted">
                     <span class="text-green d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['buckets']['total']['count']); ?>
                     </span>
                            <span class="text-nowrap">Today</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-success text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                                <path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0" >Total Visits</div>
                        <h3 class="h2 mt-2 mb-3" ><?php _e($analyticsData['visits']['total']['count']); ?></h3>
                        <p class="mb-0 text-muted">
                     <span class="text-green d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['visits']['total']['today']); ?>
                     </span>
                            <span class="text-nowrap">Today</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-danger text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon " width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M9 9v-1a3 3 0 0 1 6 0v1"></path>
                                <path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3"></path>
                                <line x1="3" y1="13" x2="7" y2="13"></line>
                                <line x1="17" y1="13" x2="21" y2="13"></line>
                                <line x1="12" y1="20" x2="12" y2="14"></line>
                                <line x1="4" y1="19" x2="7.35" y2="17"></line>
                                <line x1="20" y1="19" x2="16.65" y2="17"></line>
                                <line x1="4" y1="7" x2="7.75" y2="9.4"></line>
                                <line x1="20" y1="7" x2="16.25" y2="9.4"></line>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Broken Buckets</div>
                        <h3 class="h2 mt-2 mb-3" > <?php _e($analyticsData['buckets']['broken']); ?></h3>
                        <p class="mb-0 text-muted" >
                     <span class="text-red d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['buckets']['errorRate']); ?> %
                         </svg>
                     </span>
                            <span class="text-nowrap">Error rate</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-purple text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M5 4h14a1 1 0 0 1 1 1v5a1 1 0 0 1 -1 1h-7a1 1 0 0 0 -1 1v7a1 1 0 0 1 -1 1h-5a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1"></path>
                                <line x1="4" y1="8" x2="6" y2="8"></line>
                                <line x1="4" y1="12" x2="7" y2="12"></line>
                                <line x1="4" y1="16" x2="6" y2="16"></line>
                                <line x1="8" y1="4" x2="8" y2="6"></line>
                                <polyline points="12 4 12 7 "></polyline>
                                <polyline points="16 4 16 6 "></polyline>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Buckets Size</div>
                        <h3 class="h2 mt-2 mb-3"><?php _e(Help::formatSizeUnits($analyticsData['buckets']['total']['size'])); ?></h3>
                        <p class="mb-0 text-muted">
                     <span class="text-green d-inline-flex align-items-center lh-1">
                     <?php _e(Help::formatSizeUnits($analyticsData['buckets']['today']['size'])); ?>
                     </span>
                            <span class="text-nowrap">Today</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-cyan text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <path d="M2 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path>
                                <path d="M16 11l2 2l4 -4"></path>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Unique Visits</div>
                        <h3 class="h2 mt-2 mb-3" ><?php _e($analyticsData['visits']['unique']['count']); ?></h3>
                        <p class="mb-0 text-muted">
                     <span class="text-green d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['visits']['unique']['today']); ?>
                     </span>
                            <span class="text-nowrap">Today</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-sm-6 col-lg-4">
                <div class="card" data-color="green">
                    <div class="card-body">
                        <div class="float-right stamp bg-secondary text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-12"></path>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Cloud Drives</div>
                        <h3 class="h2 mt-2 mb-3"><?php _e($analyticsData['drives']['total']); ?></h3>
                        <p class="mb-0 text-muted d-inline-block mr-3">
                     <span class="text-green d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['drives']['active']); ?>
                     </span>
                            <span class="text-nowrap">Active</span>
                        </p>
                        <p class="mb-0 text-muted d-inline-block">
                     <span class="text-red d-inline-flex align-items-center lh-1">
                     <?php _e($analyticsData['drives']['error']); ?>
                     </span>
                            <span class="text-nowrap">Error</span>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-sm-6 -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Live Visits :: <span class="num-of-live-visits text-muted">0</span> </h3>
                        <div class="ml-auto">
                            <div class="p-loader mr-3">
                                Live Updating
                                <div class="bounce ml-3 d-inline-block">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div id="chart-live-visits" ></div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-12 -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Visits summary</h3>
                        <div class="ml-auto">
                            <div class="input-icon">
                        <span class="input-icon-addon">
                           <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z"></path>
                              <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                              <line x1="16" y1="3" x2="16" y2="7"></line>
                              <line x1="8" y1="3" x2="8" y2="7"></line>
                              <line x1="4" y1="11" x2="20" y2="11"></line>
                              <rect x="8" y="15" width="2" height="2"></rect>
                           </svg>
                        </span>
                                <input type="text" class="form-control form-control-sm" id="visitsMonthlyDatePicker" name="" value="" />
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div id="chart-visits-my-monthly"  class="chart-lg" ></div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-12 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.col-lg-9 -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="float-right stamp bg-secondary text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-12"></path>
                    </svg>
                </div>
                <div class="text-muted font-weight-normal mt-0">Total Cloud Files</div>
                <h3 class="h2 mt-2 mb-3" > <?php _e($analyticsData['files']['total']); ?></h3>
                <p class="mb-0 text-muted">
               <span class="text-green d-inline-flex align-items-center lh-1">
               <?php _e($analyticsData['files']['today']); ?>
               </span>
                    <span class="text-nowrap">Files Uploaded Today</span>
                </p>
                <div class="hr-text mb-3">Total Files Size</div>
                <div class="text-center mb-3">
                    <div class="h3 m-0"> <?php _e(Help::formatSizeUnits($analyticsData['files']['totalSize'])); ?></div>
                </div>
                <div class="d-flex align-items-center mt-2">
                    <span class="text-muted">Upload process : </span>
                    <span class="ml-auto text-success font-weight-bold">Running</span>
                </div>
                <div class="card card-sm mb-0">
                    <div class="card-body px-0">
                        <div class="mb-3 d-flex align-items-center ">
                     <span class="bg-primary text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                           <polyline points="9 15 12 12 15 15"></polyline>
                           <line x1="12" y1="12" x2="12" y2="21"></line>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    <?php _e($analyticsData['files']['summary']['processing']); ?> Uploading
                                </div>
                                <a href="<?php buildURIPath('cpanel/files/list?status=process'); ?>" class="text-muted text-decoration-underline" target="_blank">view files</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center ">
                     <span class="bg-success text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <path d="M3 12h1M12 3v1M20 12h1M5.6 5.6l.7 .7M18.4 5.6l-.7 .7"></path>
                           <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3"></path>
                           <line x1="9.7" y1="17" x2="14.3" y2="17"></line>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    <?php _e($analyticsData['files']['summary']['active']); ?> Active file/s
                                </div>
                                <a href="<?php buildURIPath('cpanel/files/list?status=active'); ?>" class="text-muted text-decoration-underline" target="_blank">view files</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center  ">
                     <span class="bg-secondary text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <path d="M12 9v2m0 4v.01"></path>
                           <path d="M5.07 19H19a2 2 0 0 0 1.75 -2.75L13.75 4a2 2 0 0 0 -3.5 0L3.25 16.25a2 2 0 0 0 1.75 2.75"></path>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    <?php _e($analyticsData['files']['summary']['waiting']); ?> Waiting file/s
                                </div>
                                <a href="<?php buildURIPath('cpanel/files/list?status=waiting'); ?>" class="text-muted text-decoration-underline" target="_blank">view files</a>
                            </div>
                        </div>
                        <div class=" d-flex align-items-center  ">
                     <span class="bg-danger text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <path d="M9 9v-1a3 3 0 0 1 6 0v1"></path>
                           <path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3"></path>
                           <line x1="3" y1="13" x2="7" y2="13"></line>
                           <line x1="17" y1="13" x2="21" y2="13"></line>
                           <line x1="12" y1="20" x2="12" y2="14"></line>
                           <line x1="4" y1="19" x2="7.35" y2="17"></line>
                           <line x1="20" y1="19" x2="16.65" y2="17"></line>
                           <line x1="4" y1="7" x2="7.75" y2="9.4"></line>
                           <line x1="20" y1="7" x2="16.25" y2="9.4"></line>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    <?php _e($analyticsData['files']['summary']['failed']); ?> Broken file/s
                                </div>
                                <a href="<?php buildURIPath('cpanel/files/list?status=failed'); ?>" class="text-muted text-decoration-underline" target="_blank">view files</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Local Storage Usage</div>
                    <div class="ml-auto lh-1">
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="h1 mb-0">
                        <?php _e(Help::formatSizeUnits($analyticsData['storage']['total'])); ?>
                    </div>
                    <div class="ml-auto lh-1"></div>
                </div>
                <div class="card card-sm mb-0">
                    <div class="card-body px-0">
                        <div class="mb-3 d-flex align-items-center ">
                     <span class="bg-secondary text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <polyline points="12 4 4 8 12 12 20 8 12 4"></polyline>
                           <polyline points="4 12 12 16 20 12"></polyline>
                           <polyline points="4 16 12 20 20 16"></polyline>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    Cache  ( <i>  <?php _e(Help::formatSizeUnits($analyticsData['storage']['cache'])); ?></i> )
                                </div>
                                <a href="javascript:void(0)" class="text-muted text-decoration-underline clear" data-type="cache">clear cache</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center ">
                     <span class="bg-secondary text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <line x1="13" y1="20" x2="20" y2="13"></line>
                           <path d="M13 20v-6a1 1 0 0 1 1 -1h6v-7a2 2 0 0 0 -2 -2h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7"></path>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    Tmp Files ( <i>  <?php _e(Help::formatSizeUnits($analyticsData['storage']['tmp'])); ?></i> )
                                </div>
                                <a href="javascript:void(0)" class="text-muted text-decoration-underline clear" data-type="tmp">clear tmp files</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Blocked Requests</div>
                    <div class="ml-auto lh-1">
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="h1 mb-0" > <?php echo number_format($analyticsData['blacklisted']['blockedRequests']); ?> </div>
                    <div class="ml-auto lh-1"></div>
                </div>
                <div class="card card-sm mb-0">
                    <div class="card-body px-0">
                        <div class="mb-3 d-flex align-items-center ">
                     <span class="bg-secondary text-white stamp mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                           <path stroke="none" d="M0 0h24v24H0z"></path>
                           <circle cx="12" cy="5" r="2"></circle>
                           <path d="M10 22v-5l-1-1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5"></path>
                        </svg>
                     </span>
                            <div class="mr-3 lh-sm">
                                <div class="strong">
                                    blacklisted Ip/s  ( <?php echo number_format($analyticsData['blacklisted']['ips']); ?> )
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 d-flex align-items-center ">
                  <span class="bg-secondary text-white stamp mr-3">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon " width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <circle cx="12" cy="11" r="3"></circle>
                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"></path>
                     </svg>
                  </span>
                        <div class="mr-3 lh-sm">
                            <div class="strong">
                                blacklisted Countries  ( <?php echo number_format($analyticsData['blacklisted']['countries']); ?> )
                            </div>
                            <a href="<?php buildURIPath('cpanel/settings'); ?>" class="text-muted text-decoration-underline">view settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(!empty($analyticsData['files']['mostVisited'])): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Most Visited Files</h4>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                    <tr>
                        <th>Filename</th>
                        <th>File code</th>
                        <th>CreatedAt</th>
                        <th>Visitors</th>
                        <th>Unique</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($analyticsData['files']['mostVisited'] as $fileVisit): ?>
                        <tr>
                            <td>
                                <span class="cut-text">  <?php _e('fileName', $fileVisit) ?></span>
                            </td>
                            <td class="text-primary font-weight-bold"><?php _e('slug', $fileVisit) ?></td>
                            <td class="text-muted"><?php echo Help::formatDT($fileVisit['createdAt']); ?></td>
                            <td class="text-muted"><?php _e(number_format($fileVisit['totalVisits'])); ?></td>
                            <td class="text-muted"><?php _e(number_format($fileVisit['uniqVisits'])); ?></td>
                            <td class="text-right">
                                <div class="btn-list flex-nowrap justify-content-end">
                                    <a href="<?php buildURIPath('cpanel/buckets/view?file=' . $fileVisit['id']); ?>" target="_blank" class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="" data-original-title="View File">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <path d="M12 9v2m0 4v.01"></path>
                                            <path d="M5.07 19H19a2 2 0 0 0 1.75 -2.75L13.75 4a2 2 0 0 0 -3.5 0L3.25 16.25a2 2 0 0 0 1.75 2.75"></path>
                                        </svg>
                                    </a>
                                    <a href="<?php _e(\CloudMonster\Helpers\Help::getFileLink($fileVisit['slug'])); ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="open file">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5"></path>
                                            <line x1="10" y1="14" x2="20" y2="4"></line>
                                            <polyline points="15 4 20 4 20 9"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if(!empty($analyticsData['buckets']['mostVisited'])): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Most Visited Buckets</h4>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                    <tr>
                        <th>Bucketname</th>
                        <th>Files</th>
                        <th>CreatedAt</th>
                        <th>Visitors</th>
                        <th>Unique</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($analyticsData['buckets']['mostVisited'] as $fileVisit): ?>
                        <tr>
                            <td>
                                <span class="cut-text">  <?php _e('name', $fileVisit) ?></span>
                            </td>
                            <td class="text-primary font-weight-bold"><?php _e('files', $fileVisit) ?></td>
                            <td class="text-muted"><?php echo Help::formatDT($fileVisit['createdAt']); ?></td>
                            <td class="text-muted"><?php _e(number_format($fileVisit['totalVisits'])); ?></td>
                            <td class="text-muted"><?php _e(number_format($fileVisit['uniqVisits'])); ?></td>
                            <td class="text-right">
                                <div class="btn-list flex-nowrap justify-content-end">
                                    <a href="<?php buildURIPath('cpanel/buckets/view/' . $fileVisit['id']); ?>" target="_blank" class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="" data-original-title="View File">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <path d="M12 9v2m0 4v.01"></path>
                                            <path d="M5.07 19H19a2 2 0 0 0 1.75 -2.75L13.75 4a2 2 0 0 0 -3.5 0L3.25 16.25a2 2 0 0 0 1.75 2.75"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>