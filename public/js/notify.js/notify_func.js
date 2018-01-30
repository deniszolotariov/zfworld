/*
 *    Alerts
 * User: Денис
 * Date: 27.10.17
 */

function showNotify(msg, autohide) {
    var notif;

    autohide = autohide === undefined || autohide == true ? true : false;

    // message
    switch (typeof msg) {
        case 'object':
            if      ( msg['error']   != undefined ) notif = $.notify( msg['error'],   {className: "error"  , autoHide: autohide, autoHideDelay: 10000} );
            else if ( msg['warning'] != undefined ) notif = $.notify( msg['warning'], {className: "warning", autoHide: autohide		} );
            else if ( msg['info']    != undefined ) notif = $.notify( msg['info'],    {className: "info"   , autoHide: autohide		} );
            else if ( msg['success'] != undefined ) notif = $.notify( msg['success'], {className: "success", autoHide: autohide		} );
            else                                    notif = $.notify( msg,            {className: "info"   , autoHide: autohide		} );
            break;
        default:
        case 'string':
                                                    notif = $.notify( msg,            {className: "info"   , autoHide: autohide		} );
            break;
    }

    return notif;
}