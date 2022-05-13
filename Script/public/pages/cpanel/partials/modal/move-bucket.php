<div class="modal modal-blur fade" id="move-bucket-modal" tabindex="-1" role="dialog" aria-hidden="true" data-folder="0">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="new-folder-alert-wrap"></div>
                <ul class="tmp-folder-list list-group list-group-flush">
                    <li class="list-group-item"></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
                <button type="button" id="move-bucket" class="btn btn-primary" >Move</button>
            </div>
        </div>
    </div>
</div>