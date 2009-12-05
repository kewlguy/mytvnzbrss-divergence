function changeShow(checkbox) {
	id = checkbox.value;
	anchor = document.getElementById('lbl_' + id);

	if (checkbox.checked==true) {
		anchor.className='box selected';
	} else if (checkbox.checked==false) {
		anchor.className='box';
	}
}