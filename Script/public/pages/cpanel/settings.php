<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?>
            </h2>
        </div>
    </div>
</div>
<div id="alert-wrap"><?php  $this->displayAlerts(); ?></div>
<div class="row">
    <div class="col-4">
        <div class="list-group" id="list-tab" role="tablist">
            <a class="list-group-item list-group-item-action active " id="" data-toggle="list" href="#settings-general" role="tab" aria-controls="home">
                General</a>
            <a class="list-group-item list-group-item-action" id="" data-toggle="list" href="#settings-account" role="tab" aria-controls="">Account</a>
            <a class="list-group-item list-group-item-action" id="" data-toggle="list" href="#settings-cloud-upload" role="tab" aria-controls="">Cloud Upload</a>
            <a class="list-group-item list-group-item-action" id="" data-toggle="list" href="#settings-cloud-file" role="tab" aria-controls="">Cloud File/ Folder</a>
        </div>
    </div>
    <div class="col-8">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="settings-general" role="tabpanel" aria-labelledby="">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">General</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php postReq(); ?>/general" method="post" class="">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">File link slug</label>
                                <div class="col">
                                    <input type="text" class="form-control" name="file_link_slug" value="<?php _e('file',$config) ?>" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Add custom slug for single file link</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Bucket link slug</label>
                                <div class="col">
                                    <input type="text" class="form-control" name="bucket_link_slug"  value="<?php _e('bucket',$config) ?>" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Add custom slug for bucket link</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Analytics system</label>
                                <div class="col">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" name="analytics_system" <?php  echo _isChecked('analytics_system',$config) ?> type="checkbox" >
                                    </label>
                                    <small class="form-hint">If you enabled this option, we will collect visitor's info (IP and Country)</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Is visit info required ?</label>
                                <div class="col">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" name="is_visitor_info_required" <?php  echo _isChecked('is_visit_info_required',$config) ?> type="checkbox" >
                                    </label>
                                    <small class="form-hint">if you enabled this option,  if unable to find visitor info then we do not redirect visitor to the destination file,( <i>IP address and country</i> )</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Blacklisted IPs</label>
                                <div class="col">
                                    <textarea class="form-control" name="blacklisted_ips" rows="6" placeholder="186.249.100.169, 117.67.104.60"><?php _e('blacklisted_ips',$config) ?></textarea>
                                    <small class="form-hint">Separate each ip by comma </small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Blacklisted countries</label>
                                <div class="col">
                                    <textarea class="form-control" name="blacklisted_countries" rows="6" placeholder="us, cn, au"><?php _e('blacklisted_countries',$config) ?></textarea>
                                    <small class="form-hint">Separate each country code by comma </small>
                                </div>
                            </div>
                            <div class="form-footer text-right">
                                <button type="submit" class="btn btn-primary ">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="settings-account" role="tabpanel" aria-labelledby="">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php postReq(); ?>/account" method="post" class="">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Your Name</label>
                                <div class="col">
                                    <input type="text" class="form-control" name="admin_name" value="<?php _e('real_monster_name',$config) ?>" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Specify a real monster's name :) <br> min length: 4 </small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Username</label>
                                <div class="col">
                                    <input type="text" class="form-control" name="login_username"  value="<?php _e('login_username',$config) ?>" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Specify a username for account login.</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Old Password</label>
                                <div class="col">
                                    <input type="password" class="form-control" name="old_login_password"  value="" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Enter the old password to create new one.</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">New Password</label>
                                <div class="col">
                                    <input type="password" class="form-control" name="new_login_password"  value="" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Specify a password for account login. <br> min length : 4 </small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Confirm Password</label>
                                <div class="col">
                                    <input type="password" class="form-control" name="confirm_password"  value="" aria-describedby="" placeholder="" >
                                    <small class="form-hint">Confirm your new entered password.</small>
                                </div>
                            </div>
                            <div class="form-footer text-right">
                                <button type="submit" class="btn btn-primary ">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="settings-cloud-upload" role="tabpanel" aria-labelledby="">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cloud Upload</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php postReq(); ?>/cloud-upload" method="post" class="">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Max upload processes </label>
                                <div class="col">
                                    <input type="number" class="form-control" name="max_upload_processes" value="<?php _e('max_upload_process',$config) ?>" aria-describedby="" min="1" max="5" placeholder="" required>
                                    <small class="form-hint">Set the maximum number of upload processes (in the same time)</small>
                                    <small class="form-hint">Min : 1 &nbsp; Max : 5 &nbsp; default: 2</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Upload chunk size </label>
                                <div class="col">
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control"  name="upload_chunk_size" value="<?php _e('upload_chunk_size',$config) ?>"  aria-describedby="" min="1" max="25" placeholder="" required>
                                        <span class="input-group-text">
                              MB
                              </span>
                                    </div>
                                    <small class="form-hint">Set the maximum chunk size for upload</small>
                                    <small class="form-hint">Min : 1MB &nbsp; Max : 25MB &nbsp; default: 5MB</small>
                                </div>
                            </div>

                            <div class="form-footer text-right">
                                <button type="submit" class="btn btn-primary ">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="settings-cloud-file" role="tabpanel" aria-labelledby="">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cloud File/ Folder</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php postReq(); ?>/cloud-file" method="post" class="">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">File operators</label>
                                <div class="col">
                                    <div>
                                        <label class="form-check">
                                            <input class="form-check-input" name="file_op_rename" <?php echo _isChecked('file_op_rename',$config) ?>   type="checkbox" >
                                            <span class="form-check-label">Rename</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" name="file_op_move" <?php echo _isChecked('file_op_move',$config) ?>  type="checkbox" >
                                            <span class="form-check-label">Move</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input"  name="" type="checkbox" disabled>
                                            <span class="form-check-label">Delete</span>
                                        </label>
                                    </div>
                                    <small class="form-hint">Due to security reasons, you must enable the delete operator from the <i>config/config.php</i> file, If you need that option</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">Folder operators</label>
                                <div class="col">
                                    <div>
                                        <label class="form-check">
                                            <input class="form-check-input" name="folder_op_create" <?php echo _isChecked('folder_op_create',$config) ?> type="checkbox" >
                                            <span class="form-check-label">Create</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" name="folder_op_rename" <?php echo _isChecked('folder_op_rename',$config) ?> type="checkbox" >
                                            <span class="form-check-label">Rename</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" name="folder_op_move" <?php echo _isChecked('folder_op_move',$config) ?> type="checkbox" >
                                            <span class="form-check-label">Move</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" disabled>
                                            <span class="form-check-label">Delete</span>
                                        </label>
                                    </div>
                                    <small class="form-hint">Due to security reasons, you must enable the delete operator from the <i>config/config.php</i> file, If you need that option</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">File check time</label>
                                <div class="col">
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control" value="<?php  _e('file_check_time',$config) ?>" name="file_check_time" min="1" placeholder="" required>
                                        <span class="input-group-text">
                              hours
                              </span>
                                    </div>
                                    <small class="form-hint">Specify how long after a file should be checked for availability
                                        <br> (default : 24 hours)</small>
                                </div>
                            </div>
                            <div class="form-footer text-right">
                                <button type="submit" class="btn btn-primary ">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

