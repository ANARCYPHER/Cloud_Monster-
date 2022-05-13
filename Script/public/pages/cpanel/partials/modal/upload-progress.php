<div class="modal modal-blur fade" id="upload-progress-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Processing ( <span class="nb-progress">0</span> %) </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body py-3">
                <div id="progress-alert-wrap"></div>
                <div class="waiting-resp-msg" style="display: none">
                    <h4 class="mr-auto">Waiting for server response. </h4>
                    <div class="spinner-border" role="status"></div>
                </div>
                <div class="progress-wrap">
                    <div class="progress progress-large">
                        <div class="progress-bar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                    <div class="d-flex mt-1">
                        <span class="mr-auto"><b>Time Remaining</b> : <span class="nb-time-remaining">0 sec</span> </span>
                        <span >  <span class="nb-uploaded">0 B</span> / <b class="nb-file-size">0 B</b> </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="nb-upload-speed">0 B/s</span>
                <button type="button" class="btn btn-danger ml-auto cancel-upload" >Cancel</button>
            </div>
        </div>
    </div>
</div>