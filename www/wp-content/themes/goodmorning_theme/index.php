<?php
	$user_info = get_userdata( get_current_user_id() );

	if(is_user_logged_in()){
		$name = $user_info->display_name;
	} else {
		$name = "Nachrichten";
	}

	get_header(); ?>
<div class="progress-bar" style="width: 0%;"></div>
<div class="loading-indicator"></div>
<div class="page-wrapper">
	<div class="prev-button" id="b_prev">
		<a href="#prev" class="fa fa-angle-left"></a>
	</div>
	<ul id="master-list" class="cards">
	    <li class="welcome-card card show">
	    	<header style="background-image: url(<?php echo WP_THEME_URL; ?>/img/morgen.jpg)">
		    	<h1 class="welcome-text">Guten Morgen, <br />
		    	<span class="username"><?php echo $name; ?></span>
		    	<span class="loading">Deine 10 Minuten Frühstücks-Nachrichten werden vorbereitet.</span></h1>
	    	</header>
	    </li>
	    <li class="goodbye-card card">
		    <header style="background-image: url(<?php echo WP_THEME_URL; ?>/img/tag.jpg)">
		    	<h2 class="goodbye-text">
			    	Wir wünschen dir<br /> einen schönen Tag!
		    	</h2>
		    </header>
	    </li>
	</ul>
	<div class="next-button" id="b_next">
		<a href="#next" class="fa fa-angle-right"></a>
	</div>
</div>

<?php

	if(is_user_logged_in()){
		$login = '<a href="'.wp_logout_url( home_url() ).'">Logout</a>';
	} else {
		$login = '<a href="'.wp_login_url( home_url() ).'">Login</a>';
	}

?>

<div class="footer">
	<h3>Guten Morgen, Nachrichten! | <?php echo $login; ?></h3>
	<p>&copy; 2016 | von Flo, Jan & Hendrik für die #pulshackdays
</div>

<?php get_footer(); ?>