export function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

export function clipboard() {
    let popOvers = jQuery('[data-bs-toggle="popover"]');
    if ( popOvers.length ) {
        new bootstrap.Popover( '[data-bs-toggle="popover"]' );
    }
    jQuery(document).on( 'click', '.gpls-general-clipboard-icon-btn', function(e) {
        e.preventDefault();
        let btn = jQuery(this);
        let target = btn.data('target');
        let text   = jQuery( target ).text();
        navigator.clipboard.writeText( text );

        setTimeout(() => {
            let popover = bootstrap.Popover.getOrCreateInstance( btn );
            popover.hide();
        }, 1000 );
    });
}
