//@depend "plugins.js"
//@depend "constants.js"

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
            console.log($content);
            $content.removeClass("hidden").addClass("shown");
        });
    });

});

$(document).ready(function () {
    $("#liveReload").click(function () {
        //$(this).hide();
        $("#liveReload").fadeOut();
        //$("#liveReload").fadeOut("slow");
        //$("#liveReload").fadeOut(constants.FADE_OUT);
    });
});

$(document).ready(function () {
});

$(document).ready(function () {
    var postindex = 0;
    $("#b_next").click(function () {
        $("#post-" + (postindex-1)).addClass("hidden").removeClass("shown");
        $("#post-" + postindex).removeClass("hidden").addClass("shown");
        // $("#post-1").toggle();
        postindex ++;
        console.log($(".article"));

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
            var $post = $("<li>").attr("id", "post-" + i).addClass("hidden article");
            $post.append($("<div>").addClass("headline").text(post.title));
            $post.append($("<div>").addClass("hidden content").text(post.content));

            $("#master-list").append($post);
            if (duration >= constants.MAX_DURATION)
                break;
        }
        console.log("duration : " + duration);
    }
}
