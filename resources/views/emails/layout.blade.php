<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link href="https://fonts.googleapis.com/css?family=Kreon:300,400,700" rel="stylesheet" type="text/css">
		<style type="text/css">
			body {
				width: 100%;
				background-color: #ffffff;
				margin: 0;
				padding: 0;
				-webkit-font-smoothing: antialiased;
				-webkit-text-size-adjust: none;
				-ms-text-size-adjust: none;
				font-family: "Kreon", serif;
				font-size: 1rem;
				font-weight: 400;
				line-height: 1.5;
				color: #212529;
			}
			html {
				width: 100%;
			}
			table td {
				border-collapse: collapse;
			}
			#logo {
				position: absolute;
				display: block !important;
				width: 76px;
				margin-left: 310px;
				background: black;
				border-radius: 0 0 100% 100%;
				padding: 12px;
				top: 0;
			}
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
				body {
					width: auto !important;
					font-size: 14px;
				}
				table[class~="tableWrapperTemplateWidth"] {
					width: 100% !important;
					min-width: 100% !important;
				}
				table[class~="tableTemplateWidth"] {
					width: 320px !important;
				}
				*[class~="onlyForSmall"] {
					display: block !important;
					width: auto !important;
					max-height: inherit !important;
					overflow: visible !important;
				}
				*[class~="hideForSmall"] {
					width: 0;
					display: none !important;
				}
				table[class~="table2Cols"] *[class~="splitOnSmall"] {
					width: 100% !important;
					clear: left !important;
				}
			}
		</style>
	</head>
	<body style="-webkit-font-smoothing:antialiased;width:100%!important;background:#ffffff;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;margin:0;padding:0;" lang="@{{ config('app.locale') }}">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="width:100% !important;" >
			<tr>
				<td width="100%" valign="top" bgcolor="#ffffff">
					<table class="tableWrapperTemplateWidth" width="720" border="0" cellpadding="0" cellspacing="0" align="center" style="width:720px;min-width:720px;margin:0 auto;position:relative">
						<tr>
							<td valign="top">
								<table class="tableTemplateWidth" width="720" style="width:720px;" border="0" cellpadding="0" cellspacing="0" align="center" style="position:relative">
									<tr>
										<td width="100%">
                                                                                        <table class="tableTemplateWidth" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom:45px">
                                                                                                <tr>
                                                                                                        <td style="background:url({{ asset('/images/gifts-1-2.jpg') }});height:64px;width:100%;position:absolute;z-index:0"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                        <td style="background:rgba(33,45,57,0.6);height:64px;position:relative;z-index:1">
														<div id="logo"><img src="{{ asset('/images/logo.png') }}" height="75" style="width:75px"/></div>
													</td>
                                                                                                </tr>
                                                                                        </table>

											<table class="tableTemplateWidth" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom:10px">
												<tr>
													<td>@yield('main')</td>
												</tr>
											</table>

											<table class="tableTemplateWidth" width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td style="background:url({{ asset('/images/mail-bottom-bg.png') }});height:12px"></td>
												</tr>
												<tr>
													<td style="background:red">&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
