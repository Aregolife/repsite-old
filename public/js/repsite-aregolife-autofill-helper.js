jQuery(document).ready(() => {
	jQuery.ajax({'url': '/repsite/public/MockData/mock.php'}).done(
(obj) => {
	for(let key in obj){
		jQuery(['#',key].join('')).val(obj[key]);
	}
});
});
