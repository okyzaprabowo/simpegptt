@extends('layouts.email.app')

@section('content')
<div style="color:#555555;line-height:180%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; padding-right: 0px; padding-left: 0px; padding-top: 0px; padding-bottom: 15px;">
    <div style="font-size:14px;line-height:22px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
        <h2 style="margin: 0;padding: 0 30px;">{!! $title !!}</h2>
    </div>
    <div style="font-size:14px;line-height:22px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
        {!! $body !!}
    </div>
</div>
@endsection