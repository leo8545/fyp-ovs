jQuery(document).ready(function ($) {
	// Set price on single vehicle page
	$(".single_vehicle_page #v_price").text(
		$(".single_vehicle_page #vehicle_price").val()
	);
	$(".single_vehicle_page #vehicle_price").on("change", function () {
		$(".single_vehicle_page #v_price").text($(this).val());
	});

	/**
	 * Tabs
	 */
	$(".tab_wrapper .tab-links-wrapper > *").on("click", function () {
		var activeTab = $(this)
			.closest(".tab-links-wrapper")
			.find(".tab-link.active-tab");
		var activeContent = $(this)
			.closest(".tab_wrapper")
			.find(".tab-contents-wrapper > .tab-content.active-tab");
		if ($(this).attr("id") !== $(activeTab).attr("id")) {
			$(activeTab).removeClass("active-tab");
			$(this).addClass("active-tab");
			if ($(this).attr("id") !== $(activeContent).data("section")) {
				$(activeContent).removeClass("active-tab");
				$(this)
					.closest(".tab_wrapper")
					.find(
						".tab-contents-wrapper > .tab-content[data-section='" +
							$(this).attr("id") +
							"']"
					)
					.addClass("active-tab");
			}
		}
	});

	// Header search bar
	let res = "";
	$("#search").on("keyup", (e) => {
		console.log("clickd");
		res = $("#search").val();
		if (res.length >= 3) {
			$(".search-results").show();
			$.ajax({
				type: "post",
				url: `/vehicle/search`,
				data: { search: res },
				// dataType: "JSON",
				success: function (_response) {
					// console.log(_response);
					let response = JSON.parse(_response);
					let html = '<ul class="search_results_items">';

					if (!response.notfound) {
						$.each(response, function (key, item) {
							console.log(item);
							const { vehicle_id, vehicle_model, vehicle_manufacturer } = item;
							html += `<li id='item-${vehicle_id}'><a href='/vehicle/${vehicle_id}'><span class='model'>${vehicle_model}</span> <span class='manufacturer'>${vehicle_manufacturer}</span></a></li>`;
						});
					} else {
						html += `<li class='notfound'><a>Such model does not exist</a></li>`;
					}

					html += "</ul>";
					$(".search-results").html(html);
				},
			});
		}
		if ($("#search").val().length === 0) {
			$(".search-results").hide();
		}
	});
	$(".vehicle_booking_form").submit((e) => {
		e.preventDefault();
		var vehicleId = $(".vehicle_booking_form").data("vehicle_id");
		var response = {};
		$.ajax({
			data: {
				vehicle_id: vehicleId,
				order_price: $("#vehicle_price").val(),
			},
			url: "/add-to-cart",
			method: "post",
			success: (_response) => {
				console.log(_response);
			},
		});
	});
	$(".checkout-form .checkout_payment_options").on("click", (e) => {
		var paymentOption = $(
			`.payment_options input[name="checkout[payment_option]"]:checked`
		).val();
		switch (paymentOption) {
			case "credit":
			case "debit":
				$(".cod_details").hide();
				$(".card_details").show();
				$(".cod_details input, .cod_details textarea").prop("required", false);
				$(".card_details input, .card_details textarea").prop("required", true);
				break;
			case "cod":
				$(".card_details").hide();
				$(".cod_details").show();
				$(".card_details input, .card_details textarea").prop(
					"required",
					false
				);
				$(".cod_details input, .cod_details textarea").prop("required", true);
				break;
		}
		console.log(paymentOption);
	});
	$(".checkout-form").submit((e) => {
		e.preventDefault();
		var paymentOption = $(
			`.payment_options input[name="checkout[payment_option]"]:checked`
		).val();
		var cardNumber = $("#card_number").val();
		var streetAddress = $("#cod_address").val();
		var city = $("#cod_city").val();
		var orderIds = $("#order_ids").val()
		var data = { paymentOption, orderIds };
		switch (paymentOption) {
			case "credit":
			case "debit":
				Object.assign(data, {
					cardNumber,
				});
				break;
			case "cod":
				Object.assign(data, {
					streetAddress,
					city,
				});
				break;
		}
		$.ajax({
			data,
			method: "post",
			url: "/checkout",
			success: (_response) => {
				console.log(_response);
			},
		});
	});
});
