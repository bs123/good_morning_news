//@depend "plugins.js"
//@dependx "constants.js"

var postindex = -1;

$(document).ready(function () {
    //   $.get("http://goodmorning.devserv.de/wp-json/goodmorning-news/1.0/list-news", function (data, status) {
    $.ajax({
        url: WP_API_Settings.root + 'goodmorning-news/1.0/list-news/', method: 'GET', beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
        },
    }).done(function (response) {
        // console.log(response);
        insert_posts(response);

        $(".article").click(function () {
            var $this = $(this);
            console.log($this);
            // var $article = $this.parents("li.article");
            var $content = $(".content", $this);
            //   $content.removeClass("panel").addClass("headline").slideDown(constants.SLIDE_DOWN);
          //  console.log($content);
            $content.removeClass("hidden").addClass("shown");
        });
    });
});

$(document).ready(function () {
    $("#liveReload").click(function () {
        $("#liveReload").fadeOut();
    });
});

$(document).ready(function () {
    $("#b_rate_up").click(function () {
        $postId = $(".card.show").attr("postId");
        console.log( $postId );
        $.ajax({
            url: WP_API_Settings.root + 'goodmorning-news/1.0/upvote/' + $postId,
            method: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
            },
        }).done(function (response) {
            console.log(response);
            //  console.log($(".article"));
        });
    });

    $("#b_rate_down").click(function () {
        $postId = $(".card.show").attr("postid");
        console.log( $postId );
        $.ajax({
            url: WP_API_Settings.root + 'goodmorning-news/1.0/downvote/' + $postId,
            method: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
            },
        }).done(function (response) {
            console.log(response);
            //  console.log($(".article"));
        });
    });
});

$(document).ready(function () {

    $("#b_next").click(function(){
	   var $current = $("ul.cards li.card.show");
	   var $next = $current.next("li.card");

	   if($next.length > 0){
	   	$current.removeClass("show");
	   	$next.addClass("show");
	   }
    });

     $("#b_prev").click(function(){
	   var $current = $("ul.cards li.card.show");
	   var $next = $current.prev("li.card");

	   if($next.length > 0){
	   	$current.removeClass("show");
	   	$next.addClass("show");
	   }
    });
});


function insert_posts(posts) {
    //  console.log('+++++++++' + typeof (posts));
    var duration = 0;
    if (typeof (posts) == "object") {
        for (var i in posts) {
            var post = posts[i];
            console.log(post);
            duration = duration + post.consume_dur;
            var $post = $("<li>").attr("id", "post-" + i).addClass("article card contentHidden").attr("postid", post.id);

            // Construct the post header
            $subheadline = $("<span>").addClass("small").html(post.headline);
            $headline = $("<h1>").html(post.title).prepend($subheadline);
            $header = $("<header>").addClass("article-header").append($headline).css("background-image", "url("+post.thumbnail+")");
			$post.append($header);

			// Construct the button bar
			$downvote_link = $("<a>").attr("href", "#downvote").addClass("downvote fa fa-thumbs-o-down");
			$upvote_link = $("<a>").attr("href", "#upvote").addClass("upvote fa fa-thumbs-o-up");
			$more_link = $("<a>").attr("href", "#upvote").addClass("more_content fa fa-angle-down");
			$buttons = $("<div>").addClass("buttons").append($downvote_link).append($more_link).append($upvote_link);

			$post.append($buttons);

			// Construct the Content
			var $datetime = $("<div>").addClass("datetime").html(post.date);
			var $content = $("<div>").addClass("hidden content clearfix").html(post.content).prepend($datetime);

            $post.append($content);

            $("#master-list").append($post);
            if (duration >= 1000)
                break;
        }
        console.log("duration : " + duration);
    }

    var $goodbyeCard = $("li.card.goodbye-card");
    $goodbyeCard.remove();
    $("ul.cards").append($goodbyeCard);
}
