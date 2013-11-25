/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, globalstrict: true,
 latedef:true, noarg:true, noempty:true, nonew:true, undef:true, maxlen:256,
 strict:true, trailing:true, boss:true, browser:true, devel:true, jquery:true */
/*global chrome, safari, SAFARI, openTab, Ember, DS, localize */
'use strict';

jQuery('document').ready(function($) {
    $("[name='server_filter']").change(function() {
        $(this).parent().trigger('submit');
    });
    $("[name='domain_filter']").change(function() {
        $(this).parent().trigger('submit');
    });
});
