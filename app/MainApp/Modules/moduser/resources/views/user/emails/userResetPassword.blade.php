@extends('layouts.email.auth.app')

@section('content')
<tr>
	<td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
			<p>Hai {{$name}},</p>
			<p>Anda baru saja melakukan permintaan untuk melakukan reset password untuk akun Billionaire Store Anda. Klik tombol dibawah ini untuk melakukan reset password dan masukan password baru anda.</p>
		</div>
	</td>
</tr>
<tr>
	<td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<a href="{{$resetPasswordUrl}}" target="_blank" style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;font-size: 15px; line-height: 120%;color: #ffffff; background-color: #DC2F2F; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; max-width: 130px; width: auto; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding: 15px 25px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;mso-border-alt: none">
			Reset Password
		</a>
	</td>
</tr>
<tr>
	<td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
		<div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
			<p>Jika Anda tidak melakukan permintaan untuk mereset password, silahkan abaikan email ini atau hubungi CS Billionaire Store untuk melakukan pengecekan. Link reset password ini hanya berlaku sampai 1 jam kedepan.</p><br>
			<p>Jika Anda bermasalah dengan klik tombol Reset Password, copy dan paste URL di bawah ini di web browser Anda.</p>
			<p><a style="color:#2F67F6" href="{{$resetPasswordUrl}}" target="_blank">{{$resetPasswordUrl}}</a></p>
		</div>
	</td>
</tr>
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
        <div style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;">
            Salam Sukses,<br><br> Billionaire Store<br>
            <a href="{{ route('home') }}" style="color:#2F67F6">{{ route('home') }}</a>
        </div>
    </td>
</tr>
@endsection