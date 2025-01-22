window.addEventListener('load', () => {
    const $ = jQuery;

    /* Make sure the codes in this file runs only once, even if enqueued twice */
    if ( typeof window.directorist_catloc_executed === 'undefined' ) {
        window.directorist_catloc_executed = true;
    } else {
        return;
    }

    /* Category card grid three width/height adjustment */
    const categoryCard = document.querySelectorAll('.directorist-categories__single--style-three');
    if(categoryCard){
        categoryCard.forEach(elm =>{
            const categoryCardWidth = elm.offsetWidth;
            elm.style.setProperty('--directorist-category-box-width', `${categoryCardWidth}px`);
        })
    }

    /* Taxonomy list dropdown */
    function categoryDropdown(selector, parent){
        var categoryListToggle = document.querySelectorAll(selector);
        categoryListToggle.forEach(function(item) {
            item.addEventListener('click', function(e) {
                const categoryName = item.querySelector('.directorist-taxonomy-list__name');
                if(e.target !== categoryName){
                    e.preventDefault();
                    this.classList.toggle('directorist-taxonomy-list__toggle--open');
                }
            });
        });
    }
    categoryDropdown('.directorist-taxonomy-list-one .directorist-taxonomy-list__toggle', '.directorist-taxonomy-list-one .directorist-taxonomy-list');
    categoryDropdown('.directorist-taxonomy-list-one .directorist-taxonomy-list__sub-item-toggle', '.directorist-taxonomy-list-one .directorist-taxonomy-list');

    // Taxonomy Ajax
    $(document).on('click', '.directorist-categories .directorist-pagination a', function(e) {
        taxonomyPagination(e, $(this), '.directorist-categories')
    });
    
    $(document).on('click', '.directorist-location .directorist-pagination a', function(e) {
        taxonomyPagination(e, $(this), '.directorist-location')
    });
    
    function taxonomyPagination(e, current, selector) {
        e.preventDefault();
        
        const page = current.html();
        const attrs = $(current.closest(selector)).data('attrs');

        $.ajax({
            url: directorist.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'directorist_taxonomy_pagination',
                nonce: directorist.directorist_nonce,
                page:parseInt( page ),
                attrs
            },
            beforeSend: function () {
                $(selector).addClass('atbdp-form-fade'); // Optional loader
            },
            success: function (response) {
                if (response.success) {

                    const { content } = response.data;

                    // Cache the #directorist container
                    const $directorist = $(current).closest('#directorist');

                    // Cache the navigation area
                    const $navAreaContent = $directorist.find('.directorist-col-12:has(.directorist-type-nav)').html();
                    
                    // Extract and update the .directorist-categories element from the `content` data
                    const $newCategories = $(content).find(selector);
                    $newCategories.find('.directorist-type-nav').html($navAreaContent);

                    $(selector).removeClass('atbdp-form-fade'); // Optional loader

                    // Replace the .directorist-categories content in the original container
                    $directorist.find(selector).html($newCategories.html());

                } else {
                    console.error('Error loading categories');
                }
            },
            complete: function () {
                $(selector).removeClass('atbdp-form-fade');
            }
        });
    }
});