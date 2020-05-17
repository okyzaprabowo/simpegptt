@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/fullcalendar/fullcalendar.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    <style type="text/css">
     .ui-timepicker-wrapper {
        z-index: 3500 !important;
    }
    .importantRule{
            z-index:1600 !important;
         } 
         .default-style .datepicker-dropdown{
            z-index:9999 !important;
         } 
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/fullcalendar/locale-all.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/js/ui_fullcalendar.js') }}"></script> -->
    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/hari_libur';
        var vueListHariLibur = new Vue({
            el: '#vueListHariLibur',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "id",
                sortDesc: false,
                searchString: "",
                curPage: 1,
                perPage: 10,
                perPageOption: [10,20,50,100],
                
                loadParams: {
                    params: {}
                },
 
                data: {
                   
                },
                
                isAdd: true,
                form: {
                    id:0,
                    title: '',
                    start: '',
                    end: '',
                    // deskripsi: ''
                },
                formEmpty: {
                    id:0,
                    title: '',
                    start: '',
                    end: '',
                    // deskripsi: ''
                }
            },
            created: function() {
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            watch: {
              
            },
            methods: {
                showForm(isAdd=true,id=0) {                  
                    this.isAdd = isAdd;
                    if(!isAdd){       
                        this.formTitle = 'Edit Data';
                        axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;
                                $('#fullcalendar-default-view').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        $('#fullcalendar-default-view').modal('show');
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    var data = this.form;
                    data.start =  moment($("#start").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                    data.end = moment($("#end").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                    if(this.isAdd){
                        this.saveData(data);
                    }else{
                        this.updateData(data.id, data);
                    }
                },
                saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.post(apiPath,data).then((res)=>{
                        
                        $('#fullcalendar-default-view-modal').modal('hide');
                        showAlert({text: "Data saved"}); 
                        //this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                        //defaultCalendar.render();
                        var eventData = {
                            title: data.title,
                            start: data.start,
                            end: data.end,
                            id:data.id
                            //className: className
                            }
                            defaultCalendar.addEvent(eventData);
                            // console.log(res);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed",type: "warning"});
                    });
                },
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#fullcalendar-default-view').modal('hide');
                        // this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                        defaultCalendar.render();
                    }).catch((res)=>{
                        showAlert({text: "Update data failed",type: "warning"});
                    });
                },
                deleteItem(id) {                    
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Penghapusan",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk:()=>{
                            axios.delete(apiPath + "/" + id)
                                .then((res)=>{
                                    var event = defaultCalendar.getEventById( id );
                                    showAlert({text: "Data hari libur '" + event.title + "' Berhasil dihapus."});                                    
                                    this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                                    event.remove();
                                }).catch((res)=>{
                                    showAlert({text: "Hapus gagal : " + res.message,type: "warning"});
                                });
                        }
                    });
                },
                loadList(curPage,q='',orderBy=false,sortDesc=false) {
                    var offset = (this.perPage * (curPage-1));
                    
                    this.loadParams.params.limit = this.perPage;
                    this.loadParams.params.offset = offset;

                    if(q!=''){
                        this.loadParams.params.q = q;
                    }else{
                        delete this.loadParams.params.q;
                    }
                    if(orderBy!=false){
                        this.loadParams.params.orderBy = orderBy;
                        this.loadParams.params.orderType = sortDesc?'DESC':'ASC';
                    }
                    axios.get(apiPath , this.loadParams)
                        .then((res)=>{
                            this.data = res.data.data;
                        }).catch((res)=>{
                            showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                        });
                }
            }
        });

        var today = new Date();
        var y = today.getFullYear();
        var m = today.getMonth();
        var d = today.getDate();

      

        // Default view
        // color classes: [ fc-event-success | fc-event-info | fc-event-warning | fc-event-danger | fc-event-dark ]
        var defaultCalendar = new Calendar($('#fullcalendar-default-view')[0], {
            plugins: [
            calendarPlugins.bootstrap,
            calendarPlugins.dayGrid,
            calendarPlugins.timeGrid,
            calendarPlugins.interaction
            ],
            dir: $('html').attr('dir') || 'ltr',

            // Bootstrap styling
            themeSystem: 'bootstrap',
            bootstrapFontAwesome: {
            close: ' ion ion-md-close',
            prev: ' ion ion-ios-arrow-back scaleX--1-rtl',
            next: ' ion ion-ios-arrow-forward scaleX--1-rtl',
            prevYear: ' ion ion-ios-arrow-dropleft-circle scaleX--1-rtl',
            nextYear: ' ion ion-ios-arrow-dropright-circle scaleX--1-rtl'
            },
            locale: 'id',
            header: {
            left: 'dayGridMonth,timeGridWeek,timeGridDay',
            center: 'title',
            right: 'prev,next today'
            },
            contentHeight: 600,   
            defaultDate: today,
            navLinks: true, // can click day/week names to navigate views
            selectable: true,
            weekNumbers: true, // Show week numbers
            nowIndicator: true, // Show "now" indicator
            firstDay: 1, // Set "Monday" as start of a week
            businessHours: {
            dow: [1, 2, 3, 4, 5], // Monday - Friday
            start: '08:00',
            end: '16:00',
            },
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: {!!json_encode($data)!!},
            allDay:true,
            views: {
            dayGrid: {
                eventLimit: 5
            }
            },

            select: function (selectionData) {
            $('#fullcalendar-default-view-modal')
                .on('shown.bs.modal', function() {
                //$(this).find('input[type="text"]').trigger('focus');
                    vueListHariLibur.form.start = moment(selectionData.startStr,'YYYY-MM-DD').format('DD-MM-YYYY');
                    vueListHariLibur.form.end =  moment(selectionData.endStr,'YYYY-MM-DD').format('DD-MM-YYYY');//selectionData.startStr;
                })
                .on('hidden.bs.modal', function() {
                $(this)
                    .off('shown.bs.modal hidden.bs.modal submit')
                    .find('input[type="text"], select').val('');
                defaultCalendar.unselect();
                })
                .on('submit', function(e) {
                e.preventDefault();
                var title = $(this).find('input[type="text"]').val();
                var className = $(this).find('select').val() || null;

                if (title) {
                    var eventData = {
                    title: title,
                    start: selectionData.startStr,
                    end: selectionData.endStr,
                    className: className
                    }
                    defaultCalendar.addEvent(eventData);
                }

                $(this).modal('hide');
                })
                .modal('show');
            },

            eventClick: function(calEvent) {
                // console.log(calEvent);
                // alert('Event: ' + calEvent.event.title);
                 if (confirm('Hapus hari libur '+ calEvent.event.title+'?')){
                    vueListHariLibur.deleteItem(calEvent.event.id);
                 }

            }
        });
        defaultCalendar.render();
        // defaultCalendar.setOption('locale', 'id'); 
        
        $('.periode').datepicker({
            orientation:  'auto left',
            autoclose:true,
            format: 'dd-mm-yyyy',
                /*{
                toDisplay : function(date,format,language){
                    date.format = 'dd-mm-yyyy';
                    date.languange = 'id';
                    return date;
                },
                toValue : function(date,format,language){
                    date.format = 'yyyy-mm=dd';
                    date.languange = 'id';
                    return date;
                }
            }, */
            zIndexOffset:9999
        });
        $(document).ready(function(){
            $('#fullcalendar-default-view-modal').on('shown.bs.modal', function() {
            //    $('.daterangepicker').css('z-index','1600','important');
              // $('#daterange-2').addClass('importantRule');
              var origStyleContent = $('.datepicker').attr('style');
          //  origStyleContent = 'top:0px !important;right:auto;left:428px;'
                $('.datepicker').attr('style', origStyleContent + ';z-index:9999 !important;top:0px !important;');
            }); 
        });
    </script>
@endsection


@section('content')
<div id="vueListHariLibur">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Setup /</span> Hari Libur</div>
        <!-- <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Hari Libur
        </div> -->
    </h4>
    @include("alert")
    <div class="card">        
        
        <div class="card-body"> 
            <div id='fullcalendar-default-view'></div>      
        </div>

         <!-- Event modal -->
    <form class="modal modal-top fade" id="fullcalendar-default-view-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hari Libur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" v-model="form.title" class="form-control">
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label class="form-label">Dari</label>
                            <input type="text" id="start" name="start" v-model="form.start" class="form-control periode importantRule"
                            onchange="vueListHariLibur.form.start = this.value">
                        </div>
                        <div class="form-group col">
                            <label class="form-label">Sampai</label>
                            <input type="text" id="end"  name="end" v-model="form.end" class="form-control periode importantRule"
                            onchange="vueListHariLibur.form.end = this.value">
                        </div>
                    </div>
                <!--    <div class="form-group">
                        <label class="form-label">Warna</label>
                        <select class="custom-select">
                            <option value="" selected>Abu</option>
                            <option value="fc-event-success">Hijau</option>
                            <option value="fc-event-info">Biru</option>
                            <option value="fc-event-warning">Orange</option>
                            <option value="fc-event-danger">Merah</option>
                            <option value="fc-event-dark">Hitam</option>
                        </select>
                    </div>-->
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="formSubmitted">Save</button>
                </div>
            </div>
        </div>
    </form>
    <!-- / Event modal -->

    </div>
</div>
@endsection