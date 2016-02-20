<?php
	$user_info = get_userdata( get_current_user_id() );

	get_header(); ?>

<ul id="master-list" class="cards">
    <li class="welcome-card card show">
    	<h1 class="welcome-text">Guten Morgen, <br />
    	<span class="username"><?php echo $user_info->user_nicename; ?></span></h1>
    </li>
    <li class="goodbye-card card">
    	<h2 class="goodbye-text">
	    	Wir wünschen dir einen schönen Tag!
    	</h2>
    </li>
</ul>

<div class="buttons">

<button id="b_next">next</button>
<button id="b_last">last</button>

<button id="b_rate_down">rate_down</button>
<button id="b_rate_up">rate_up</button>
</div>

<?php get_footer(); ?>