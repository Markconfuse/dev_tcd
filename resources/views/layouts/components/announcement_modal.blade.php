<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content"
            style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">

            <div class="row no-gutters" style="min-height: 500px; height: 60vh;">
                <!-- Left Side - 40% -->
                <div class="col-md-5 d-flex flex-column bg-white" style="padding: 1rem 1.5rem 1rem 1.5rem;">

                    <!-- Mobile Close Button -->
                    <button type="button" class="close d-md-none position-absolute" data-dismiss="modal"
                        aria-label="Close" style="top: 1rem; right: 1rem; z-index: 10;">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h2 class="font-weight-bold mb-2 text-dark" id="annTitle"
                        style="font-size: 1.2rem; line-height: 1.2;">Announcement Title</h2>
                    <div class="d-flex align-items-center mb-4 text-muted" style="font-size: 0.9rem;">
                        <i class="far fa-calendar-alt mr-2"></i>
                        <span id="annDate">Not yet released</span>
                    </div>

                    <div class="flex-grow-1 overflow-auto mb-3 pr-2" id="annContent" style="font-size: 13px; color: #666; max-height: 40vh;">
                        <!-- Content -->
                    </div>
                    
                    <div id="annAttachmentsContainer" class="mb-3 w-100" style="display: none;">
                        <!-- Attachments will be injected here -->
                    </div>
                    
                    <button class="btn btn-primary btn-block font-weight-bold mt-auto" id="btnUnderstand" data-id="" style="border-radius: 8px; padding: 12px;">I Understand</button>
                </div>

                <!-- Right Side - 60% -->
                <div class="col-md-7 d-flex align-items-center justify-content-center position-relative bg-light">
                    <!-- Desktop Close Button -->
                    <button type="button"
                        class="d-none d-md-flex position-absolute align-items-center justify-content-center"
                        data-dismiss="modal" aria-label="Close"
                        style="top: 1rem; right: 1rem; background: rgba(255,255,255,0.7); border: none; border-radius: 50%; width: 32px; height: 32px; z-index: 10; cursor: pointer; outline: none; transition: background 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,1)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.7)'">
                        <i class="fas fa-times text-dark"></i>
                    </button>

                    <div id="annImageContainer" class="w-100 h-100 position-absolute" style="top: 0; left: 0;">
                        <!-- Image or placeholder inserted here -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>