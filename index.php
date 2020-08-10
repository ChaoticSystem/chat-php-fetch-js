<?php

	if(isset($_SESSION)){
		session_start();
	}
	
	
	
	$users = [
		1 => ['id' => 1, 'name' => 'Test1', 'value'=> '1'],
		2 => ['id' => 2, 'name' => 'Test2', 'value'=> '2'],
		3 => ['id' => 3, 'name' => 'Test3', 'value'=> '3'],
	];
	
	
		try{
		$user = $_GET['user'];
	}catch(exception $ex){}
	
	try{
		unset($users[$user]);
	}catch(exception $ex){}
	
	$filename = 'text'.$user.'.txt';
	if(!file_exists($filename)){
		$open = fopen($filename, 'w');
		fclose($open);
	}
	
	$_SESSION['id'] = $user;
	
	
	
	
	if(isset($_POST['read'])){
		$users = $_SESSION['id'];
		$filename = 'text'.$users.'.txt';
		set_time_limit(0);
		$result = ['users' => $users];
		$text = null;
		while(true){
			try{
				$text = file_get_contents($filename);
			}catch(exception $ex){}
			if(!empty($text)){
				$result['text'] = $text;
				file_put_contents($filename, '');
				break;
			}
			sleep(1);
		}
		header('content-type: application/json');
		die(json_encode($result));
	}
	
	if(isset($_POST['write'])){
		$users = $_SESSION['id'];
		$userTo = $_POST['userto'];
		$filename = 'text'.$userTo.'.txt';
		

		$result = ['ok' => false, 'user' => $users];
		$message = $_POST['text'];
		try{
			file_put_contents($filename, $message);
			$result['ok'] = true;
		}catch(exception $ex){}
		header('content-type: application/json');
		die(json_encode($result));
	}
	
	//$user = 1;
	/*$users = [
		1 => ['id' => 1, 'name' => 'Test1'],
		2 => ['id' => 2, 'name' => 'Test2'],
		3 => ['id' => 3, 'name' => 'Test3'],
	];
	*/
echo '<h1>'.$_SESSION['id'].'</h1>'
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat</title>
	<meta charset="utf-8">
	<style>
		* {
			margin: 0;
			padding: 0;
			outline: none;
		}
		#chat, #sendInputs {
			width: 100%;
			max-width: 360px;
			border: 4px solid green;
			box-sizing: border-box;
		}
		#chat {
			min-height: 480px;
		}
		#input {
			width: 100%;
			height: 36px;
			line-height: 36px;
		}
		#send {
			text-align: center;
			margin: 0 auto;
			display: block;
			height: 36px;
			width: 64px;
		}
	</style>
</head>
<body>
	<div id=chat>
		
	</div>
	<div id=sendInputs>
		<select id=userTo>
			<?php
				foreach($users as $id => $obj){
					echo '<option value="'.$obj['id'].'"               >';
			        echo $obj['name'];
					echo '</option>';
				}
			?>
		</select>
		<table style="width: 100%;">
			<tr>
				<td><input id=input type="text"></td>
				<td width=74><input id=send type="button" value="Enviar"></td>
			</tr>
		</table>
	</div>
	 <script>
        function read(){
            fetch('', {
                method: 'POST',
                headers: {
                    'content-type': 'application/x-www-form-urlencoded; charset=utf-8',
                },
                body: 'read='
            }).then(r => r.text()).then(result => {
                console.log('read:'+result);
                try{
                    var json = JSON.parse(result);
                    if(typeof json.text !== 'undefined' && json.text != ''){
                        var div = document.createElement('div');
                            div.className = 'mssg';
                            div.innerText = json.text;
                            chat.appendChild(div);
                    }
                }catch(ex){}
                read();
            });
        };
        function write(){
            var text = null;
            var usert = null;
            try{
                text = input.value.trim();
                usert = userTo.value.trim();
                
            }catch(ex){}
            console.log('text:', text, usert );
            if(text !=  null && text != ''){
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'content-type': 'application/x-www-form-urlencoded; charset=utf-8',
                    },
                    body: 'write=&text='+text+'&userto='+usert
                }).then(r => r.text()).then(result => {
                            
                    console.log('write:'+result);
        
                });
                input.value = '';
            } 
            else{
                input.focus();
            }
        };
        send.onclick = function(e){
            e.preventDefault();
            write();
        };
        read();
    </script>
	</body>
</html>
