<?php if(!empty($bucket)): ?>
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <h2 class="page-title">
                    <b>Bucket: </b>
                    <small class="text-muted bucket-name align-middle">
                        <span class="name-txt cut-text"><?php _e('name', $bucket); ?></span>
                        .<span class="file-ext"><?php _e('ext', $bucket); ?></span>
                    </small>
                </h2>
            </div>
            <div class="col-auto ml-auto d-print-none">
         <span class="dropdown  position-static">
            <button class="btn btn-secondary  dropdown-toggle " data-boundary="viewport" data-toggle="dropdown" aria-expanded="false">
               <svg xmlns="http://www.w3.org/2000/svg" class="icon tb-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <rect x="4" y="6" width="4" height="5" rx="1"></rect>
                  <line x1="6" y1="4" x2="6" y2="6"></line>
                  <line x1="6" y1="11" x2="6" y2="20"></line>
                  <rect x="10" y="14" width="4" height="5" rx="1"></rect>
                  <line x1="12" y1="4" x2="12" y2="14"></line>
                  <line x1="12" y1="19" x2="12" y2="20"></line>
                  <rect x="16" y="5" width="4" height="6" rx="1"></rect>
                  <line x1="18" y1="4" x2="18" y2="5"></line>
                  <line x1="18" y1="11" x2="18" y2="20"></line>
               </svg>
               Settings
            </button>
            <div class="dropdown-menu dropdown-menu-right " style="">
               <a class="dropdown-item text-info rename-bucket" href="javascript:void(0)" data-rename="get-data" >
               Rename
               </a>
               <a class="dropdown-item text-danger delete-bucket" data-confirm="1" href="javascript:void(0)" >
               Delete
               </a>
            </div>
         </span>
                <a href="<?php _e('link', $bucket); ?>" target="_blank" class="btn  btn-secondary ml-2" id="bucket-link"  >
                    <svg xmlns="http://www.w3.org/2000/svg" class=" icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5"></path>
                        <line x1="10" y1="14" x2="20" y2="4"></line>
                        <polyline points="15 4 20 4 20 9"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div id="bucket" data-id="<?php echo $bucket['id']; ?>" data-shared="<?php _e('shared', $bucket); ?>">
        <div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="mr-auto">
                        <span class="mr-3">Files: <span class="badge bg-primary"><?php echo count($files); ?></span> </span>
                    </div>
                    <div>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#re-upload-modal" class="btn btn-sm  btn-outline-warning"   >
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M7 18a4.6 4.4 0 0 1 0 -9h0a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                                <polyline points="9 15 12 12 15 15"></polyline>
                                <line x1="12" y1="12" x2="12" y2="21"></line>
                            </svg>
                            Re-upload
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm ml-2 btn-primary share-bucket" id=""  >
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3"></path>
                            </svg>
                            Share
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="card" id="rm-file-download-progress" style="display: none">
            <div class="card-body">
                <div class="badge bg-primary mb-2">Remote File Download Progressing : </div>
                <div class="rm-progress-wrap mr-3">
                    <div class="progress mb-1">
                        <div class="progress-bar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> <span class="progress-bar-text">0%</span>
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                    <div class="text-h5 d-flex">
                        <span class="mr-auto"><b>Time Remaining:</b>  <span class="remainingTime">00 min 00 sec</span></span>
                        <span class="currentSpeed">0 K/s</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" >
            <div class="card-header">
                <h3 class="card-title">Drive Files</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable table-vcenter card-table table-striped drive-files" id="tracker">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Drive</th>
                            <th>File</th>
                            <th>Total Visits</th>
                            <th>Unique Visits</th>
                            <th>Status</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files as $key => $file): ?>
                            <tr id="<?php echo $file['id']; ?>" data-status="<?php _e('pstatus', $file); ?>" data-type="<?php _e('type', $file); ?>" >
                                <td>#0</td>
                                <td data-label="Name">
                                    <div class="d-flex lh-sm py-1 align-items-center">
                                        <span class="avatar mr-2" style="background-image: url(<?php imgUri('cdrives/'.$file['type'].'.png'); ?>)"></span>
                                        <div class="flex-fill">
                                            <div class="strong"><?php _e('name', $file); ?> </div>
                                            <div class="text-muted text-h5"><a href="#" class="text-reset"><?php _e('type', $file); ?></a></div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Title" class="text-muted message" style="min-width: 300px">
                                    <?php if($file['pstatus'] == 'process' || $file['pstatus'] == 'waiting'): ?>
                                        <div class="progress-wrap mr-3">
                                            <div class="progress mb-1">
                                                <div class="progress-bar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> <span class="progress-bar-text">0%</span>
                                                    <span class="sr-only">0% Complete</span>
                                                </div>
                                            </div>
                                            <div class="text-h5 d-flex">
                                                <span class="mr-auto"><b>Time Remaining:</b>  <span class="remainingTime">00 min 00 sec</span></span>
                                                <span class="currentSpeed">0 K/s</span>
                                            </div>
                                        </div>
                                    <?php elseif($file['pstatus'] == 'failed'): ?>
                                        <span class="text-danger"> <?php _e('msg', $file); ?> </span>
                                    <?php elseif($file['pstatus'] == 'active'): ?>
                                        <span class="text-primary font-weight-bold"><?php _e('code', $file); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted text-center">
                                    <?php _e(number_format($file['totalVisits'])); ?>
                                </td>
                                <td class="text-muted text-center">
                                    <?php _e(number_format($file['uniqVisits'])); ?>
                                </td>
                                <td class="text-muted ">
                                    <span class="badge status-badge status-<?php echo $file['pstatus']; ?> align-middle"></span>&nbsp;
                                    <span class="status-text"><?php _e('pstatus', $file); ?></span>
                                </td>
                                <td class="text-right">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <a href="javascript:void(0)"
                                           data-link="<?php _e(\CloudMonster\Helpers\Help::getFileLink($file['slug'])); ?>"
                                           class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy link">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"></path>
                                                <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"></path>
                                            </svg>
                                        </a>
                                        <a href="<?php buildURIPath('cpanel/analytics?file=' . $file['id']); ?>"  class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="" data-original-title="View Analytics">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <polyline points="4 19 8 13 12 15 16 10 20 14 20 19 4 19"></polyline>
                                                <polyline points="4 12 7 8 11 10 16 4 20 8"></polyline>
                                            </svg>
                                        </a>
                                        <a href="<?php _e(\CloudMonster\Helpers\Help::getFileLink($file['slug'])); ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Open File">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5"></path>
                                                <line x1="10" y1="14" x2="20" y2="4"></line>
                                                <polyline points="15 4 20 4 20 9"></polyline>
                                            </svg>
                                        </a>
                                        <a href="javascript:void(0)"  class="btn btn-outline-danger btn-sm delete-file" data-confirm="1" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Analytics">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <line x1="4" y1="7" x2="20" y2="7"></line>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
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
    </div>
    <?php includePartial('/modal/upload-progress'); ?>
    <?php includePartial('/modal/rename-bucket'); ?>
    <?php includePartial('/modal/del-file'); ?>
    <?php includePartial('/modal/del-bucket'); ?>
    <?php includePartial('/modal/share-bucket'); ?>
    <?php include_once ROOT . '/' . TEMPLATE_DIR . '/pages/cpanel/partials/modal/re-upload.php'; ?>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        Bucket not found
    </div>
<?php endif; ?>