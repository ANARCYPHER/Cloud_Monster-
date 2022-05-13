<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?>
            </h2>
        </div>
        <div class="col-auto ml-auto d-print-none">
            <a href="<?php buildURIPath('cpanel/drives/list'); ?>" class="btn btn-primary ml-3 d-none d-sm-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <line x1="9" y1="6" x2="20" y2="6"></line>
                    <line x1="9" y1="12" x2="20" y2="12"></line>
                    <line x1="9" y1="18" x2="20" y2="18"></line>
                    <line x1="5" y1="6" x2="5" y2="6.01"></line>
                    <line x1="5" y1="12" x2="5" y2="12.01"></line>
                    <line x1="5" y1="18" x2="5" y2="18.01"></line>
                </svg>
                Cloud Drive List
            </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-9 ">
        <div class="card">
            <div class="card-body">
                <div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
                <?php if($isDriveFound): ?>
                    <form  action="<?php postReq(); ?>" method="POST" id="new-drive-form" data-is-edit="<?php _e($isEdit); ?>" data-id="<?php _e('id', $formData) ?>">
                        <div class="form-group mb-3 ">
                            <label class="form-label">Select Cloud Drive</label>
                            <select name="source"  class="form-select " id="select-drives-list"  <?php if($isEdit) echo 'disabled'; ?> >
                                <option value=""></option>
                                <?php foreach ($drives as $val): ?>
                                    <option value="<?php _e($val) ?>"
                                            data-data='{"avatar": "<span class=\"avatar avatar-sm rounded mr-2 ml-n1\" style=\"background-image: url(<?php imgUri('cdrives/'.$val.'.png'); ?>)\"></span>"}'
                                        <?php _selected($source, $val); ?>
                                    >
                                        <?php _e($val) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if(!empty($drive)): ?>
                            <div class="form-group mb-3 ">
                                <label class="form-label">Drive Name  </label>
                                <div>
                                    <input type="text" class="form-control" name="name" value="<?php _e('name', $formData) ?>"  placeholder="Enter drive name. (Ex: My OneDrive Personal)" >
                                </div>
                            </div>
                            <?php foreach ($drive->getUserInput() as $name => $uInput): ?>
                                <div class="form-group mb-3 ">
                                    <label class="form-label"><?php _e('label', $uInput) ?>  <sup class="text-danger">*</sup> </label>
                                    <div>
                                        <input type="text" class="form-control" name="<?php _e($name); ?>" value="<?php _e('val', $uInput) ?>" placeholder="<?php _e('placeholder', $uInput) ?>"  >
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-footer text-right">
                                <button type="submit" class="btn btn-primary" >Submit</button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if($isDriveFound && $isEdit): ?>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Drive API
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="float-left">Status:</span>
                        <?php echo \CloudMonster\Helpers\Help::formatDriveStatus(_g('status', $formData)); ?>
                    </div>
                    <a  href="<?php buildURIPath('cpanel/drives/edit/' . _g('id', $formData) . '?check=1'); ?>" class="btn btn-warning btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <path d="M7 10h3v-3l-3.5 -3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1 -3 3l-6-6a6 6 0 0 1 -8 -8l3.5 3.5"></path>
                        </svg>
                        Check API
                    </a>
                    <a href="<?php buildURIPath('cpanel/files/list?drive='._g('id', $formData)); ?>" class="btn btn-secondary btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11"></path>
                            <line x1="8" y1="8" x2="12" y2="8"></line>
                            <line x1="8" y1="12" x2="12" y2="12"></line>
                            <line x1="8" y1="16" x2="12" y2="16"></line>
                        </svg>
                        View Files
                    </a>
                    <a  href="<?php buildURIPath('cpanel/buckets/list?drive='._g('id', $formData)); ?>" class="btn btn-secondary btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <rect x="3" y="4" width="18" height="4" rx="2"></rect>
                            <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        View Buckets
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>