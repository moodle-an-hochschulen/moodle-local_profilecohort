/**
 * Local plugin "Profile field based cohort membership" - JS code for reordering rules
 *
 * @package   local_profilecohort
 * @copyright 2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Note: The @this comments throughout this file are just there to keep grunt satisfied */

define(['jquery'], function($) {
    "use strict";
    var SELECTORS = {
        MOVETO: 'select.moveto, .moveto select',
        FIELDWRAPPER: '.localprofile-fieldwrapper',
        RULENUMBER: '.localprofile-number'
    };

    /**
     * The last rule should not show a 'and next rule' checkbox, but all the rest of the rules should.
     */
    function showHideAndNextCheckbox() {
        var $andNextRule = $('#region-main form input.andnextrule');
        if ($andNextRule.closest('label').length) {
            // Boost theme wraps labels around input tags, instead of spans.
            $andNextRule.closest('label').removeClass('hidden').last().addClass('hidden');
        } else {
            $andNextRule.closest('span').removeClass('hidden').last().addClass('hidden');
        }
    }

    /**
     * Remove all the divs that were wrapping rules that are combined together.
     */
    function removeCombinedDivs() {
        var $form = $('#region-main form');
        $form.find(SELECTORS.FIELDWRAPPER).removeClass('localprofile-flash').each(/* @this */function() {
            var $this = $(this);
            if ($this.closest('.localprofile-combined').length) {
                $this.unwrap();
            }
        });
    }

    /**
     * Find all rules that are combined together and wrap them in a div to visually indicate this.
     */
    function addCombinedDivs() {
        var $collection = null;
        var $form = $('#region-main form');
        $form.find(SELECTORS.FIELDWRAPPER).each(/* @this */function() {
            var $this = $(this);
            var $andnextrule = $this.find('input.andnextrule');
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

    /**
     * Refresh the divs that show which rules are combined together.
     */
    function updateCombinedDivs() {
        removeCombinedDivs();
        addCombinedDivs();
    }

    /**
     * Check if the item has been reordered and, if it has, insert it into the new position.
     * @param {Object} e The event object.
     */
    function checkReorderItems(e) {
        var $target = $(e.currentTarget);
        var lastPosition = parseInt($target.data('lastPosition'), 10);
        var newPosition = parseInt($target.val(), 10);
        if (lastPosition === newPosition) {
            return; // Nothing has changed.
        }

        // Find the item being displaced (i.e. the one that already has the 'position' we are moving to).
        var $displace = null;
        var $moveSelects = $(SELECTORS.MOVETO);
        $moveSelects.each(/* @this */function() {
            var $this = $(this);
            if ($this.attr('id') !== $target.attr('id')) {
                if (parseInt($this.val(), 10) === newPosition) {
                    $displace = $this.closest(SELECTORS.FIELDWRAPPER);
                    return false;
                }
            }
            return true;
        });
        if (!$displace) {
            return;
        }

        // Now we know we are moving elements, remove all 'combine with next rule' divs.
        removeCombinedDivs();

        // Put the moved item before, or after the 'displace' item, depending on whether we are moving up or down.
        var $moveItem = $target.closest(SELECTORS.FIELDWRAPPER);
        if (newPosition < lastPosition) {
            $moveItem.insertBefore($displace);
        } else {
            $moveItem.insertAfter($displace);
        }

        // Update all the 'moveTo' selects.
        $moveSelects = $(SELECTORS.MOVETO); // Reload the list of 'moveto' selects, in the new order.
        $moveSelects.each(/* @this */function(idx) {
            var $this = $(this);
            var position = idx + 1;
            $this.data('lastPosition', position);
            $this.val(position);
            $this.closest(SELECTORS.FIELDWRAPPER).find(SELECTORS.RULENUMBER).html(position);
        });

        // Flash the moved element.
        $moveItem.removeClass('localprofile-flash').addClass('localprofile-flash');

        // Replace the 'combine with next rule' divs.
        addCombinedDivs();
    }

    /**
     * Highlight each of the rules that will be deleted when the form is next saved.
     */
    function showRulesToBeDeleted() {
        var $deleteRule = $('#region-main form input.deleterule');
        $deleteRule.each(/* @this */function() {
            var $this = $(this);
            var $container = $this.closest('.localprofile-fieldwrapper');
            if ($this.prop('checked')) {
                $container.addClass('todelete');
                $container.find(':input:not(.deleterule)').prop('disabled', true);
            } else {
                $container.removeClass('todelete');
                $container.find(':input').prop('disabled', false);
            }
        });
    }

    return {
        init: function() {
            var $form = $('#region-main form');
            $form.find(SELECTORS.MOVETO).each(/* @this */function() {
                var $this = $(this);
                $this.data('lastPosition', $this.val());
            });
            $form.on('change', SELECTORS.MOVETO, checkReorderItems);
            $form.on('change', 'input.andnextrule', updateCombinedDivs);
            $form.on('change', 'input.deleterule', showRulesToBeDeleted);
            addCombinedDivs();
        }
    };
});
