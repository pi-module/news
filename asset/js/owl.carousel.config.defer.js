$('.owl-carousel').owlCarousel({
    //loop:true,
    //lazyLoad:true,
    margin: 5,
    nav: true,
    rtl: true,
    autoplay: true,
    dots: false,
    autoplayTimeout: 12000,
    autoplayHoverPause: true,
    navText: ['<i class="fa fa-angle-right"></i>', '<i class="fa fa-angle-left"></i>'],
    responsive: {
        0: {items: 2},
        600: {items: 4},
        1000: {items: 4}
    }
})