/*
 *    Loader
 * User: Денис
 * Date: 27.10.17
 */

$(document).ready(function() {
    $('body').append('<div style="display: none;" class="loader-general loader-global" id="view-loader" ></div>');
});
/*
            Loader
*/
function showLoader(loader_id) {
    // loader
    if (loader_id === undefined)
        loader_id = '#view-loader.loader-global';

    // show reload gif
    $(loader_id).css('display', 'block');
}
function hideLoader(loader_id) {
    // loader
    if (loader_id === undefined)
        loader_id = '#view-loader.loader-global';

    // hide reload gif
    $(loader_id).css('display', 'none');
}

function startLoader(loader_id) {
    // loader
    showLoader(loader_id);
}
function endLoader(msg, loader_id) {
    // loader
    hideLoader(loader_id);

    // message
    showNotify( msg );
}