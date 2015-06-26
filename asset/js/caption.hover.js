/* From : http://alijafarian.com/jquery-image-hover-captions/ */
$(document).ready(function () {
    $('.hover-captions').hover(
        function () {
            $(this).find('.hover-caption').slideDown(250);
        },
        function () {
            $(this).find('.hover-caption').slideUp(250);
        }
    );
});