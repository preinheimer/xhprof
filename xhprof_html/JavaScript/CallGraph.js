/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, globalstrict: true,
 latedef:true, noarg:true, noempty:true, nonew:true, undef:true, maxlen:256,
 strict:true, trailing:true, boss:true, browser:true, devel:true, jquery:true */
/*global chrome, safari, SAFARI, openTab, Ember, DS, localize, Raphael */
'use strict';

jQuery(document).ready(function($) {
    $('.loading').show();
    $.ajax({
        url: './callGraphRenderer.php' + window.location.search,
        cache: false
    })
        .done(function(html) {
            $('.loading').hide();
            $('#callGraph').append(html);
            $('#callGraph svg').panzoom({
                $zoomIn: $('.zoomIn'),
                $zoomOut: $('.zoomOut'),
                $zoomRange: $('.zoomRange'),
                $reset: $('.zoomReset'),
                increment: 0.05,
                rangeStep: 0.05,
                startTransform: 'matrix(0.50, 0, 0, 0.50, -' +
                    Math.ceil($('#callGraph svg').width() / 2) + ', -' +
                    Math.ceil($('#callGraph svg').height() / 4.5) + ')',
                minScale: 0.1,
                maxScale: 1
            }).panzoom('zoom');
            $('#callGraph').on('mousewheel.focal', function(e) {
                e.preventDefault();
                var delta = e.delta || e.originalEvent.wheelDelta;
                var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
                $('#callGraph svg').panzoom('zoom', zoomOut, {
                    increment: 0.01,
                    focal: e
                });
            });
            $('#callGraph').on('dblclick', function(e) {
                $('#callGraph svg').panzoom(
                    'zoom',
                    {
                        scale: Number($('.zoomRange').val()) + 0.05,
                        focal: e
                    }
                );
            });
        });
});
