@extends('layouts.email.app')

@section('content')
<tr>
	<td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
			<p>Hai {{$name}},</p>
			<p>Selamat Datang, Silahkan klik link di bawah untuk verifikasi akun email anda</p>
		</div>
	</td>
</tr>
<tr>
	<td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<a href="{{$verifyUrl}}" target="_blank" style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;font-size: 15px; line-height: 120%;color: #ffffff; background-color: #DC2F2F; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; max-width: 130px; width: auto; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding: 15px 25px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;mso-border-alt: none">
			Verifikasi Email
		</a>
	</td>
</tr>
<tr>
	<td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
			<p>Jika Anda bermasalah dengan klik tombol Verifikasi Email, copy dan paste URL di bawah ini di web browser Anda.</p>
			<p><a style="color:#2F67F6" href="{{$verifyUrl}}" target="_blank">{{$verifyUrl}}</a></p>
		</div>
	</td>
</tr>
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
        <div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;">
            Salam Sukses,<br><br> Aplikasi<br>
            <a href="#" style="color:#2F67F6">#</a>
        </div>
    </td>
</tr>
@endsection