<?php

error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

/** Use filter in tables list
* @link http://www.adminer.org/plugins/#use
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerTablesFilter {

	function tablesPrint($tables) {
		?>
<script type="text/javascript">
    window.onload = function() {
        var val = getCookie('filter');
        if (typeof val !== 'undefined') {
            tablesFilter(val);
            document.getElementById('filter-field').value = val;
        }else{
            tablesFilter();
        }
    };

function tablesFilter(value) {
    setCookie('filter', value, 2);

	var tables = document.getElementById('tables').getElementsByTagName('span');
	for (var i = tables.length; i--; ) {
		var a = tables[i].children[1];
		var text = a.innerText || a.textContent;
		tables[i].className = (text.indexOf(value) == -1 ? 'hidden' : '');
		a.innerHTML = text.replace(value, '<b>' + value + '</b>');
	}
}

function setCookie (name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

</script>
<p class="jsonly"><input id="filter-field" onkeyup="tablesFilter(this.value);">
<?php
		echo "<p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>\n";
        $sel = lang('select');
		foreach ($tables as $table => $type) {
			echo '<span><a href="' . h(ME) . 'select=' . urlencode($table) . '"' . bold($_GET["select"] == $table) . ">" . $sel . "</a> ";
			echo '<a href="' . h(ME) . 'table=' . urlencode($table) . '"' . bold($_GET["table"] == $table) . ">" . h($table) . "</a><br></span>\n";
		}
		return true;
	}

}
