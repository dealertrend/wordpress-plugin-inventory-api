// parseUri 1.2.2
// (c) Steven Levithan <stevenlevithan.com>
// MIT License

function parseUri (str) {
    var o   = parseUri.options,
        m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
        uri = {},
        i   = 14;

    while (i--) uri[o.key[i]] = m[i] || "";

    uri[o.q.name] = {};
    uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
        if ($1) uri[o.q.name][$1] = $2;
    });

    return uri;
};

parseUri.options = {
    strictMode: false,
    key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
    q:   {
        name:   "queryKey",
        parser: /(?:^|&)([^&=]*)=?([^&]*)/g
    },
    parser: {
        strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
        loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
    }
};

var dealertrend = jQuery.noConflict();
var cookie_name = 'dealertrend-traffic-source';

var cookie_value = dealertrend.cookie( cookie_name );

var request_uri = parseUri( document.referrer );
var cookie_uri = parseUri( cookie_value );
var current_uri = parseUri( window.location );

if(
    (
        cookie_value == null ||
        cookie_value.length < 1 ||
        ( cookie_value != document.referrer && request_uri.host != cookie_uri.host )
    ) &&
    request_uri.host != current_uri.host ) {

    var new_value = null

    if( document.referrer != null && document.referrer.length > 0 ) {
        new_value = document.referrer;
    } else {
        new_value = 'direct';
    }

    dealertrend.cookie( cookie_name , new_value );
}
