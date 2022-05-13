<div class="modal modal-blur fade " id="re-upload-modal" tabindex="-1" role="dialog" aria-hidden="true"  >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Re-Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body py-2 px-3">
                <div id="re-upload-alert-wrap"></div>
                <div class="file-info-wrap" style="display: block">
                    <div class="mb-3 overflow-hidden">
                        <label class="form-check form-check-inline">
                            <input class="form-check-input select-re-upload-type" name="re-upload-type" value="auto" type="radio" checked="">
                            <span class="form-check-label">Auto</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input class="form-check-input select-re-upload-type"  name="re-upload-type" value="manually" type="radio" >
                            <span class="form-check-label">Manual</span>
                        </label>
                    </div>
                    <div class="re-upload-auto">
                        <div class="auto-upload-msg">
                            <p>Your file will be uploaded automatically.  <a href="#">Learn more</a> </p>
                        </div>
                    </div>
                    <div class="re-upload-manually" style="display: none">
                        <div class="upload-zone"  id="uploadzone">
                            <div class="dz-message" data-dz-message>
                                <span class="dz-message-text"><span>Drag and drop</span> file here or <span>browse</span></span>
                            </div>
                        </div>
                        <div class="file-info py-3" style="display: none">
                            <dl class="row">
                                <dt class="col-md-2">Name:</dt>
                                <dd class="col-md-10 fname"></dd>
                                <dt class="col-md-2">Size:</dt>
                                <dd class="col-md-10 fsize"></dd>
                                <dt class="col-md-2">Type:</dt>
                                <dd class="col-md-10 ftype"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="drive-list-wrap" style="display: none">
                    <div class="mb-3 overflow-hidden">
                        <label class="form-check form-check-inline">
                            <input class="form-check-input select-re-upload-dest-type" name="re-upload-dest-type" value="new-drive" type="radio" checked="">
                            <span class="form-check-label">To New Drives</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input class="form-check-input select-re-upload-dest-type"  name="re-upload-dest-type" value="exit-file" type="radio" >
                            <span class="form-check-label">To Exist Files</span>
                        </label>
                    </div>
                    <div class="mb-3 to-new-drive" >
                        <?php if(!empty($newDrives)): ?>
                            <p>Select Drive: </p>
                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column cloud-drive-select-group" style="max-height: 250px">
                                <?php foreach ($newDrives as $key => $val): ?>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="checkbox" name="drives" data-type="new-drive" value="<?php _e('id', $val); ?>" class="form-selectgroup-input drive-select">
                                        <div class="form-selectgroup-label d-flex align-items-center justify-content-between py-1 px-2">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div class="form-selectgroup-label-content d-flex align-items-center ">
                                                    <span class="avatar  mr-3" style="background-image: url(<?php imgUri('cdrives/'.$val['type'].'.png'); ?>)"></span>
                                                    <div class="lh-sm">
                                                        <div class="strong"><?php _e('name', $val); ?></div>
                                                        <div class="text-muted"><?php _e('type', $val); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="u-icon mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                                    <path d="M4 13h3l3 3h4l3 -3h3"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Cloud Drives Not Found</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 to-exit-file" style="display: none">
                        <?php if(!empty($files)): ?>
                            <p>Select File: </p>
                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column cloud-drive-select-group" style="max-height: 250px">
                                <?php foreach ($files as $key => $val): ?>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="checkbox" name="drives" data-type="exist-file" value="<?php _e('id', $val); ?>" class="form-selectgroup-input drive-select">
                                        <div class="form-selectgroup-label d-flex align-items-center justify-content-between py-1 px-2">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div class="form-selectgroup-label-content d-flex align-items-center ">
                                                    <span class="avatar  mr-3" style="background-image: url(<?php imgUri('cdrives/'.$val['type'].'.png'); ?>)"></span>
                                                    <div class="lh-sm">
                                                        <div class="strong"><?php _e('name', $val); ?></div>
                                                        <div class="text-muted"><?php _e('type', $val); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="u-icon mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                                    <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                                                    <line x1="9" y1="7" x2="15" y2="7"></line>
                                                    <line x1="9" y1="11" x2="15" y2="11"></line>
                                                    <line x1="9" y1="15" x2="13" y2="15"></line>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Cloud Drives Not Found</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary " id="upload" data-action="next" >Next</button>
            </div>
        </div>
    </div>
</div>