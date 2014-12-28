<?php
$project_perma = get_permalink();
$project_tit = get_the_title();
$project_desc = get_the_excerpt();
$project_emission = round( get_post_meta($post->ID,'_hce_project_emission_total',true) + get_post_meta($post->ID,'_hce_project_emission_transport_total',true) );

if ( $post->post_status != 'draft' ) {
	$project_featured = "
	<div class='col-sm-2 pull-left'><div class='list-circle'><div class='list-circle-label bg-primary'><strong>".$project_emission."</strong><br />kg CO<sub>2</sub> eq</div></div></div>
	";

} else {
	$project_featured = "
	<div class='col-sm-2 pull-left'><div class='list-circle'><div class='list-circle-label bg-info'><strong>?</strong><br />kg CO<sub>2</sub> eq</div></div></div>
	";

}

if ( $what_projects == 'mine' ) {
	$project_edit_url = "/calculo-huella-carbono/?step=1&project_id=".$post->ID;
	if ( $post->post_status == 'draft' ) { $project_status = "Incompleto"; $btn_class = " btn-warning"; }
	elseif ( $post->post_status == 'private' ) { $project_status = "Privado"; $btn_class = " btn-danger"; }
	else { $project_status = "Público"; $btn_class = " btn-success"; }
	$project_extra = "
	<footer class='list-footer'>
		<ul class='list-inline'>
			<li><button type='button' class='btn btn-xs".$btn_class."' disabled='disabled'>".$project_status."</button></li>
			<li><a class='btn btn-default btn-xs' href='".$project_edit_url."'>Editar proyecto</a></li>
		</ul>
	</footer>
	";

} elseif ( $what_projects == 'author' ) {
	$project_extra = "";

} else {
	$project_author = get_the_author();
	$project_author_url = get_author_posts_url( $post->post_author);
	$project_extra = "
	<footer class='list-footer'>
		<ul class='list-inline'>
			<li>Proyecto evaluado por <a class='btn btn-default btn-xs' href='".$project_author_url."'><span class='gliphicon gliphicon-user'></span> ".$project_author."</a></li>
		</ul>
	</footer>
	";
}
?>
<article class="row list-item">
	<?php echo $project_featured; ?>
	<div class="col-sm-10">
		<header><h2 class="list-heading"><a href="<?php echo $project_perma ?>"><?php echo $project_tit ?></a></h2></header>
		<div class="list-desc"><?php echo $project_desc; ?></div>
		<?php echo $project_extra; ?>
	</div>
</article>
