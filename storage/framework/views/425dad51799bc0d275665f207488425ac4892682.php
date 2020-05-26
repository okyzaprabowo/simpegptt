
<?php $__env->startSection('scripts'); ?>
##parent-placeholder-16728d18790deb58b3b8c1df74f06e536b532695##
<script>
    var alertModalOnOk = function(){

    };
    var alertModalOnClose = function(){

    };    
    var _showAlert_position = {
        "top-left": "notifications-top-left",
        "top-center": "notifications-top-center",
        default: "notifications-default",
        "bottom-left": "notifications-bottom-left",
        "bottom-center": "notifications-bottom-center",
        "bottom-right": "notifications-bottom-right"
    };
    var _showAlert_type = {
        warning: "bg-warning text-body",
        success: "bg-success text-white",
        info: "bg-info text-white",
        danger: "bg-danger text-white",
        primary: "bg-primary text-white",
        secondary: "bg-secondary text-white",
        dark: "bg-dark text-white"
    }
    var showAlert = function(params) {
        
        if (!params.type) params.type = "info";
        if (!params.title) params.title = "Alert";
        if (!params.text) params.text = "Shome Warning";
        if (!params.styleType) params.styleType = "hover";
        if (!params.position) params.position = "top-center";
        
        if (_showAlert_type[params.type] == undefined)
            params.type = "info";
        if (_showAlert_position[params.position] == undefined)
            params.position = "top-center";

        //alert biasa
        if (params.styleType == "alert") {
            // this.store.commit("addAlert", {
            //     text: params.text,
            //     type: params.type
            // });
        } else if (params.styleType == "modal") {
            if (!params.onShow) params.onShow = null;
            if (!params.onCancel) params.onCancel = null;
            if (!params.onOk) params.onOk = null;
            if (!params.modalButtonCancel) params.modalButtonCancel = null;
            if (!params.modalButtonOk) params.modalButtonOk = null;

            // this.store.commit("setModal", {
            //     title: params.title,
            //     text: params.text,
            //     onShow: params.onShow,
            //     onClose: params.onClose,
            //     onOk: params.onOk,
            //     modalButtonCancel: params.modalButtonCancel,
            //     modalButtonOk: params.modalButtonOk
            // });

            if(typeof params.onOk == 'function') alertModalOnOk = params.onOk;
            if(typeof params.onClose == 'function') alertModalOnClose = params.onClose;

            $('#alertModalTitle').text(params.title);
            $('#alertModalBody').text(params.text);
            
            $('#alertModalClose').text(params.modalButtonCancel);
            $('#alertModalOk').text(params.modalButtonOk);

            $('#alertModal').modal('show');
        } else {
            toastr[params.type](params.text, params.title, {
                positionClass: 'toast-top-center',
                closeButton: true,//     $('#toastr-close-button').prop('checked'),
                progressBar:       false,//$('#toastr-progress-bar').prop('checked'),
                preventDuplicates: true,//$('#toastr-prevent-duplicates').prop('checked'),
                newestOnTop:       false,//$('#toastr-newest-on-top').prop('checked'),
                rtl:               $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl'
            });                

        }
        
    }
    
    $('#toastr-clear').on('click', function() {
        toastr.clear();
    });

    $('#alertModalClose').on('click',function(){
        alertModalOnClose();
        $('#alertModal').modal('hide');
    });
    $('#alertModalOk').on('click',function(){
        alertModalOnOk();
        $('#alertModal').modal('hide');
    });
</script>
<?php $__env->stopSection(); ?>

<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="alertModalBody">
                isi modal
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="alertModalClose">Close</button>
                <button type="button" class="btn btn-primary" id="alertModalOk">Save changes</button>
            </div>
        </div>
    </div>
</div><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/resources/views/alertModal.blade.php ENDPATH**/ ?>