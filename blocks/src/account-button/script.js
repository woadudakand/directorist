'use strict';

function authorDropdownActive() {
	const authorTriggers = document.querySelectorAll(
		'.directorist-account-block-logged-mode .avatar'
	);

	const requiredElements = Object.values(authorTriggers);
	if (requiredElements.some(element => !element)) {
		return;
	}

	authorTriggers.forEach( ( authorTrigger ) => {
		const parentBlock = authorTrigger.closest(
			'.directorist-account-block-logged-mode'
		);
		let shade = parentBlock.querySelector(
			'.directorist-account-block-logged-mode__overlay'
		);
		const authorDropdown = parentBlock.querySelector(
			'.directorist-account-block-logged-mode__navigation'
		);

		function toggleAuthorDropdown() {
			if ( authorDropdown && shade ) {
				authorDropdown.classList.toggle( 'show' );
				shade.classList.toggle( 'show' );
			}
		}

		function removeDropdown() {
			if ( authorDropdown && shade ) {
				authorDropdown.classList.remove( 'show' );
				shade.classList.remove( 'show' );
			}
		}

		if ( ! shade ) {
			shade = document.createElement( 'div' );
			shade.className = 'directorist-account-block-logged-mode__overlay';
			parentBlock.appendChild( shade );
		}

		if ( authorTrigger && shade ) {
			authorTrigger.addEventListener( 'click', toggleAuthorDropdown );
			shade.addEventListener( 'click', removeDropdown );
		}
	} );
}

document.addEventListener( 'DOMContentLoaded', authorDropdownActive );

function login() {
	const elements = {
		clickBtns: document.querySelectorAll(
			'.directorist-account-block-logout-mode .wp-block-button__link'
		),

		loginInBtn: document.querySelector( '.directory_regi_btn button' ),
		popup: document.getElementById(
			'directorist-account-block-login-modal'
		),
		closeBtn: document.querySelector(
			'#directorist-account-block-login-modal .directorist-account-block-close'
		),
	};

	// Check if all required elements exist
	const requiredElements = Object.values(elements);
	if (requiredElements.some(element => !element)) {
		return;
	}
	
	const showModal = (modal) => {
        if (modal) {
            modal.style.display = 'block';
        }
    };
    const hideModal = (modal) => {
        if (modal) {
            modal.style.display = 'none';
        }
    };

	const toggleModals = ( hide, show ) => {
		hideModal( hide );
		showModal( show );
	};

	elements.clickBtns.forEach( ( clickBtn, index ) => {
		clickBtn.addEventListener( 'click', () => showModal( elements.popup ) );
	} );

	if ( elements.closeBtn ) {
		elements.closeBtn.addEventListener( 'click', () =>
			hideModal( elements.popup )
		);
	}

	if ( elements.popup ) {
		elements.popup.addEventListener( 'click', ( event ) => {
			if ( event.target === elements.popup ) hideModal( elements.popup );
		} );
	}

	if ( elements.loginInBtn ) {
		elements.loginInBtn.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			toggleModals( elements.signupPopup, elements.popup );
		} );
	}
}

document.addEventListener( 'DOMContentLoaded', login );