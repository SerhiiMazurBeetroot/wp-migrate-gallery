(function( $ ) {
	'use strict';

	/**
	 * 
	 */
	function init() {
		import_gallery()
		parse_images()
	}

	/**
	 * 
	 */
	function import_gallery() {
		const btn = document.querySelector('#import_submit')
		const error_wrap = document.querySelector('#error_wrap')

		btn && btn.addEventListener('click', (e) => {
			e.preventDefault()
			call_process_ajax( 'process_import_gallery', btn )
		})
	}

	/**
	 * 
	 */
	function parse_images() {
		const btn = document.querySelector('#parse_images')
		const notice_wrap = document.querySelector('#notice_wrap')

		btn && btn.addEventListener('click', (e) => {
			e.preventDefault()

			let selectedIndex = parserOption.options.selectedIndex
			let currentFn = parserOption.options[selectedIndex].dataset.fn

			call_process_ajax( `process_${currentFn}`, btn )
		})

		let parserOption = document.querySelector(`#parser_option`)

		if(parserOption) {
			let selectedIndex = parserOption.options.selectedIndex
			let currentFn = parserOption.options[selectedIndex].dataset.fn
			visivility_textarea_parse_by(currentFn)
	
			parserOption.addEventListener('change', function(e) {
				let currentFn = e.target.options[e.target.selectedIndex].dataset.fn
				visivility_textarea_parse_by(currentFn)
			})
		}
	}

	/**
	 * 
	 */
	function visivility_textarea_parse_by(currentFn) {
		let wrappers = document.querySelectorAll(`.wrapper_by_selector`)

		if(currentFn === "parse_images_by") {
			wrappers.forEach(item => item.classList.add("active"))
		} else {
			wrappers.forEach(item => item.classList.remove("active"))
		}
	}

	/**
	 * 
	 */
	function call_process_ajax( action, btn ) {
		const loader = document.querySelector('#loader')

		const formData = new FormData()
		formData.append("action", action)
		formData.append("ajax_nonce", wpmg_vars.ajax_nonce)

		loader.classList.add('active')
		btn.disabled = true

		fetch(wpmg_vars.endpoint + action, {
			method: "POST",
			body: formData,
			headers: {
				Accept: "application/json", 
			}
		})
		.then(response => {
			loader.classList.remove('active')
			btn.disabled = false

			if (response.ok) {
				return response.json()
			} else {
				throw new Error('Failed to fetch data')
			}
		})
		.then(data => {
			if(data.alert) {
				alert(data.alert)
			}
		})
		.catch(error => {
			console.error('Error: ')
			console.log(error)
		})
	}

	document.addEventListener("DOMContentLoaded",function() {
		init()
	})
})( jQuery );
