Rp(function() {
	var original = Rp('#edit_form').serialize();
	Rp('#edit_form').on('submit', function() {
		var now = Rp('#edit_form').serialize();
		if (original == now)
			alert('Tidak ada perubahan.');
		this.submit();
	})
})