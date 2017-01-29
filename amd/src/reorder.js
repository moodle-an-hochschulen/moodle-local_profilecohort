/**
 * Local plugin "Profile field based cohort membership" - JS code for reordering rules
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* This comment is just there to keep grunt satisfied and won't be processed at runtime */
/* global define */

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

        // Now we know we are moving elements, remove all 'combine with next rule' divs.
        removeCombinedDivs();

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

        // Replace the 'combine with next rule' divs.
        addCombinedDivs();
    }

    function removeCombinedDivs() {
        var $form = $('#region-main form');
        $form.find('.fitem_fgroup').each(function() {
            var $this = $(this);
            if ($this.closest('.localprofile-combined').length) {
                $this.unwrap();
            }
        });
    }

    function addCombinedDivs() {
        var $collection = null;
        var $form = $('#region-main form');
        $form.find('.fitem_fgroup').each(function() {
            var $this = $(this);
            var $andnextrule = $this.find('.andnextrule');
            if (!$andnextrule.length) {
                return;
            }
            if ($andnextrule.prop('checked')) {
                if ($collection) {
                    $collection = $collection.add($this);
                } else {
                    $collection = $this;
                }
            } else {
                if ($collection) {
                    $collection = $collection.add($this);
                    $collection.wrapAll('<div class="localprofile-combined" />');
                    $collection = null;
                }
            }
        });
        if ($collection) {
            $collection.wrapAll('<div class="localprofile-combined" />');
            $collection = null;
        }
        showHideAndNextCheckbox();
    }

    function showHideAndNextCheckbox() {
        var $andNextRule = $('#region-main form .andnextrule');
        $andNextRule.closest('span').removeClass('hidden').last().addClass('hidden');
    }

    function updateCombinedDivs() {
        removeCombinedDivs();
        addCombinedDivs();
    }

    return {
        init: function() {
            var $form = $('#region-main form');
            $form.find('select.moveto').each(function() {
                var $this = $(this);
                $this.data('lastPosition', $this.val());
            });
            $form.on('change', 'select.moveto', checkReorderItems);
            $form.on('change', 'input.andnextrule', updateCombinedDivs);
            addCombinedDivs();
        }
    };
});
