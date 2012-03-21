<?php
  set_time_limit(180);
  ignore_user_abort(true);
  //This array contains the definitions of every command.
  //The array key is the command name, shown in the dropdown
  // - Script needs to be a valid script in the script directory, 
  //   - for security sake, sudoers has been set up to only allow execution of scripts in that directory.
  // - Command is the first param, usually followed by a space, unless your concating a command.
  // - Variables are an array of the required feilds needed for each command, none are optional.
  //   - If you provide two values in the array, the second is the default value
  //     - If you provide an array for the default values, this will create a dropdown menu
  // - Message lets you define the success message after using the command.
  
  $commandList = array(
				'Broadcast'	=> array(
					'script' => 'post.sh',
					'command' => '',
					'variables' => array (
            array('Type', array('say','broadcast')),
            array('Message'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Give'	=> array(
					'script' => 'post.sh',
					'command' => 'give ',
					'variables' => array (          
            array ('Player'),
            array ('Item'),
            array ('Amount', '64'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Time' 		=> array(
					'script' => 'post.sh',
					'command' => 'time ',
					'variables' => array (
            array ('Time', array('sunrise','day','sunset','night')),
            array ('Where', array('ALL','world','world_nether','world_the_end')),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'TP' 		=> array(
					'script' => 'post.sh',
					'command' => 'tp ',
					'variables' => array (
            array ('Player'),
            array ('ToPlayer'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Kick' 		=> array(
					'script' => 'post.sh',
					'command' => 'kick ',
          'variables' => array (
            array ('Player'),
            array ('Reason'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Ban' 		=> array(
					'script' => 'post.sh',
					'command' => 'ban ',
					'variables' => array (
            array ('Player'),
            array ('Reason'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Unban' 	=> array(
					'script' => 'post.sh',
					'command' => 'unban ',
					'variables' => array (
            array ('Player'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
        'Mute' 	=> array(
					'script' => 'post.sh',
					'command' => 'mute ',
					'variables' => array (
            array ('Player'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'OP' 		=> array(
					'script' => 'post.sh',
					'command' => '',          
					'variables' => array (
            array ('Give?', array('op', 'deop')),
            array ('Player'),
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
        'GameMode' 		=> array(
					'script' => 'post.sh',
					'command' => 'gamemode ',
					'variables' => array (
            array ('Player'),
            array ('Give?', array('1', '0')),            
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'List' 		=> array(
					'script' => 'post.sh',
					'command' => 'list',
					'variables' => array (
          ),
					'message' => 'Executed: /{cmd}{value}',
				),
				'Restart' 		=> array(
					'script' => 'restart.sh',
					'command' => '',
					'variables' => array (
            array ('SecondsDelay', array('15','30','60','120','180')),
          ),
					'message' => 'Triggered soft restart, 15 second warning.',
				),
				'FullRestart' 		=> array(
					'script' => 'start.sh',
					'command' => '',
					'variables' => array (
          ),
					'message' => 'Triggered hard restart. May cause rollback! Allow 60 seconds before connecting.<br />{output}',
				),
	);

	$cmd = @escapeshellcmd($_GET['cmd']);
	$selectedCmd = @escapeshellcmd($_GET['selectedcmd']);
	$inputBoxes = "";	
  //Default message on first loading the page.
	$message = '<span class="ok">Select a command from the box below</span>';
	$commandOptions = "";
	foreach ($commandList as $command => $data) {		
		$commandOptions .= '<option value="' . $command . '"' . ($command == $cmd ? " selected" : ""). '>' . $command . '</option>';
	}	
	//Was a valid command selected
	if (isset($commandList[$cmd])) {
		if ($selectedCmd == $cmd) {
			$continue = 1;
			$message = "";
			foreach ($commandList[$cmd]['variables'] as $index => $variables) {			
				if ((!isset($_GET['value'][$index])) || ($_GET['value'][$index] == "")) {				
					$message .= '<span class="error">You need to give a value for ' . $variables['0'] . '</span><br />';
					$continue = 0;
				}
        elseif (isset($_GET['value'][$index]) && is_array($variables['1']) && !in_array($_GET['value'][$index],$variables['1'])) {
          //Invalid option in the value dropdown? Someone is form hacking!
          $message .= '<span class="error">You need to give a valid value for ' . $variables['0'] . '</span><br />';
					$continue = 0;
        }
			}
      //No errors found in the given variables.
			if ($continue) {
        $value = "";
        if (isset($_GET['value'])) {
          $value = implode(' ',$_GET['value']);
        }
        
				$script = $commandList[$cmd]['script'];
				$command = $commandList[$cmd]['command'];
        
        //The command needed to be executed, sudo is required if the httpd user doesnt match the screen user.
        //It is recommended you create a sudoers jail to just execute scripts in this folder.
				$exec = 'sudo -u root /root/scripts/'. $script . ' "' . $command . escapeshellcmd($value) . '"';
				 
				$output = str_replace("\n", ' - ', shell_exec($exec));
				$message = '<span class="ok">' . $commandList[$cmd]['message'] . '</span>';
				$message = str_replace(array('{cmd}','{value}','{output}'),array($command,$value,$output),$message);
			}		
		} else {
      //Default message on selecting a command.
			$message = '<span class="warn">Complete the required fields, or choose a different command.  Don\'t forget to hit submit!</span>';
		}
    //Submitted/not-subbmited with error/not-error, we need to build the form anyway.
		foreach ($commandList[$cmd]['variables'] as $index => $variables) {
			$inputBoxes .= '<span class="variables">'.$variables['0'] . ': </span>';
      $varible = @$variables['1'];
      if (is_array($varible)) {
        $inputBoxes .= ' <select name="value[]">';
      	foreach ($varible as $options) {		
          $inputBoxes .= '<option value="' . $options . '">' . $options . '</option>';
        }	
        $inputBoxes .= '</select>';
      }
      else {
        if (!is_string($varible)) { $varible = ""; }
        $inputBoxes .= ' <input name="value[]" '.($index == 0 ? 'autofocus' : '').' value="'.$varible.'" />';
      }
      $inputBoxes .= '<br />';					
		}
	} elseif ($cmd) {
    //If you get here, you're being a hacker.
		$message = '<span class="error">Invalid Command</span>';
	}
?>
<html>
	<head>
		<title>Command me!</title>
		<style>
    h2 { margin: 5px 5px 5px 10px; }
    h3 { margin: 5px 5px 5px 10px; }
		span.variables{display:inline-block;width:100px;}
		span.error{color:red}
		span.warn{color:orange}
		span.ok{color:green}
		input,select{display:inline-block;width:150px;}
		</style>
	</head>
	<body>
		<h2>Perform a command on the server...</h2>
    <?php include('nav.php'); ?>
		<div style="height:220px; padding: 5px">
			<div style="height:80px; width:50%; padding-left: 5px; border: 1px dashed gray">
				<p><?php print $message; ?></p>
			</div>
			<form action="./" method="GET" style="height:130px; width:50%; padding-left: 5px; border: 1px dashed gray; border-top: 0px">
				<input type="hidden" name="selectedcmd" value="<?php echo $cmd; ?>" />
				<span class="variables">Command:</span>
				<select name="cmd" onchange="this.form.submit();"><?php print $commandOptions; ?></select><br />
				<?php print $inputBoxes; ?>
				<input type="submit"/>
			</form>
		</div>
		<iframe src="tail.php?tail=25&lite=1" width="95%" height="540px"/>
	</body>
</html>
//copyright 2012 Cory Huckaby.
//Do not distribute.