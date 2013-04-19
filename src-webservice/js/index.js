
Rp(function() {
	/*----- Bagian SlideShow ----*/
	var content_wrap = document.getElementById("intro-slideshow");
	si = Rp('.slide_item');
	sicount = si.length();
	/* si.each(function() {
		this.style.width = (100 / sicount) + '%';
	}); */
	si.css('width', (100 / sicount) + '%');

	bullets = document.createElement('div');
	bullets.id = 'slide_bullet';
	handleBullet = function() {
		n = this.getAttribute('data-i');
		go_to_image(n);
	};

	for (i = 1; i <= sicount; i++) {
		bullet = document.createElement('div');
		bullet.className = 'bullet_slide';
		if (i == 1)
			bullet.className += ' active';

		bullet.setAttribute('data-i', i);
		Rp(bullet).on('click', handleBullet);

		bullets.appendChild(bullet);
	}

	content_wrap.appendChild(bullets);

	// scrap this
	content_wrap.onmouseover = function()
	{
		document.getElementById("slide_left").style.opacity = "1";
		document.getElementById("slide_right").style.opacity = "1";
	}

	content_wrap.onmouseout = function()
	{
		document.getElementById("slide_left").style.opacity = "0";
		document.getElementById("slide_right").style.opacity = "0";
	}
	// /scrap	

	var current_image = 1;
	var max_image = 4;
	var width = 100;
	function go_to_bullet(index)
	{
		var bullets = document.getElementById("slide_bullet").childNodes;
		var i = 1;
		for (var j=0;j<bullets.length;++j)
		{
			if (bullets[j].nodeName!="#text")
			{
				if (i==index)
					bullets[j].className = "bullet_slide active";
				else
					bullets[j].className = "bullet_slide";
				i++;
			}
		}	
	}
	function next_image()
	{
		current_image++;
		if (current_image > max_image)
			current_image = 1;
		document.getElementById("content_wrap_inner").style.left = (-1*width*(current_image-1))+"%";
		go_to_bullet(current_image);
	}
	function prev_image()
	{
		current_image--;
		if (current_image < 1)
			current_image = max_image;
		document.getElementById("content_wrap_inner").style.left = (-1*width*(current_image-1))+"%";
		go_to_bullet(current_image);
	}
	document.onkeydown = function(event)
	{
		if (event.keyCode=="37")
			prev_image();
		else if (event.keyCode=="39")
			next_image();

	}
	function go_to_image(index)
	{
		current_image = index;
		document.getElementById("content_wrap_inner").style.left = (-1*width*(current_image-1))+"%";
		go_to_bullet(current_image);
	}
	document.getElementById("slide_left").onclick = function() {prev_image()};
	document.getElementById("slide_right").onclick = function() {next_image()};

	window.onload = function() 
	{
		datePicker.init(document.getElementById("calendar"), document.getElementById("register_form"), "birth_date");
		setInterval(function(){next_image();}, 5500);
	}
});
