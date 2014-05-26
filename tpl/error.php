<!DOCTYPE html>
<html>
	<head>
		<title>Error. System throwed an exception!</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1 class="block">System exception #<?php echo $code ?></h1>
		<h3 class="block"><?php echo $message ?></h3>
		<div class="block info">
			<b>In file:</b> <?php echo $file ?><br>
			<b>On line:</b> <?php echo $line ?><br>
			<hr>
			<div id="trace">
				<?php 
					foreach ($trace as $id => $node) {
						echo $id . ' -> In file <span>'. $node['file'] . '</span> on line <span>['. $node['line'] .']</span>. Function <span>'. $node['function'] .'</span> of class<span> '. $node['class'] .'</span>. Args: <span>'. implode(';', $node['args']) .'</span><br>';
					}
				?>
			</div>
		</div>
	</body>
</html>