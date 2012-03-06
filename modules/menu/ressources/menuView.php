<?php
	$connected = $data['user']['connected'];
?>
	<div id=menu>
		<div class=sub_menu>
			<?php echo "<a href='".$this->baseURL."/Royaume'>Royaume</a>";?>
		</div>
		<div class=sub_menu>
			<?php
			    if($data['user']['connected']){
					echo 'Bienvenue'.$data['user']['namePlayer'].'<br/>';
				}else{
					echo 'Veuillez vous identifier :<br/>';
					echo $this->module->getTemplate()->show('login','user',$data);
				}
			?>
		</div>
	</div>