/*global define*/
define(['jquery', 'jqueryui'], function($) {
    return {
        init: function() {
            $('.profilecohort-userlist').accordion({
                header: '.profilecohort-cohortname'
            });
        }
    };
});
