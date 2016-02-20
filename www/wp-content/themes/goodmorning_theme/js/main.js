//@depend "plugins.js"
//@dependx "constants.js"



var postindex = -1;
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
		}
	});


});

function insert_posts(posts) {
	//  console.log('+++++++++' + typeof (posts));
	var duration = 0;
	if (typeof(posts) == "object") {
		for (var i in posts) {
			var post = posts[i];
			console.log(post);
			duration = duration + post.consume_dur;
			var $post = $("<li>").attr("id", "post-" + i).addClass("article card").attr("postid", post.id);
			// Construct the post header
			$subheadline = $("<span>").addClass("small").html(post.headline);
			$headline = $("<h1>").html(post.title).prepend($subheadline);
			$header = $("<header>").addClass("article-header").append($headline).css("background-image", "url(" + post.thumbnail + ")");
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
			if (duration >= 1000) break;
		}
		console.log("duration : " + duration);
	}
	var $goodbyeCard = $("li.card.goodbye-card");
	$goodbyeCard.remove();
	$("ul.cards").append($goodbyeCard);
}