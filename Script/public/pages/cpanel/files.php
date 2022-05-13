<?php

    use CloudMonster\Helpers\Help;

?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?> <small>( <i><?php echo count($files); ?></i> )</small>
            </h2>
        </div>
        <div class="col-auto ml-auto d-print-none">
            <?php if($status == 'active'): ?>
                <a href="javascript:void(0)" class="btn btn-success ml-3 d-none d-sm-inline-block disabled">
                    ACTIVE
                </a>
            <?php elseif($status == 'failed'): ?>
                <a href="javascript:void(0)" class="btn btn-danger ml-3 d-none d-sm-inline-block disabled">
                    FAILED
                </a>
            <?php elseif($status == 'waiting'): ?>
                <a href="javascript:void(0)" class="btn btn-secondary ml-3 d-none d-sm-inline-block disabled">
                    UPLOAD PENDING
                </a>
            <?php elseif($status == 'process'): ?>
                <a href="javascript:void(0)" class="btn btn-warning ml-3 d-none d-sm-inline-block disabled">
                    UPLOAD PROCESSING
                </a>
            <?php else: ?>
                <a href="javascript:void(0)" class="btn btn-primary ml-3 d-none d-sm-inline-block disabled">
                    ALL FILES
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="alert-wrap">
    <?php  $this->displayAlerts(); ?>
</div>


<div class="box">
    <div class="card">
        <div class="card-body py-2">
            <div class="selected-drive d-flex justify-content-between" >
                <div class="d-flex lh-sm py-1 align-items-center">
                    <?php if(!empty($activeDrive['id'])): ?>
                        <span class="avatar mr-2" style="background-image: url( <?php imgUri('cdrives/'.$activeDrive['type'].'.png'); ?>  )"></span>
                        <div class="flex-fill">
                            <div class="strong name"><?php _e('name', $activeDrive); ?></div>
                            <div class="text-muted text-h5"><a href="javascript:void(0)" class="text-reset"><?php _e('type', $activeDrive); ?></a></div>
                        </div>
                        <span class="ml-3 mb-3">
               <?php echo Help::formatDriveStatus($activeDrive['fstatus']); ?>
               </span>
                    <?php else: ?>
                        <span>Not selected</span>
                    <?php endif; ?>
                </div>
                <div class="row" >
                    <label class="form-label col-3 col-form-label" style="width: 150px;">Select Your Drive : </label>
                    <div class="col">
                        <select name="source"  class="form-select " id="select-drives-list" style="width: 220px;">
                            <option value="0"           data-data='{"avatar": "<span class=\"avatar avatar-sm rounded mr-2 ml-n1\" style=\"background-image: url()\"></span>"}'>-- default -- </option>
                            <?php foreach ($drives as $drive): ?>
                                <option value="<?php _e($drive['id']) ?>"
                                        data-data='{"avatar": "<span class=\"avatar avatar-sm rounded mr-2 ml-n1\" style=\"background-image: url(<?php imgUri('cdrives/'.$drive['type'].'.png'); ?>)\"></span>"}'
                                    <?php _selected($activeDriveId, $drive['id']); ?>
                                >
                                    <?php _e('name',$drive); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" >
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable table-vcenter card-table table-striped drive-files" id="files-list">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Drive</th>
                        <th>File Code & FileName</th>
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
                            <td data-label="Title" class="text-muted message" style="min-width: 150px">
                                <?php if($file['pstatus'] == 'process' || $file['pstatus'] == 'waiting'): ?>
                                    <div class="text-warning"> Not ready </div>
                                <?php elseif($file['pstatus'] == 'failed'): ?>
                                    <div class="text-danger"> <?php _e('msg', $file); ?> </div>
                                <?php elseif($file['pstatus'] == 'active'): ?>
                                    <div class="text-primary font-weight-bold"><?php _e('code', $file); ?></div>
                                <?php endif; ?>
                                <div class="cut-text">
                                    <?php _e('fileName', $file); ?>
                                </div>
                            </td>
                            <td class="text-muted text-center"><?php _e(number_format($file['totalVisits'])); ?></td>
                            <td class="text-muted text-center"><?php _e(number_format($file['uniqVisits'])); ?></td>
                            <td class="text-muted ">
                                <span class="badge status-badge status-<?php _e($file['pstatus']); ?> align-middle"></span>&nbsp;
                                <span class="status-text"><?php _e('pstatus', $file); ?></span>
                            </td>
                            <td class="text-right">
                                <div class="btn-list flex-nowrap justify-content-end">
                                    <a href="<?php buildURIPath('cpanel/buckets/view/' . $file['bucketId']); ?>" target="_blank" class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="" data-original-title="View Bucket">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                                            <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                                            <line x1="10" y1="12" x2="14" y2="12"></line>
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