<!doctype html>

<html lang="en">
  <head>
  	<title>Таблица с пользователями GitHub</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/style.css">

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">Таблица с пользователями GitHub</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="table-wrap">
						<table class="table table-bordered table-dark table-hover">
						  <thead>
						    <tr>
						      	<th>Номер</th>
						      	<th>Логин</th>
						      	<th> &nbsp  &nbsp  &nbsp  &nbsp  &nbsp  &nbsp  &nbsp &nbsp Профили</th>
								<th>Коммитов за последние 30 дней</th>

						    </tr>
						  </thead>
						  <tbody>
						  		<?php require 'login_script.php';?>
						  </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>

	</body>
</html>

