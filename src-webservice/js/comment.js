Rp(function() 
{
	function retrieve_detail_task()
	{
		var month = ["January","February","March","April","May","June",
					"July","August","September","October","November","December"];
		var req = Rp.ajaxRequest();
		req.onreadystatechange = function() {
			switch (req.readyState) {
				case 1:
				case 2:
				case 3:
					// Rp('#commentForm').addClass('loading');
					break;
				case 4:
					// Rp('#commentForm').removeClass('loading');
					try {
						response = Rp.parseJSON(req.responseText);
						document.getElementById("task-title").innerHTML = response.nama_task;
						date = new Date(response.deadline.substr(0,4),(parseInt(response.deadline.substr(5,2)))-1,parseInt(response.deadline.substr(8,2)));
						document.getElementById("detail-deadline").innerHTML = date.getDate()+" "+month[date.getMonth()]+" "+date.getFullYear();
						var string = "";
						for (var i in response.asignee)
						{
							string += "<a href='profile?id="+response.asignee[i].id_user+"'>"+response.asignee[i].username+"</a>,";
						}
						string = string.substr(0,string.length-1);
						document.getElementById("detail-asignee").innerHTML = string;
						var tags = document.getElementsByClassName('tag');
						for (var i=0; i<tags.length; i++) 
						{
							tags[i].parentNode.removeChild(tags[i]);
						}
						var tagparent = document.getElementById("detail-tag");
						for (var i in response.tag)
						{
							var tag = document.createElement("span");
							tag.className = "tag"
							tag.innerHTML = response.tag[i];
							tagparent.appendChild(tag);
						}
					}
					catch (e) {

					}
					break;
			}
		}
		req.get('api/get_task?id_task=' + id_task);
	}
	
	function buildcomment(comment)
	{
		var commentList = document.getElementById("commentsList");
		var string = '<article id="comment_'+comment.id_komentar+'" class="comment">';
			string += '<a href="profile?id='+comment.id_user+'">';
				string += '<img src="upload/user_profile_pict/'+comment.avatar+'" alt="'+comment.fullname+'" class="icon_pict" >';
			string += '</a>';
			string += '<div class="right">';
				date = new Date(comment.timestamp.substr(0,4),(parseInt(comment.timestamp.substr(5,2)))-1,comment.timestamp.substr(8,2),
								comment.timestamp.substr(11,2),comment.timestamp.substr(14,2),comment.timestamp.substr(17,2));
				var temphour = (date.getHours()>=10)? "" : "0";
				var tempmin = (date.getMinutes()>=10)? "" : "0";
				string += temphour + date.getHours()+":"+tempmin+date.getMinutes()+" – "+Day[date.getDay()]+"/"+Mon[date.getMonth()];
				if (comment.id_user==id_user)
					string += ' <a href="javascript:delete_comment('+comment.id_komentar+')">DELETE</a>';
			string += '</div>';
			string += '<header>';
				string += '<a href="profile?id='+comment.id_user+'">';
					string += '<h4>'+comment.username+'</h4>';
				string += '</a>';
			string += '</header>';
			string += '<p>';
				string += comment.komentar;
			string += '</p>';
			string += '<div class="clear"></div>';
		string += '</article>';		
		commentList.innerHTML += string;
		total_comment++;
		current_total_comment++;
		var tempstring = (total_comment>1)? "s":"";
		document.getElementById("total_comment").innerHTML = total_comment + " Comment"+tempstring;
	}
	
	function retrievecomment()
	{
		var req = Rp.ajaxRequest();
		req.onreadystatechange = function() {
			switch (req.readyState) {
				case 1:
				case 2:
				case 3:
					// Rp('#commentForm').addClass('loading');
					break;
				case 4:
					// Rp('#commentForm').removeClass('loading');
					try {
						response = Rp.parseJSON(req.responseText);
						for (var i in response)
						{
							buildcomment(response[i]);
							document.getElementById("show_status").innerHTML = "Showing "+current_total_comment+" of "+total_comment;
							timestamp = response[i].timestamp;
						}
					}
					catch (e) {

					}
					break;
			}
		}
		req.get('api/retrieve_comments?id_task=' + id_task+"&timestamp="+timestamp);
	}
	
	Rp('#commentForm').on('submit', function(e) 
	{
		e.preventDefault();
		
		var serialized = Rp(this).serialize();

		var req = Rp.ajaxRequest('api/comment');
		req.onreadystatechange = function() {
			switch (req.readyState) {
				case 1:
				case 2:
				case 3:
					Rp('#commentForm').addClass('loading');
					break;
				case 4:
					Rp('#commentForm').removeClass('loading');
					try {
						response = Rp.parseJSON(req.responseText);
						if (response == "success")
						{
							document.getElementById("commentBody").value = "";
							retrievecomment();
						}
					}
					catch (e) {

					}
					break;
			}
		}
		req.post(serialized);
	});
	
	window.onload = function()
	{
		setInterval(function(){retrievecomment();},7000);
		setInterval(function(){retrieve_detail_task();},10000);
		// override onload function
		datePicker.init(document.getElementById("calendar"), document.getElementById("new_tugas"), "deadline");
	}
});

function delete_comment(id)
{
	var serialized = "id="+id;
	var req = Rp.ajaxRequest('api/remove_comment');
	req.onreadystatechange = function() {
		switch (req.readyState) {
			case 1:
			case 2:
			case 3:
				Rp('#commentForm').addClass('loading');
				break;
			case 4:
				Rp('#commentForm').removeClass('loading');
				try {
					response = Rp.parseJSON(req.responseText);
					if (response == "success")
					{
						var element = document.getElementById("comment_"+id);
						element.parentNode.removeChild(element);
						total_comment--;
						current_total_comment--;
						var tempstring = (total_comment>1)? "s":"";
						document.getElementById("total_comment").innerHTML = total_comment + " Comment"+tempstring;
						document.getElementById("show_status").innerHTML = "Showing "+current_total_comment+" of "+total_comment;
					}
				}
				catch (e) {

				}
				break;
		}
	}
	req.post(serialized);
}

function prepend_comment(comment)
{
	var commentList = document.getElementById("commentsList");
	var string = '<article id="comment_'+comment.id_komentar+'" class="comment">';
		string += '<a href="profile?id='+comment.id_user+'">';
			string += '<img src="upload/user_profile_pict/'+comment.avatar+'" alt="'+comment.fullname+'" class="icon_pict" >';
		string += '</a>';
		string += '<div class="right">';
			date = new Date(comment.timestamp.substr(0,4),(parseInt(comment.timestamp.substr(5,2)))-1,comment.timestamp.substr(8,2),
								comment.timestamp.substr(11,2),comment.timestamp.substr(14,2),comment.timestamp.substr(17,2));
			var temphour = (date.getHours()>=10)? "" : "0";
			var tempmin = (date.getMinutes()>=10)? "" : "0";
			var tempstring = temphour + date.getHours()+":"+tempmin+date.getMinutes()+" – "+Day[date.getDay()]+"/"+Mon[date.getMonth()];
			string += tempstring;
			if (comment.id_user==id_user)
				string += ' <a href="javascript:delete_comment('+comment.id_komentar+')">DELETE</a>';
		string += '</div>';
		string += '<header>';
			string += '<a href="profile?id='+comment.id_user+'">';
				string += '<h4>'+comment.username+'</h4>';
			string += '</a>';
		string += '</header>';
		string += '<p>';
			string += comment.komentar;
		string += '</p>';
		string += '<div class="clear"></div>';
	string += '</article>';
	commentList.innerHTML = string + commentList.innerHTML;
}

function more_comment()
{
	var req = Rp.ajaxRequest();
	req.onreadystatechange = function() {
		switch (req.readyState) {
			case 1:
			case 2:
			case 3:
				Rp('#commentList').addClass('loading');
				break;
			case 4:
				Rp('#commentList').removeClass('loading');
				try {
					response = Rp.parseJSON(req.responseText);
					for (var i in response)
					{
						prepend_comment(response[i]);
						first_timestamp = response[i].timestamp;
						current_total_comment++;
					}
					var tempstr = (total_comment-current_total_comment>1) ? "s" : "";
					if (total_comment==current_total_comment)
					{
						var element = document.getElementById("more_link");
						element.parentNode.removeChild(element);
					}
					else
					{
						document.getElementById("more_link").innerHTML = "Previous Comment"+tempstr;
					}
					document.getElementById("show_status").innerHTML = "Showing "+current_total_comment+" of "+total_comment;
				}
				catch (e) {

				}
				break;
		}
	}
	req.get('api/get_previous_comments?id_task=' + id_task+"&timestamp="+first_timestamp);
}
