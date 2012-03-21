<?php
$tail = isset($_GET['tail']) ? trim($_GET['tail']) : '';
$lite = isset($_GET['lite']) ? true : false;
if (!is_numeric($tail)) { $tail = 40; }
$lines = file('server.log');
?>
<html>
<head>
<title>Server log</title>
<style type="text/css">
body {
font-family: "Courier New",
             Consolas,
             "Bitstream Vera Sans Mono",
             "DejaVu Sans Mono",
             monospace;
}             
</style>
</head>
<body>

<?php
if (!$lite) {
include('nav.php');
?>      
      <form>
  			<span>Number of lines to show: </span>
        <input type="text" name="tail" value="<?php echo $tail; ?>" />				
				<input type="submit"/>
			</form>
<?php
}

$lines = array_slice($lines, (-1 * $tail * 5));

foreach($lines as $key => $value) {
	if($value == "" || $value == " " || is_null($value) || strpos($value,'Did the system time change') !== false) {
		unset($lines[$key]);
	}
}

$lines = array_slice($lines, (-1 * $tail));

foreach($lines as $line) {
$line = '<span>' . $line . '</span>';
$srem = array('[0m','[31m','[32m','[33m','[34m','[35m','[36m','[37m');
$swith = array(
			'</span><span style="color:gray">',
			'</span><span style="color:red">',
			'</span><span style="color:green">',
			'</span><span style="color:orange">',
			'</span><span style="color:blue">',
			'</span><span style="color:purple">',
			'</span><span style="color:teal">',
			'</span><span style="color:black">',
			);
  $line = str_replace($srem, $swith, $line);
  echo $line . '<br/>';
}
?>
</body>
</html>