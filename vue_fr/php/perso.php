<div class="row" style="padding-bottom: 0; display: table; height:100%; width: 100%;">
	<div id="menu_perso" class="hidden-xs">
		<div class="row">
			<a href="?dashboard" <?php if(isset($_GET['dashboard'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-dashboard"></span>
			<br />
			Mon tableau de bord
			</a>
		</div>
		
		<div class="row">
			<a href="?clubs" <?php if(isset($_GET['clubs'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-flag"></span>
			<br />
			Mes clubs
			</a>
		</div>
		
		<div class="row">
			<a href="?messages" <?php if(isset($_GET['messages'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-envelope"></span>
			<br />
			Mes messages
			</a>
		</div>
		
		<div class="row">
			<a href="?demandes_de_location" <?php if(isset($_GET['demandes_de_location'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-check"></span>
			<br />
			Mes demandes de location
			</a>
		</div>
		
		<div class="row">
			<a href="?annonces" <?php if(isset($_GET['annonces'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-list-alt"></span>
			<br />
			Mes annonces
			</a>
		</div>
		
		<div class="row">
			<a href="?revenus" <?php if(isset($_GET['revenus'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-stats"></span>
			<br />
			Mes revenus
			</a>
		</div>
		
		<div class="row">
			<a href="?compte" <?php if(isset($_GET['compte'])) echo 'class="active"'; ?>>
			<span class="glyphicon glyphicon-wrench"></span>
			<br />
			Mon compte
			</a>
		</div>
	</div>
	
	<div style="display: table-cell; height:100%; vertical-align: middle;">
		<?php
		if(isset($_GET['dashboard']))
		{
			echo '<script type="text/javascript" src="zeroclipboard-2.2.0/dist/ZeroClipboard.min.js"></script><script type="text/javascript" src="' . $_SESSION['dossier_vue'] . '/js/perso_dashboard.js"></script>';
			
			require_once('perso_dashboard.php');
		}
		else if(isset($_GET['clubs']))
			require_once('perso_clubs.php');
		else if(isset($_GET['messages']))
		{
			echo '<script type="text/javascript" src="bootstrap-chosen-master/chosen.jquery.js"></script>';
			
			if(!empty($infos['conversation']) && !empty($infos['messages']))
			{
				$conversation = $infos['conversation'];
				$messages = $infos['messages'];
				
				echo '<script type="text/javascript" src="' . $_SESSION['dossier_vue'] . '/js/perso_messages_conversation.js"></script>';
				require_once('perso_messages_conversation.php');
			}
			else
			{
				echo '<script type="text/javascript" src="' . $_SESSION['dossier_vue'] . '/js/perso_messages.js"></script>';
				require_once('perso_messages.php');
			}
		}
		else if(isset($_GET['demandes_de_location']))
			require_once('perso_demandes_de_location.php');
		else if(isset($_GET['annonces']))
			require_once('perso_annonces.php');
		else if(isset($_GET['revenus']))
			require_once('perso_revenus.php');
		else if(isset($_GET['compte']))
		{
			echo '<script type="text/javascript" src="' . $_SESSION['dossier_vue'] . '/js/perso_compte.js"></script>';
			require_once('perso_compte.php');
		}
		?>
	</div>
</div>