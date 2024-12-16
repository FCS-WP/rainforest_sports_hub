(function($, api) {
    "use strict";

    api('page_preloader', function (value){
        value.bind(function ( newval ){
            console.log(newval);
        });
    });

})(jQuery, wp.customize);