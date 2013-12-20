/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, globalstrict: true,
 latedef:true, noarg:true, noempty:true, nonew:true, undef:true, maxlen:256,
 strict:true, trailing:true, boss:true, browser:true, devel:true, jquery:true */
/*global chrome, safari, SAFARI, openTab, Ember, DS, localize */
'use strict';

jQuery(document).ready(function($) {
    $('.tablesorter').tablesorter({
        textExtraction: function(node) {
            var attr = $(node).attr('data-sort-value');
            if (typeof attr !== 'undefined' && attr !== false) {
                return attr;
            }
            return $(node).text();
        }
    });
    $('#box-table-a').stickyTableHeaders();
    $('#domainFilterDomain').change(function() {
        $('#domainFilter').submit();
    });
    $('.filterByDomain').click(function(e) {
        e.preventDefault();
        $('#domainFilterDomain').val(e.target.innerText);
        $('#domainFilter').submit();
    });
    $('#serverFilterServer').change(function() {
        $('#serverFilter').submit();
    });
});
