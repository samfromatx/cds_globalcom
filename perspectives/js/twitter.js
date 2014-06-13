//Twitter 
// http://www.alovefordesign.com/demos/custom-twitter/index.html 
// http://www.alovefordesign.com/add-a-custom-ajax-twitter-feed-to-your-web-site/
window.onload = function() {
	var ajax_load = "<img class='loader' src=' loader.gif' alt='Loading...' />";
	var url = 'https://api.twitter.com/1/statuses/user_timeline.json?screen_name=cdsglobal&include_rts=1&callback=twitterCallback2&count=2';
	var script = document.createElement('script');	
	$("#twitter_feed").html(ajax_load);
	script.setAttribute('src', url);
	document.body.appendChild(script);
}

function twitterCallback2(twitters) {
  var statusHTML = [];
  for (var i=0; i<twitters.length; i++){
    var username = twitters[i].user.screen_name;
    var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, 
	function(url) { return '<a href="'+url+'" target="_blank">'+url+'</a>';
    }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
      return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'" target="_blank">'+reply.substring(1)+'</a>';
    });
    statusHTML.push('<li><p>'+status+' <a href="http://twitter.com/'+username+'" class="twitter_date" target="_blank">'+relative_time(twitters[i].created_at)+'</a></p></li>');
  }
  document.getElementById('twitter_update_list').innerHTML = statusHTML.join('');
}

function relative_time(time_value) {
  var values = time_value.split(" ");
  time_value = values[1] + " " + values[2] + " " + values[5] + " " + values[3];
  var parsed_date = new Date();
  parsed_date.setTime(Date.parse(time_value));  
  var months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug',
	'Sep', 'Oct', 'Nov', 'Dec');
  var m = parsed_date.getMonth();
  var postedAt = '';
  postedAt = months[m];
  postedAt += " "+ parsed_date.getDate();
  postedAt += ","
  postedAt += " "+ parsed_date.getFullYear();
  return postedAt;
}	