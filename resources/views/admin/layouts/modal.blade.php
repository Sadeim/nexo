<div class="modal fade " tabindex="-1" id="online_courses_modal_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Upload File</h3>
            </div>

            <div class="modal-body">
                <p class="hint">You can Upload just Excel File</p>
                <div class="dropzone dz-clickable" id="kt_ecommerce_add_product_media">
                    <!--begin::Message-->
                    <div class="dz-message needsclick">
                        <!--begin::Icon-->
                        <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                        <!--end::Icon-->
                        <!--begin::Info-->
                        <div class="ms-4">
                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
                            <span class="fs-7 fw-semibold text-gray-400">Upload up to 10 files</span>
                        </div>
                        <!--end::Info-->
                    </div>
                </div>


                <div class="downloadFile">
                    <p class="hint">Download the form of the excel file to be uploaded</p>
                    <button class="btn btn-primary">Downlaod sample File</button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Upload File</button>
            </div>
        </div>
    </div>
</div>
@stack('modals')
