<?php
if(isset($data[$induk]))foreach($data[$induk] as $val){
$hasChildren = isset($data[$val['id']]);
?>
<div class="list-item" id="accordion{{$val['id']}}">                 
    <div class="row heading" id="heading-{{$val['id']}}">
        <h5 class="col-10 mb-0 pb-2">
            <a class="item-header collapsed {{ $hasChildren?'':' no-child' }}" role="button" data-toggle="collapse" href="#collapse-{{$val['id']}}" aria-expanded="{{ $val['eselon'] == 1?'true':'false' }}" aria-controls="collapse-{{$val['id']}}">
                {{ $val['nama'] }}
            </a>                                
        </h5>
        <div class="col-2 text-right">
            @if(\UserAuth::hasAccess('Master.instansi','c') && $val['eselon'] < 4)
            <div @click="showForm(true,{{$val['id']}})" class="btn btn-xs btn-primary icon-btn md-btn-flat" title="Tambah Sub">
                <span class="ion ion-md-add"></span>
            </div>
            @endif

            @if(\UserAuth::hasAccess('Master.instansi','u'))
            <div @click="showForm(false,{{$val['id']}})" class="btn btn-xs btn-success icon-btn md-btn-flat" title="Edit">
                <span class="ion ion-md-create"></span>
            </div>
            @endif
            
            @if(\UserAuth::hasAccess('Master.instansi','d') && !$hasChildren && $val['eselon'] != 1)
            <div @click="deleteItem({{$val['id']}})" class="btn btn-danger btn-xs icon-btn md-btn-flat" title="Delete">
                <span class="ion ion-md-close"></span>
            </div>
            @endif
        </div>
    </div>
    @if($hasChildren)
    <div id="collapse-{{$val['id']}}" class="collapse {{ $val['eselon'] == 1?'show':'' }}" data-parent="#accordion{{$val['id']}}" aria-labelledby="heading-{{$val['id']}}">    
        @include('instansi.nestedlist',['data'=>$data,'induk' => $val['id']])
    </div>
    @endif
</div>
<?php } ?>