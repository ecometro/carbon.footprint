<?php
$project_perma = get_permalink();
$project_tit = get_the_title();
$project_desc = get_the_excerpt();

if ( $post->post_status != 'draft' ) {
	$project_featured = "
	<a class='pull-left' href='".$project_perma."'>Aqui la emision total del proyecto</a>
	";
} else { $project_featured = ""; }

if ( $my_projects == 1 ) {
	$project_edit_url = "/calculo-huella-carbono/?step=1&project_id=".$post->ID;
	if ( $post->post_status == 'draft' ) { $project_status = "Incompleto"; $btn_class = " btn-warning"; }
	elseif ( $post->post_status == 'private' ) { $project_status = "Privado"; $btn_class = " btn-danger"; }
	else { $project_status = "Público"; $btn_class = " btn-success"; }
	$project_extra = "
	<footer class='media-footer'>
		<ul class='list-inline'>
			<li><button type='button' class='btn btn-xs".$btn_class."' disabled='disabled'>".$project_status."</button></li>
			<li><a class='btn btn-default btn-xs' href='".$project_edit_url."'>Editar proyecto</a></li>
		</ul>
	</footer>
	";
} else { $project_extra = ""; }
?>
<article class="media-collabora media">
	<?php echo $project_featured; ?>
	<div class="media-body">
		<header><h2 class="media-heading"><a href="<?php echo $project_perma ?>"><?php echo $project_tit ?></a></h2></header>
		<div class="media-desc"><?php echo $project_desc; ?></div>
		<?php echo $project_extra; ?>
	</div>
</article>
