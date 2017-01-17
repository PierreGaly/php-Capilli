<?php
$membres_manager = new MembresMan($bdd);
$parrainage_manager = new ParrainagesMan($bdd);
$nombre_resultats_par_page = 20;
$nbr_membres = $membres_manager->countMembres($bdd);
$nbr_pages = ceil($nbr_membres/$nombre_resultats_par_page);

if(isset($_GET['p']) && ((int) $_GET['p']) > 1 && ((int) $_GET['p']) <= $nbr_pages)
	$page_number = (int) $_GET['p'];
else
	$page_number = 1;
?>
<div class="row">
	<div class="row_content">
		<h2>Liste des membres<br /><span class="small"><?php echo $nbr_membres; ?> membres inscrits</span></h2>
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th class="text-center">Parrain</th>
					<th class="text-center">Prénom Nom</th>
					<th class="text-center">Adresse postale</th>
					<th class="text-center">Adresse mail</th>
					<th class="text-center">Téléphone fixe</th>
					<th class="text-center">Téléphone portable</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="5" class="text-center">
						<ul class="pagination">
							<li class="hidden-xs hidden-sm<?php if($page_number == 1) echo ' disabled'; ?>"><a href="membre.php?p=1" aria-label="Début"><span aria-hidden="true">&laquo;</span></a></li>
							<li class="hidden-xs<?php if($page_number == 1) echo ' disabled'; ?>"><a href=membre.php?p=<?php echo max(1, $page_number-1); ?>" aria-label="Précédant"><span aria-hidden="true">&lsaquo;</span></a></li>
							<?php
							for($i=1; $i<=$nbr_pages; $i++)
							{
								echo '<li';
								
								if($i == $page_number)
									echo ' class="active"';
								
								echo '><a href="membre.php?p=' . $i . '">' . $i . '</a></li>';
							}
							?>
							<li class="hidden-xs<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="membre.php?p=<?php echo min($nbr_pages, $page_number+1); ?>" aria-label="Suivant"><span aria-hidden="true">&rsaquo;</span></a></li>
							<li class="hidden-xs hidden-sm<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="membre.php?p=<?php echo $nbr_pages; ?>" aria-label="Fin"><span aria-hidden="true">&raquo;</span></a></li>
						</ul>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$membres_liste = $membres_manager->getMembres($nombre_resultats_par_page, $nombre_resultats_par_page * ($page_number-1));
				
				foreach($membres_liste as $membre_liste)
				{
					$parrain = $parrainage_manager->getParrain($membre_liste->ID);
					?>
					<tr>
						<td class="text-center"><?php echo empty($parrain) ? '-' : $parrain->sePresenter(); ?></td>
						<td><?php echo $membre_liste->sePresenter(); ?></td>
						<td><?php echo htmlspecialchars($membre_liste->adresse_complete); ?></td>
						<td><?php if($membre_liste->type == 0 && !$membre_liste->email_valide) echo '<span class="glyphicon glyphicon-exclamation-sign rose_custom" data-toggle="tooltip" title="L\'adresse email n\'a pas été validée."></span> '; ?><a href="mailto:<?php echo htmlspecialchars($membre_liste->email); ?>"><?php echo htmlspecialchars($membre_liste->email); ?></a></td>
						<td class="text-center"><?php echo ($membre_liste->tel_fixe == '') ? '-' : ('+' . substr($membre_liste->tel_fixe, 2, 2) . '.' . substr($membre_liste->tel_fixe, 4)); ?></td>
						<td class="text-center"><?php echo '+' . substr($membre_liste->tel_portable, 2, 2) . '.' . substr($membre_liste->tel_portable, 4); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>