<?php
	$connected = $data['user']['connected'];
?>
	<div id=menu>
		<div class=sub_menu>
			<a href='<?php echo $this->baseURL?>/Royaume'>Royaume</a>
		</div>
		<div class=sub_menu>
		
			<?php if($connected){
				echo "<a href='".$this->baseURL."/Login'>Login</a>";
			}else{
				echo "<a href='".$this->baseURL."/Login'>Login</a>";
			}
			?>
		</div>
	</div>