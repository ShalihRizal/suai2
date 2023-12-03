<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>401-Unauthorize</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{asset('css')}}/simplebar.css">
    <link rel="shortcut icon" href="{{asset('img')}}/logo.jpg">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{asset('css')}}/feather.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('css')}}/daterangepicker.css">
    <!-- App CSS -->
    {{-- <link rel="stylesheet" href="{{asset('css')}}/app-light.css" id="lightTheme"> --}}
    <link rel="stylesheet" href="{{asset('css')}}/app-dark.css" id="darkTheme">
    {{-- {!! ReCaptcha::htmlScriptTagJsApi() !!} --}}
	


</head>
<body class="light" style="background-image: url('{{ asset('img/401.png') }}'); background-size: cover;">
    <div class="mt-10">
        <div class="row justify-content-center">
          <div class="card">
              <div class="card-header">
                <h3 style="text-align: center">Anda Tidak Memiliki Akses ke Menu Ini</h3>
                <center>
                    <a href="/beranda" class="btn btn-primary btn-lg"> Kembali Ke Dashboard </a>
                </center>
                  
              </div>
          </div>
        </div>
      </div>

    {{-- <div class="card">
		<div class="card-body">
			<h3 style="text-align: center">Anda Tidak Memiliki Akses ke Menu Ini</h3>
			<center>
				<a href="/beranda" class="btn btn-primary btn-lg"> Kembali Ke Dashboard </a>
			</center>
		</div>
	</div> --}}

</body>

</html>
