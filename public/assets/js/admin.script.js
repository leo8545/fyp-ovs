/**
 * Adds menu open class to open menu list item
 */
const path = window.location.pathname;
document.querySelectorAll("aside ul > li.menu-item").forEach(li => {
	li.querySelectorAll("a").forEach(a => {
		var href = a.getAttribute("href");
		var w = "";
		if (href.indexOf("/", 7) !== -1)
			// searches after '/admin/'
			w = href.slice(7, href.indexOf("/", 7));
		else w = href.slice(7);

		if (w && path.indexOf(w) > -1) li.classList.add("menu-open");
	});
	li.querySelectorAll("li.sub-menu-item").forEach(sub => {
		sub.querySelectorAll("a").forEach(a => {
			if (path.indexOf(a.getAttribute("href")) > -1)
				sub.classList.add("menu-open");
		});
	});
});

/**
 * Confirmation box for deleting user
 */
const deleteUserBtns = document.querySelectorAll(
	".delete-user, .delete-vehicle, .delete-order"
);
deleteUserBtns.forEach((v, i) => {
	v.addEventListener("click", e => {
		e.preventDefault();
		const username = v.closest("tr").querySelector(".username, .vehicle_name, .order_id").textContent;
		Swal.fire({
			title: `Are you sure to delete ${username}?`,
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {			
				Swal.fire(
					'Deleted!',
					`${username} has been deleted.`,
					'success'
				);
				window.location.href = v.href;
			}
		})
	});
});

const urlParams = new URLSearchParams(window.location.search);
let pageNo = urlParams.get('page');
document.addEventListener("DOMContentLoaded", (e) => {
	if(pageNo === null) pageNo = 1;
	document.querySelector(`.pagination a:nth-child(${pageNo})`).classList.add("active");
});

(function($) {
	$(document).ready(function() {
		$(".wyswyg").trumbowyg();
	});
})(jQuery);
