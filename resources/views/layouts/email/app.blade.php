<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<title></title>
	<!--[if !mso]><!-- -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!--<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">#outlook a{padding:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}.ExternalClass *{line-height:100%}body{margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}img{border:0;height:auto;line-height:100%;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}p{display:block;margin:13px 0}</style>
	<!--[if !mso]><!-->
	<style type="text/css">@media only screen and (max-width:480px){@-ms-viewport{width:320px}@viewport{width:320px}}</style>
	<!--<![endif]-->
	<!--[if mso]>
        <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
	<!--[if lte mso 11]>
        <style type="text/css">
          .outlook-group-fix { width:100% !important; }
        </style>
        <![endif]-->
	<style type="text/css">@media only screen and (min-width:480px){.mj-column-per-100{width:100%!important}}</style>
</head>

<body style="background-color:#f9f9f9;">
	<div style="background-color:#f9f9f9;">

		<!--[if mso | IE]>
      <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]-->

		<div style="background:#f9f9f9;background-color:#f9f9f9;Margin:0px auto;max-width:600px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9f9;background-color:#f9f9f9;width:100%;">
				<tbody>
					<tr>
						<td style="border-bottom:#DC2F2F solid 5px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
							<!--[if mso | IE]>
								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
									<tr>
					
									</tr>      
								</table>
							<![endif]-->
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!--[if mso | IE]>
          </td>
        </tr>
      </table>
      
      <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]-->

		<div style="background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff;background-color:#fff;width:100%;">
				<tbody>
					<tr>
						<td style="border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
							<!--[if mso | IE]>
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">                
        <tr>      
            <td style="vertical-align:bottom;width:600px;" >
          <![endif]-->
							<div class="mj-column-per-100 outlook-group-fix"style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">
								<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:bottom;" width="100%">
									@include('layouts.email.header')
									@yield('content')
								</table>
							</div>
							<!--[if mso | IE]>
            </td>          
        </tr>      
                  </table>
                <![endif]-->
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!--[if mso | IE]>
          </td>
        </tr>
      </table>
      
      <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]-->
	  	@include('layouts.email.footer')
		<!--[if mso | IE]>
          </td>
        </tr>
      </table>
      <![endif]-->
	</div>
</body>

</html>