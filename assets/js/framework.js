Document.prototype.getAll = function (querySelector) {
	return this.querySelectorAll(querySelector);
};
Document.prototype.getOne = function (querySelector) {
	return this.querySelector(querySelector);
};

Document.prototype.onReady = function (callback, ...args) {
	this.addEventListener("DOMContentLoaded", function () {
		callback.call(null, args);
	});
};

HTMLElement.prototype.onClick = function (callback) {
	callback.call(this, this);
	return this;
};

HTMLElement.prototype.addClass = function (...classNames) {
	classNames.forEach((className) => {
		this.classList.add(className);
	});
	return this;
};
HTMLElement.prototype.addCopyListener = function () {
	let self = this;
	this.addClass("toCopy");
	this.addEventListener("click", () => {
		self.copyText();
	});
	return this;
};
HTMLElement.prototype.clearHTMLContent = function () {
	this.innerHTML = "";
	return this;
};
HTMLElement.prototype.containsClass = function (...className) {
	let res = true;
	className.forEach(
		(class_name) => (res = res && this.classList.contains(class_name))
	);
	return res;
};
HTMLElement.prototype.copyText = function () {
	let self = this;
	let input = document.createElement("input");
	input.setAttribute("type", "text");
	input.setAttribute("value", this.innerText);
	input.select();
	input.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(input.value);
	this.addClass("copied");
	setTimeout(() => {
		self.removeClass("copied");
	}, 500);
	return this;
};

/**
 *
 * @param {Function} callback
 * @return {HTMLElement}
 */
HTMLElement.prototype.onClick = function (callback, ...args) {
	let self = this;
	this.onclick = (event) => {
		callback.call(this, event, self, ...args);
	};
	return this;
};
/**
 *
 * @param {Function} callback
 * @return {HTMLInputElement}
 */
HTMLInputElement.prototype.onInput = function (callback) {
	let self = this;
	this.oninput = () => {
		callback.call(this, self.value, self);
	};
	return this;
};
HTMLElement.prototype.getComputedStyle = function () {
	return window.getComputedStyle(this);
};

HTMLInputElement.prototype.isChecked = function () {
	if (this.type == "checkbox" || this.type == "radio") {
		return this.checked === true;
	}
	return null;
};
HTMLElement.prototype.removeClass = function (...classNames) {
	classNames.forEach((className) => {
		this.classList.remove(className);
	});
	return this;
};
HTMLElement.prototype.toggleClass = function (...classNames) {
	classNames.forEach((className) => {
		this.classList.toggle(className);
	});
	return this;
};

NodeList.prototype.forEach = HTMLCollection.prototype.forEach =
	Array.prototype.forEach;

NodeList.prototype.addClass = HTMLCollection.prototype.addClass =
	function (...classNames) {
		this.forEach((el) => el.addClass(...classNames));
	};
NodeList.prototype.removeClass = HTMLCollection.prototype.removeClass =
	function (...classNames) {
		this.forEach((el) => el.removeClass(...classNames));
	};
NodeList.prototype.toggleClass = HTMLCollection.prototype.toggleClass =
	function (...classNames) {
		this.forEach((el) => el.toggleClass(...classNames));
	};
NodeList.prototype.onClick = HTMLCollection.prototype.onClick = function (
	callback
) {
	this.forEach((e, i, a) => {
		e.onClick(callback, i, a);
	});
};
