jQuery(document).ready(function($){
	$('.slider-home').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		// autoplay: true,
		autoplaySpeed: 2000,
	});
	// Set price on single vehicle page
	$(".single_vehicle_page #v_price").text($(".single_vehicle_page #vehicle_price").val())
	$(".single_vehicle_page #vehicle_price").on("change", function() {
		$(".single_vehicle_page #v_price").text($(this).val())
	})
	// new Glide('.glide', {
	// 	gap: 0,
	// 	// perView: 3,
	// 	// autoplay: 2000

	// }).mount();

	/**
	 * Tabs
	 */
	$(".tab_wrapper .tab-links-wrapper > *").on("click", function() {
		var activeTab = $(this).closest(".tab-links-wrapper").find(".tab-link.active-tab");
		var activeContent = $(this).closest(".tab_wrapper").find(".tab-contents-wrapper > .tab-content.active-tab");
		if($(this).attr("id") !== $(activeTab).attr("id")) {
			$(activeTab).removeClass("active-tab");
			$(this).addClass("active-tab")
			if( $(this).attr("id") !== $(activeContent).data("section") ) {
				$(activeContent).removeClass("active-tab")
				$(this).closest(".tab_wrapper").find(".tab-contents-wrapper > .tab-content[data-section='"+$(this).attr("id")+("']")).addClass("active-tab")
			}
		}
	})

	// Header search bar
	let res = '';
	$('#search').on('keyup', e => {
		res = $('#search').val();
		if( res.length >= 3 ) {
			$('.search-results').show();
			$.ajax({
				type: "post",
				url: `/vehicle/search`,
				data: {"search": res},
				// dataType: "JSON",
				success: function (_response) {
					console.log(_response);
					let response = JSON.parse(_response);
					let html = '<ul class="search_results_items">';

					if( !response.notfound ) {
						$.each(response, function(key, item){
							console.log(item);
							const { vehicle_id, vehicle_model, vehicle_manufacturer } = item;
							html += `<li id='item-${vehicle_id}'><a href='/vehicle/${vehicle_id}'><span class='model'>${vehicle_model}</span> <span class='manufacturer'>${vehicle_manufacturer}</span></a></li>`;
						})
					} else {
						html += `<li class='notfound'><a>Such model does not exist</a></li>`
					}
					
					html += '</ul>';
					$('.search-results').html(html)
				}
			});
		}
		if( $('#search').val().length === 0 ) {
			$('.search-results').hide();
		}
	})

	// Wrap every letter in a span
var textWrapper = document.querySelector('.ml3');
textWrapper.innerHTML = textWrapper.textContent.replace(/\S/g, "<span class='letter'>$&</span>");

anime.timeline({loop: true})
  .add({
    targets: '.ml3 .letter',
    opacity: [0,1],
    easing: "easeInOutQuad",
    duration: 2250,
    delay: (el, i) => 150 * (i+1)
  }).add({
    targets: '.ml3',
    opacity: 0,
    duration: 1000,
    // easing: "easeOutExpo",
    delay: 1000
  });



});
