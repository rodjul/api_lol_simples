<?php
require_once('model/api_lol.php');

$lol = new ApiLol();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Current Game</title>
		<meta charset='utf-8' />
		<style>
			.center{
				display:flex;
				align-items:center;
				justify-content:center;
			}
			.center-items{
				max-width:100%;
			}
			.team-blue{
				list-style:none;
				max-width:50%;
				float:left;
			}
			.team-blue p, .team-red p{
				font-size:19px;
			}
				
			.team-red{
				list-style:none;
				max-width:50%;
				float:right;
			}
		</style>
	</head>
	<body class="center">
		
		<section class="center-items">
		<form action="index.php" method="get">
			<input type="text" name="nome" value="" placeholder="Nickname" />
			<input type="submit" value="Buscar" name="submit"/>
		</form>
<?php
if(isset($_GET['submit'])){
	$lol->Main();
}
?>
		</section>
		
	</body>
</html>