<?php	/* *	The template for displaying events calendar on "/events" url slug * *	Override this tempalte by coping it to yourtheme/eventon/archive-ajde_events.php * *	@Author: AJDE *	@EventON *	@version: 0.1 */				get_header();		$evOpt = evo_get_options('1');	$archive_page_id = evo_get_event_page_id($evOpt);	// check whether archieve post id passed	if($archive_page_id){		$archive_page  = get_page($archive_page_id);			echo apply_filters('the_content', $archive_page->post_content);	}else{		echo "<p>ERROR: Please select a event archieve page in eventON Settings</p>";	}		get_footer();?>