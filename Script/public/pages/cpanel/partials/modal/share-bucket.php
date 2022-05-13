<div class="modal modal-blur fade " id="share-bucket-modal"   tabindex="-1" role="dialog" aria-hidden="true" data-loaded="0">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Bucket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body py-3">
                <div id="share-bucket-alert-wrap"></div>
                <div class="bucket-shared-info">
                    <div class="input-group mb-2" >
                        <input type="text" class="form-control bucket-link"   value=""  readonly  placeholder="" required>
                        <button class="btn btn-primary copy-bucket-shared-link">
                            Copy
                        </button>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-4 col-form-label">Select specific drive</label>
                        <div class="col">
                            <select class="form-select active-cloud-drive-list">
                                <option value="">-- select --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto share-bucket" data-share-status="unshared">Unshare file</button>
                <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" >Done</button>
            </div>
        </div>
    </div>
</div>