<?php

    use CloudMonster\Helpers\Help;

?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?>
            </h2>
        </div>
        <div class="col-auto ml-auto d-print-none">
            <a href="<?php buildURIPath('cpanel/drives/new'); ?>" class="btn btn-primary ml-3 d-none d-sm-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New
            </a>
        </div>
    </div>
</div>


<div class="cloud-drive-list-wrap">
    <div class="card" >
        <div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table " id="cloud-drive-list-tbl">
                <thead>
                <tr>
                    <th>#id</th>
                    <th>Name & Source</th>
                    <th>Num Of Buckets</th>
                    <th> Buckets Size</th>
                    <th>Last Updated At</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($driveList as $k => $drive): ?>
                    <tr id="<?php echo $drive['id']; ?>">
                        <td>#<?php echo $k+1; ?></td>
                        <td>
                            <div class="d-flex lh-sm py-1 align-items-center">
                                <span class="avatar mr-2" style="background-image: url( <?php imgUri('cdrives/'. $drive['type'] .  '.png'); ?>  )"></span>
                                <div class="flex-fill">
                                    <div class="strong name"><?php _e('name', $drive) ?></div>
                                    <div class="text-muted text-h5"><a href="javascript:void(0)" class="text-reset"><?php _e('type', $drive) ?></a></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php echo number_format( $drive['buckets']); ?>
                        </td>
                        <td><?php echo Help::formatSizeUnits( $drive['size']); ?></td>
                        <td class="date text-muted">
                            <?php echo Help::formatDT($drive['updatedAt']); ?>
                        </td>
                        <td>
                            <?php
                            echo Help::formatDriveStatus($drive['fstatus']);

                            ?>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="<?php buildURIPath('cpanel/files/list?drive='.$drive['id']); ?>" class="btn btn-secondary btn-sm " data-toggle="tooltip" data-placement="top" title="View Files">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <polyline points="14 3 14 8 19 8"></polyline>
                                        <path d="M17 21H7a2 2 0 0 1 -2 -2V5a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                        <line x1="9" y1="7" x2="10" y2="7"></line>
                                        <line x1="9" y1="13" x2="15" y2="13"></line>
                                        <line x1="13" y1="17" x2="15" y2="17"></line>
                                    </svg>
                                </a>
                                <a href="<?php buildURIPath('cpanel/buckets/list?drive='.$drive['id']); ?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="View Buckets">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                                        <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                                        <line x1="10" y1="12" x2="14" y2="12"></line>
                                    </svg>
                                </a>
                                <button class="btn btn-secondary btn-sm view-more-info" data-toggle="tooltip" data-placement="top" title="View More Info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </button>
                                <span class="dropdown ml-1 position-static">
                           <button class="btn btn-secondary btn-sm dropdown-toggle align-text-top" data-boundary="viewport" data-toggle="dropdown" aria-expanded="false">Actions</button>
                           <div class="dropdown-menu dropdown-menu-right" style="">
                              <a class="dropdown-item text-info" id="" href="<?php buildURIPath('cpanel/drives/edit/' . $drive['id']); ?>">
                              Edit
                              </a>
                              <?php if($drive['fstatus'] != 'error'): ?>
                                  <a class="dropdown-item text-warning"href="<?php buildURIPath('cpanel/drives/pause/' . $drive['id']); ?>">
                              <?php echo $drive['fstatus'] == 'active' ? 'pause' : 'active'; ?>
                              </a>
                              <?php endif; ?>
                              <a class="dropdown-item text-danger delete-drive" data-confirm="1"  href="javascript:void(0)">
                              Delete
                              </a>
                           </div>
                        </span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php includePartial('/modal/drive-more-info'); ?>
<?php includePartial('/modal/del-drive'); ?>

