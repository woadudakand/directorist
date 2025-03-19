/**
 * Update listings grid view count.
 */
jQuery(($) => {
    const updateMarkup = (viewCounts) => {
        for (const [id, count] of Object.entries(viewCounts)) {
            const $el = $(`.directorist-view-count[data-id="${id}"]`);
            const $elIcon = $el.find('.directorist-icon-mask');

            if ($elIcon.length) {
                $elIcon[0].nextSibling.textContent = count;
            } else {
                $el.text(count);
            }
        }
    };

    const ids = [];
    $('.directorist-view-count[data-id]').each((i, item) => {
        ids.push(+item.dataset.id);
    });

    if (ids.length === 0) {
        return;
    }

    let cache = window.localStorage?.getItem('directorist_view_count');
    const CACHE_EXPIRATION = 1000 * 60 * 60 * 5; // 5 hours.

    if (cache) {
        cache = JSON.parse(cache);

        if (cache?.viewCount && cache?.lastUpdated && (Date.now() - cache.lastUpdated) < CACHE_EXPIRATION) {
            updateMarkup(cache.viewCount);
            return;
        }
    }

    $.post(
        directorist.ajax_url,
        {
            action: 'directorist_update_view_count',
            nonce: directorist.directorist_nonce,
            ids: ids,
        },
        (response) => {
            if (!response.success) {
                console.warn( response.data.message );
                return;
            }

            updateMarkup(response.data.view_count);

            window.localStorage?.setItem('directorist_view_count', JSON.stringify({
                lastUpdated: Date.now(),
                viewCount: response.data.view_count,
            }));
        }
    );
});
