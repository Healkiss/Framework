<?php
	$datas = $this->module->getCore()->getDatas();
	$userDatas = $this->getDatasModule('User');
	$connected = $userDatas['connected'];
	$Joueur = $userDatas['player'];
?>
<div id=menu>
	<div class=sub_menu>
		<?php echo "<a href='".$this->baseURL."/Royaume'>Royaume</a>";?>
	</div>
	<div class=sub_menu>
		<?php
		    if($connected){
				echo "Vous etes connecté en tant que <b>".$Joueur->pseudo."</b>";
				echo "<form method='post' action=''>";
				echo "<input type='hidden' name='Unlogin' value='1'/>";
				echo "<input type='submit' value='Déconnexion'/>";
				echo "</form>";
				echo "<a class='lienMenu rouge' href='profil.php?joueur=$pseudoJoueur'>Mon profil</a>";
				echo "<a class='lienMenu rouge' href='./messagerie.php'>";
				echo "Messagerie <br/>";
				echo "</a>";
			}else{
				echo $this->module->getTemplate()->showModule('login','user',$data);
			}
		?>
	</div>
</div>