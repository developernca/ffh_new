/**
 * author Nyein Chan Aung<developernca@gmail.com>
 */

/**
 * 
 * @type type
 */
$(window).on("load", function () {
    setMainDivSize();
});

/**
 * 
 * @type type
 */
$(window).on("resize", function () {

});

function setMainDivSize() {
    var screen_width = window.screen.width;
    $("#id-div-maincontainer").css("width", screen_width + "px");
}