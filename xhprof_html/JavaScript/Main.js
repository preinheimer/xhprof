/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, globalstrict: true,
 latedef:true, noarg:true, noempty:true, nonew:true, undef:true, maxlen:256,
 strict:true, trailing:true, boss:true, browser:true, devel:true, jquery:true */
/*global chrome, safari, SAFARI, openTab, Ember, DS, localize */
'use strict';

jQuery(document).ready(function($) {
    $('.tablesorter').tablesorter({
        textExtraction: function(node) {
            var attr = $(node).attr('title');
            if (typeof attr !== 'undefined' && attr !== false) {
                return attr;
            }
            return $(node).text();
        }
    });
    // This kills scrolling performance for large tables
    //$('#box-table-a').stickyTableHeaders();
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
    /*
    $('[title!=""]').qtip({
        style: {
            classes: 'qtip-rounded qtip-shadow'
        },
        title: {
            button: true
        },
        show: {
            solo: true,
            effect: function() {
                $(this).fadeTo(200, 1);
            }

        },
        hide: {
            event: false,
            inactive: 1500,
            effect: function() {
                $(this).fadeTo(200, 0);
            }
        },
        position: {
            my: 'bottom cencter',
            at: 'top cencter'
        }
    });
    */
});
