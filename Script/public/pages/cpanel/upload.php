<form>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-3 ">
                        <label class="form-label">File Title</label>
                        <div>
                            <input type="text" name="title" class="form-control file-title" aria-describedby="" placeholder="Example Title">
                            <small class="form-hint">We'll never share your email with anyone else.</small>
                        </div>
                    </div>
                    <div class="form-group mb-3 ">
                        <label class="form-label">Upload File</label>
                        <div>
                            <div class="upload-zone"  id="uploadzone">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text"><span>Drag and drop</span> file here or <span>browse</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="form-group mb-3 ">
                        <label class="form-label">Join To</label>
                        <div>
                            <select name="users" id="advance-select" class="form-select">
                                <option value="" >Select Exit File</option>
                                <option value="1">Chuck Tesla</option>
                                <option value="2">Elon Musk</option>
                            </select>
                            <small class="form-hint">We'll never share your email with anyone else.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Your Cloud Drives</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3 ">
                        <label class="form-label">Stage 1</label>
                        <div class="form-selectgroup">
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="name" value="HTML" class="form-selectgroup-input" checked="">
                                <span class="form-selectgroup-label"><img src="<?php imgUri('cdrives/gdrive.png'); ?>" height="22" alt=""> Google Drive 1</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="name" value="CSS" class="form-selectgroup-input" checked>
                                <span class="form-selectgroup-label"><img src="<?php imgUri('cdrives/dropbox.png'); ?>" height="22" alt=""> Dropbox 1</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="name" value="PHP" class="form-selectgroup-input" checked>
                                <span class="form-selectgroup-label"><img src="<?php imgUri('cdrives/onedrive.png'); ?>" height="22" alt=""> OneDrive 1</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="name" value="JavaScript" class="form-selectgroup-input">
                                <span class="form-selectgroup-label"><img src="<?php imgUri('cdrives/default.png'); ?>" height="22" alt=""> MediaFire 1</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">Upload</button>
                </div>
            </div>
        </div>
    </div>
</form>