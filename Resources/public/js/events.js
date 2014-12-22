/**
 * Created by stijnvoss on 22/12/14.
 */

$(document).ready(function() {
    $('.cropable').each(
        function(k,v) {
            $(v).click(function(event) {
                    event.preventDefault();
                    var c = new Cropper($(v));
                    c.show();
                }

            );

        }
    );
});