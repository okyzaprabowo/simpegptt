<?php
if(!isset($maxEselon))$maxEselon=3;
if(isset($data[$induk]))foreach($data[$induk] as $val){
$hasChildren = isset($data[$val['id']]);
?>
@if($val['eselon'] <= $maxEselon)
<option value="{{ $val['id'] }}" class="optionitem-{{$val['id']}}">{{ ($val['eselon']>1?str_repeat('|---',($val['eselon']-1)):'').' '.$val['nama'] }}</option>
@if($hasChildren)
    @include('instansi.selectnested',['data'=>$data,'induk' => $val['id'],'maxEselon' => $maxEselon])
@endif
@endif
<?php } ?>