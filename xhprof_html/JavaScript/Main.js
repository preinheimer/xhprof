/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, globalstrict: true,
 latedef:true, noarg:true, noempty:true, nonew:true, undef:true, maxlen:256,
 strict:true, trailing:true, boss:true, browser:true, devel:true, jquery:true */
/*global chrome, safari, SAFARI, openTab, Ember, DS, localize */
'use strict';

jQuery(document).ready(function($) {
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        'title-numeric-pre': function(a) {
            var x = a.match(/title="*(-?[0-9\.]+)/)[1];
            return parseFloat(x);
        },

        'title-numeric-asc': function(a, b) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        'title-numeric-desc': function(a, b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
    var sortableTable = $('.tablesorter').dataTable({
        'bFilter': true,
        'iDisplayLength': 50,
        /* Disable initial sort */
        'aaSorting': [],
        'aoColumnDefs': [{
            'sType': 'title-numeric',
            'aTargets': ['title-numeric']
        }],
        'sDom': 'RClfrtip'
    });
    new FixedHeader(document.getElementById('box-table-a'));
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
