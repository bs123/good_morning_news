//@depend "plugins.js"
//@dependx "constants.js"



var duration = 0;
var loadingState = false;



function setLoading(loading) {
	loadingState = loading;
	if (loading) {
		$("body").addClass("loading");
	} else {
		$("body").removeClass("loading");
	}
}


function isLoading() {
	return loadingState;
}

$(document).ready(function() {

	// Load the inital posts
	setLoading(true);
	$.ajax({
		url: WP_API_Settings.root + 'goodmorning-news/1.0/list-news/',
		method: 'GET',
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
		},
	}).done(function(response) {
		// console.log(response);
		insert_posts(response);
		setLoading(false);
	}).error(function(response) {
		setLoading(false)
	});


	$("ul.cards").on("click", "li.card a.upvote", function(e) {
		var $card = $(e.currentTarget).parents("li.card");
		var $postId = $card.attr("postid");
		setLoading(true);
		$.ajax({
			url: WP_API_Settings.root + 'goodmorning-news/1.0/upvote/' + $postId,
			method: 'GET',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
		}).done(function(response) {
			console.log("upvote successful");
			$card.addClass("upvoted").removeClass("downvoted");
			setLoading(false);
		}).error(function(response) {
			setLoading(false)
		});
	});


	$("ul.cards").on("click", "li.card a.downvote", function(e) {
		var $card = $(e.currentTarget).parents("li.card");
		var $postId = $card.attr("postid");

		set_read($card.attr("postid"));

		setLoading(true);
		$.ajax({
			url: WP_API_Settings.root + 'goodmorning-news/1.0/downvote/' + $postId,
			method: 'GET',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
		}).done(function(response) {
			console.log("downvote successful");
			$card.addClass("downvoted").removeClass("upvoted");
			setLoading(false);
		}).error(function(response) {
			setLoading(false)
		});
	});


	$("ul.cards").on("click", "li.card a.more_content", function(e) {
		var $card = $(e.currentTarget).parents("li.card");
		$card.toggleClass("content_open");

		set_read($card.attr("postid"));

		if($card.hasClass("content_open") && $("video", $card).length > 0){
			$('video', $card).each(function() {
			    $(this).get(0).play();
			});
		} else if($("video", $card).length > 0) {
			$('video', $card).each(function() {
			    $(this).get(0).pause();
			});
		}
	});


	$("#b_next").click(function() {
		if(isLoading()){
			return;
		}
		var $current = $("ul.cards li.card.show");
		var $next = $current.next("li.card");
		if ($next.length > 0) {
			$current.removeClass("show").removeClass("content_open");
			$next.addClass("show");

			$('video').each(function() {
			    $(this).get(0).pause();
			});

			var newDuration = 0;
			$next.prevAll("li.card").each(function(){
				newDuration += parseInt($(this).attr("duration")) || 0;
			});
			$(".progress-bar").css("width", (newDuration/duration*100) + "%");
		}
	});


	$("#b_prev").click(function() {
		if(isLoading()){
			return;
		}
		var $current = $("ul.cards li.card.show");
		var $next = $current.prev("li.card");
		if ($next.length > 0) {
			$current.removeClass("show").removeClass("content_open");
			$next.addClass("show");

			$('video').each(function() {
			    $(this).get(0).pause();
			});

			var newDuration = 0;
			$next.prevAll("li.card").each(function(){
				newDuration += parseInt($(this).attr("duration")) || 0;
			});
			$(".progress-bar").css("width", (newDuration/duration*100) + "%");
		}
	});


});

function set_read(post_id){
	$.ajax({
		url: WP_API_Settings.root + 'goodmorning-news/1.0/read/' + post_id,
		method: 'GET',
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
		},
	}).done(function(response) {
		console.log("read successful");
	}).error(function(response) {

	});
}

function insert_posts(posts) {
	//  console.log('+++++++++' + typeof (posts));
	if (typeof(posts) == "object") {
		for (var i in posts) {
			var post = posts[i];
			console.log(post);
			duration = duration + post.consume_dur;
			var $post = $("<li>").attr("id", "post-" + i).addClass("article card").attr("postid", post.id).attr("duration", post.consume_dur);
			// Construct the post header
			$subheadline = $("<span>").addClass("small").html(post.headline);
			$headline = $("<h1>").html(post.title).prepend($subheadline);
			$header = $("<header>").addClass("article-header").append($headline);
			if(post.thumbnail != "" && post.thumbnail != null){
				$header.css("background-image", "url(" + post.thumbnail + ")");
			}

			$video = null;
			if(post.video.video_src != null){
				$src = $("<source>").attr("src", post.video.video_src).attr("type", "video/mp4");
				$video = $("<video>").append($src).attr("controls", true).addClass("video-js");
				$header.append($video);
				$post.addClass("video");
			}

			$post.append($header);
			// Construct the button bar
			$downvote_link = $("<a>").attr("href", "#downvote").addClass("downvote fa fa-thumbs-o-down").attr("id", "b_rate_down");
			$upvote_link = $("<a>").attr("href", "#upvote").addClass("upvote fa fa-thumbs-o-up").attr("id", "b_rate_up");
			$more_link = $("<a>").attr("href", "#upvote").addClass("more_content fa fa-angle-down");
			$buttons = $("<div>").addClass("buttons").append($downvote_link).append($more_link).append($upvote_link);
			$post.append($buttons);
			// Construct the Content
			var $datetime = $("<div>").addClass("datetime").html(post.date);
			var $content = $("<div>").addClass("content clearfix").html(post.content).prepend($datetime);
			$post.append($content);
			$("#master-list").append($post);

			if (duration >= 600) break;
		}
		console.log("duration : " + duration);
	}
	var $goodbyeCard = $("li.card.goodbye-card");
	$goodbyeCard.remove();
	$("ul.cards").append($goodbyeCard);
}