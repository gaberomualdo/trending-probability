<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Trending Data</title>
</head>
<body>
	<h1>Trending Data Page</h1>
	<p>Each time this page reloads, the current set of Trending YouTube videos is added to the trending-probability database.</p>
	<p>This page is set to automatically reload every hour.</p>
	<p>The more data we have, the more accurate the trending-probability predictions will be, so please run this file overnight or in the background.</p>

	<div style="display: none;" id="vid_data">
	<?php

	require("simple_html_dom.php");

	$youtubeTrendingList = file_get_html("https://www.youtube.com/feed/trending")->find("div.expanded-shelf-content-item");
	foreach ($youtubeTrendingList as $video){
		echo "<div class='video'>";
		echo "<p class='views'>" . $video->find("ul.yt-lockup-meta-info li")[1]->plaintext . "</p>";
		echo "<p class='date'>" . $video->find("ul.yt-lockup-meta-info li")[0]->plaintext . "</p>";
		echo "<p class='duration'>" . $video->find("h3.yt-lockup-title span")[0]->plaintext . "</p>";
		echo "</div>";
	}

	?>
	</div>


	<script src="https://www.gstatic.com/firebasejs/5.4.2/firebase.js"></script>
	<script>
	// Initialize Firebase
	var config = {
		apiKey: "AIzaSyDmJXMpZOA8ZDg9OhSRZv1K-ZYon4gypMo",
		authDomain: "trending-data.firebaseapp.com",
		databaseURL: "https://trending-data.firebaseio.com",
		projectId: "trending-data",
		storageBucket: "trending-data.appspot.com",
		messagingSenderId: "600953004306"
	};
	firebase.initializeApp(config);

	var videos = document.querySelectorAll("div#vid_data div.video");
	for(var i = 0;i < videos.length;i++){
		var views = videos[i].querySelector(".views").innerText.split(" ")[0].split(",").join("");
		var hoursAgo = videos[i].querySelector(".date").innerText.toLowerCase().split(" ago").join("").split("streamed ").join("");
		if(hoursAgo.includes("hour")){
			hoursAgo = parseInt(hoursAgo.split(" ")[0]);
		}else if(hoursAgo.includes("day")){
			hoursAgo = parseInt(hoursAgo.split(" ")[0]) * 24;
		}else if(hoursAgo.includes("week")){
			hoursAgo = parseInt(hoursAgo.split(" ")[0]) * 168;
		}else if(hoursAgo.includes("year")){
			hoursAgo = parseInt(hoursAgo.split(" ")[0] * 8760);
		}
		var duration = videos[i].querySelector(".duration").innerText.split(": ")[1].split(". ").join("").split(":");
		if(duration.length == 2){
			duration = (parseInt(duration[0]) * 60) + parseInt(duration[1]);
		}else if(duration.length == 3){
			duration = (parseInt(duration[0]) * 60 * 60) + (parseInt(duration[1]) * 60) + parseInt(duration[2]);
		}else {
			duration = parseInt(duration[0]);
		}
		firebase.database().ref().push({
			views: views,
			duration: duration,
			hoursAgo: hoursAgo
		});
	}

	setInterval(function(){
		window.location.reload();
	},3600000);
	</script>
</body>
</html>