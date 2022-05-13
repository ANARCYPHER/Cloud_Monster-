
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <!-- Page pre-title -->
            <h2 class="page-title">
                <?php _e($this->pageTitle) ?>
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ml-auto">
            <a href="<?php buildURIPath('cpanel/buckets/list'); ?>" class="btn btn-primary ml-3 d-none d-sm-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                    <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
                Bucket List
            </a>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.page-header -->


<div id="main-upload-wrap" data-tmp-folder="<?php _e($tmpFolderId); ?>">

    <form>
        <div class="row" >
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <div id="alert-wrap"></div>
                        <!-- /#alert-wrap -->

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control bucket-name" name="" placeholder="Enter bucket name">
                        </div>

                        <div class="form-group mb-3 ">
                            <label class="form-label">Select File</label>
                            <div>
                                <div class="upload-zone"  id="uploadzone">
                                    <div class="dz-message" data-dz-message>
                                        <span class="dz-message-text"><span>Drag and drop</span> file here or <span>browse</span></span>
                                    </div>
                                </div>
                                <!-- /.upload-zone -->
                            </div>
                        </div>
                        <!-- /.form-group -->


                        <div class="file-info mb-3" style="display: none">
                            <dl class="row">

                                <dt class="col-md-2">Name:</dt>
                                <dd class="col-md-10 fname"></dd>

                                <dt class="col-md-2">Size:</dt>
                                <dd class="col-md-10 fsize"></dd>

                                <dt class="col-md-2">Type:</dt>
                                <dd class="col-md-10 ftype"></dd>

                            </dl>
                        </div>
                        <!-- /.file-info -->
                        <div class="hr-text">or</div>

                        <div class="mb-3">
                            <label class="form-label">Remote File Url</label>
                            <input type="url"  class="form-control" id="remote-link" name="" value="" placeholder="Enter remote file link">
                            <small class="form-hint"><b>Supported Url/s</b> : <i>google drive, direct file link</i> </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload To</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control upload-path" data-id="0" name="" value="<?php _e($fLocation); ?>" readonly>
                                <a class="btn btn-secondary" href="javascript:void(0)" data-toggle="modal" data-target="#select-folder-modal">Select Folder</a>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


                <div class="card">
                    <div class="card-body">
                        <button type="button" id="upload" data-action="upload" class="btn btn-primary btn-block">Upload</button>
                    </div>
                </div>
                <!-- /.card -->

            </div>
            <!-- /.col-md-8 -->


            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cloud Drives</h3>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column cloud-drive-select-group" >

                                <?php foreach ($drives as $key => $val): ?>

                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="checkbox" name="drives" value="<?php _e('id', $val); ?>" class="form-selectgroup-input c-drive-select">
                                        <div class="form-selectgroup-label d-flex align-items-center py-1 px-2">
                                            <div class="mr-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div class="form-selectgroup-label-content d-flex align-items-center ">
                                                <span class="avatar  mr-3" style="background-image: url(<?php imgUri('cdrives/'.$val['type'].'.png'); ?>)"></span>
                                                <div class="lh-sm">
                                                    <div class="strong"> <?php _e('name', $val); ?> </div>
                                                    <div class="text-muted"> <?php _e('type', $val); ?> </div>
                                                </div>
                                            </div>
                                            <!-- /.form-selectgroup-label-content -->
                                        </div>
                                        <!-- /.form-selectgroup-label -->
                                    </label>
                                <!-- /.form-selectgroup-item -->

                                <?php endforeach; ?>

                            </div>
                            <!-- /.form-selectgroup -->

                            <div class="mt-3">
                                <div><a href="javascript:void(0)" class="select-all-drives" data-action="select-all">select all</a></div>
                                <div><a href="javascript:void(0)" class="select-all-drives" data-action="unselect-all">unselect all</a></div>
                            </div>

                        </div>
                        <!-- /.mb-3 -->

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


            </div>
            <!-- /.col-md-4 -->

        </div>
        <!-- /.row -->
    </form>
    <!-- /.form -->

</div>
<!-- /#main-upload-wrap -->

<!-- upload progress modal -->
<?php includePartial('/modal/upload-progress'); ?>
<!-- folder select modal -->
<?php includePartial('/modal/select-folder'); ?>
