<?php

use CloudMonster\Helpers\Help;

?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <!-- Page pre-title -->
            <h2 class="page-title">
                <?php _e($this->pageTitle) ?>
            </h2>
        </div>
        <!-- /.col -->
        <!-- Page title actions -->
        <div class="col-auto ml-auto d-print-none">
         <span class="d-none d-sm-inline">
            <a href="#" class="btn btn-secondary"  data-toggle="modal" data-target="#new-folder-modal">
               <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z"></path>
                  <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                  <line x1="12" y1="10" x2="12" y2="16"></line>
                  <line x1="9" y1="13" x2="15" y2="13"></line>
               </svg>
               &nbsp;New Folder
            </a>
         </span>
            <a href="<?php buildURIPath('cpanel/buckets/new?folder='.$currentFolder['id']); ?>" class="btn btn-primary ml-3 d-none d-sm-inline-block">
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
    <!-- /.row -->
</div>
<!-- /.page-header -->


<div class="bucket-list-wrap" data-active-folder="<?php echo $currentFolder['id']; ?>">
    <div class="card">
        <div class="card-body py-2">
            <div class="selected-drive d-flex justify-content-between" >
                <div class="drives-list d-flex lh-sm py-1 align-items-center">
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
                <!-- /.drives-list -->
                <div class="row" >
                    <label class="form-label col-3 col-form-label" style="width: 150px;">Select Your Drive : </label>
                    <div class="col">
                        <select name="source"  class="form-select " id="select-drives-list" style="width: 220px;">
                            <option value="0" data-data='{"avatar": "<span class=\"avatar avatar-sm rounded mr-2 ml-n1\" style=\"background-image: url()\"></span>"}'>-- default -- </option>
                            <?php foreach ($drives as $drive): ?>
                                <option value="<?php _e($drive['id']) ?>"
                                        data-data='{"avatar": "<span class=\"avatar avatar-sm rounded mr-2 ml-n1\" style=\"background-image: url(<?php imgUri('cdrives/'.$drive['type'].'.png'); ?>)\"></span>"}'
                                    <?php _selected($activeDriveId, $drive['id']); ?>
                                >
                                    <?php _e('name',$drive); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- /.form-select -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.selected-drive -->
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="alert alert-light-gray   move-alert-wrap " style="display: none"  data-id="0">
        <span class="mr-auto">Your selected folder ready to move</span>
        <div>
            <button class="btn btn-sm mr-3 move-bucket"  data-move="close">Cancel</button>
            <button class="btn btn-sm btn-dark move-bucket"  data-move="drop">Drop here</button>
        </div>
    </div>
    <!-- /.alert -->

    <div class="card" >
        <div id="alert-wrap"></div>
        <div class="card-header w-100" id="buckets-breadcrumb" style="display: none">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="<?php buildURIPath('cpanel/buckets/list?drive='.$activeDriveId); ?>">Home</a></li>
                <?php foreach ($parentFolders as $folder): ?>
                    <li class="breadcrumb-item"><a href="<?php buildURIPath('cpanel/buckets/list/'. $folder['id'] . '?drive=' . $activeDriveId ); ?>"><?php _e('name', $folder) ?></a></li>
                <?php endforeach; ?>
                <!--                <li class="breadcrumb-item active" aria-current="page"><a href="#">Data</a></li>-->
            </ol>
        </div>
        <?php if(!empty($list)): ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table" id="bucket-list-tbl">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th class="text-center">Created At</th>
                        <th class="text-center">Updated At</th>
                        <th class="w-1"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $key => $item): ?>
                        <tr id="<?=$item['id']?>" class="item"  data-type="<?php _e('type', $item);  ?>"    >
                            <td class="name clickable-row " data-href="<?php _e('href', $item); ?>" data-target="<?php if(!$item['isFolder']) echo '_blank'; ?>">
                                <div class="cut-text cut-text-long">
                                    <?php if($item['isFolder']): ?>
                                        <svg class="icon tb-icon text-warning align-middle" width="1em" height="1em" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M11.828 5h3.982a2 2 0 011.992 2.181l-.637 7A2 2 0 0115.174 16H4.826a2 2 0 01-1.991-1.819l-.637-7a1.99 1.99 0 01.342-1.31L2.5 5a2 2 0 012-2h3.672a2 2 0 011.414.586l.828.828A2 2 0 0011.828 5zm-8.322.12C3.72 5.042 3.95 5 4.19 5h5.396l-.707-.707A1 1 0 008.172 4H4.5a1 1 0 00-1 .981l.006.139z" clip-rule="evenodd"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="icon tb-icon text-primary align-middle" width="1em" height="1em" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M14.643 17C15.979 17 17 15.845 17 14.5V7H3v7.5C3 15.845 4.021 17 5.357 17h9.286zM8 9a.5.5 0 000 1h4a.5.5 0 000-1H8zM3 3a1 1 0 00-1 1v1.5a1 1 0 001 1h14a1 1 0 001-1V4a1 1 0 00-1-1H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    <?php endif;  ?>
                                    &nbsp;<span class="name-txt "><?php _e('name', $item) ?></span>
                                    <?php if(!$item['isFolder']): ?>
                                        <span class="file-ext">.<?php _e('ext', $item) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center date text-muted">
                                <?php _e('createdAt', $item); ?>
                            </td>
                            <td class="text-center date text-muted ">
                                <?php _e('updatedAt', $item); ?>
                            </td>
                            <td class="text-right">
                                 <span class="dropdown ml-1 position-static">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle align-text-top" data-boundary="viewport" data-toggle="dropdown" aria-expanded="false">Actions</button>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                           <a class="dropdown-item text-info rename-bucket" data-rename="get-data" id="" href="javascript:void(0)" >
                                           Rename
                                           </a>
                                           <a class="dropdown-item text-warning move-bucket" data-toggle="modal" data-target="#select-folder-modal" href="javascript:void(0)">
                                           Move
                                           </a>
                                           <a class="dropdown-item text-danger delete-bucket" data-confirm href="javascript:void(0)">
                                           Delete
                                           </a>
                                    </div>
                                 </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        <?php else: ?>
            <div class="empty my-5">
                <div class="empty-icon">
                    <!-- SVG icon code -->
                </div>
                <p class="empty-title h4">No Data</p>
                <div class="empty-action">
                </div>
            </div>
            <!-- /.empty -->
        <?php endif; ?>
    </div>
    <!-- /.card -->

</div>

<?php includePartial('/modal/new-folder'); ?>
<?php includePartial('/modal/del-bucket'); ?>
<?php includePartial('/modal/rename-bucket'); ?>
<?php includePartial('/modal/select-folder'); ?>