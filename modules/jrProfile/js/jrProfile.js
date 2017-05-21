/**
 * Get pulse counts for viewer
 * @param cb callback function
 */
function jrProfile_get_pulse_counts(cb)
{
    var url = core_system_url + '/' + jrProfile_url + '/get_pulse_counts/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        cache: false,
        success: function(n)
        {
            if (cb !== null && typeof cb == "function") {
                cb(n);
            }
        }
    });
}

/**
 * Reset Pulse count for a key
 * @param key string Key to reset
 * @param cb function callback
 */
function jrProfile_reset_pulse_key(key, cb)
{
    var url = core_system_url + '/' + jrProfile_url + '/reset_pulse_count/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {key: key}, function()
    {
        if (cb !== null && typeof cb == "function") {
            return cb();
        }
        return false;
    });
}