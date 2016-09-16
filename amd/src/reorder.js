/*global define*/
define(['jquery'], function($) {
    "use strict";

    function checkReorderItems(e) {
        var $target = $(e.currentTarget);
        var lastPosition = parseInt($target.data('lastPosition'), 10);
        var newPosition = parseInt($target.val(), 10);
        if (lastPosition === newPosition) {
            return; // Nothing has changed.
        }

        // Find the item being displaced (i.e. the one that already has the 'position' we are moving to).
        var $displace = null;
        var $moveSelects = $('select.moveto');
        $moveSelects.each(function() {
            var $this = $(this);
            if ($this.attr('id') !== $target.attr('id')) {
                if (parseInt($this.val(), 10) === newPosition) {
                    $displace = $this.closest('.fitem');
                    return false;
                }
            }
        });
        if (!$displace) {
            return;
        }

        // Put the moved item before, or after the 'displace' item, depending on whether we are moving up or down.
        var $moveItem = $target.closest('.fitem');
        if (newPosition < lastPosition) {
            $moveItem.insertBefore($displace);
        } else {
            $moveItem.insertAfter($displace);
        }

        // Update all the 'moveTo' selects.
        $moveSelects = $('select.moveto'); // Reload the list of 'moveto' selects, in the new order.
        $moveSelects.each(function(idx) {
            var $this = $(this);
            var position = idx + 1;
            $this.data('lastPosition', position);
            $this.val(position);
            $this.closest('.fitem').find('.localprofile-number').html(position);
        });

        // Flash the moved element.
        $moveItem.removeClass('localprofile-flash').addClass('localprofile-flash');
    }

    return {
        init: function() {
            var $form = $('#region-main form');
            $form.find('select.moveto').each(function() {
                var $this = $(this);
                $this.data('lastPosition', $this.val());
            });
            $form.on('change', 'select.moveto', checkReorderItems);
        }
    };
});