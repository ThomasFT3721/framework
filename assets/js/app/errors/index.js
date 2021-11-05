document.onReady(() => {
	document.getAll("#errors>.header>.element").onClick((_, el, i, arr) => {
		arr.forEach((e) => e.removeClass("active"));
		document.getAll("#errors>.content>.element").removeClass("active");
		el.addClass("active");
		document
			.getOne("#errors>.content>#content_" + el.getAttribute("id"))
			.addClass("active");
	});
	document.getAll("#content_stack_trace>.left>.step").onClick((_, el, i, arr) => {
		arr.forEach((e) => e.removeClass("active"));
		document.getAll("#content_stack_trace>.right>.step").removeClass("active");
		el.addClass("active");
		document
			.getOne("#content_stack_trace>.right>#step_" + el.getAttribute("data-number"))
			.addClass("active");
	});
});
