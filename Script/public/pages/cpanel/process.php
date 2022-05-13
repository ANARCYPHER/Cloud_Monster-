<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?>
            </h2>
        </div>
        <div class="col-auto ml-auto d-print-none">
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
</div>
<div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
<div class="row">
    <div class="col-lg-9">
        <div class="row row-cards row-deck">
            <div class="col-md-4">
                <div class="card" >
                    <div class="card-body">
                        <div class="float-right stamp bg-orange-lt ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="7" cy="6" r="2"></circle>
                                <circle cx="17" cy="6" r="2"></circle>
                                <line x1="7" y1="8" x2="7" y2="16"></line>
                                <path d="M9 18h6a2 2 0 0 0 2 -2v-5"></path>
                                <polyline points="14 14 17 11 20 14"></polyline>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Active Threads</div>
                        <h3 class="h2 mt-2 active-threads">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" >
                    <div class="card-body">
                        <div class="float-right stamp bg-red-lt ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <rect x="8" y="8" width="8" height="8" rx="1"></rect>
                                <line x1="3" y1="8" x2="4" y2="8"></line>
                                <line x1="3" y1="16" x2="4" y2="16"></line>
                                <line x1="8" y1="3" x2="8" y2="4"></line>
                                <line x1="16" y1="3" x2="16" y2="4"></line>
                                <line x1="20" y1="8" x2="21" y2="8"></line>
                                <line x1="20" y1="16" x2="21" y2="16"></line>
                                <line x1="8" y1="20" x2="8" y2="21"></line>
                                <line x1="16" y1="20" x2="16" y2="21"></line>
                            </svg>
                        </div>
                        <div class="text-muted font-weight-normal mt-0">Memory Usage</div>
                        <h3 class="h2 mt-2 total-thread-memory-usage">0 B</h3>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Threads</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table" id="threads-tbl">
                            <thead>
                            <tr>
                                <th>PID</th>
                                <th>RAM Usage</th>
                                <th>Created At</th>
                                <th>Run Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="col-12">
            <div class="card" >
                <div class="card-body">
                    <div class="float-right stamp bg-blue-lt ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <line x1="12" y1="4" x2="12" y2="14"></line>
                            <line x1="12" y1="4" x2="16" y2="8"></line>
                            <line x1="12" y1="4" x2="8" y2="8"></line>
                            <line x1="4" y1="20" x2="20" y2="20"></line>
                        </svg>
                    </div>
                    <div class="text-muted font-weight-normal mt-0 ">Upload Process</div>
                    <h3 class="h2 mt-2 total-upload-process">0</h3>
                    <div class="mt-3">
                        <a href="#" class="btn btn-sm btn-secondary  mr-3">
                            View Tracker
                        </a>
                        <a href="#" class="btn btn-sm btn-secondary ">
                            Refresh
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>