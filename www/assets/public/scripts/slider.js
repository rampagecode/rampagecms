(function() {
    var curSlide = 1;
    var minSlide = 1;
    var maxSlide = 5;

    setInterval( function() {
        curSlide = curSlide + 1 > maxSlide ? minSlide : curSlide + 1;

        for( var i = 1; i <= maxSlide; i++ ) {
            document.getElementsByClassName('slide' + i)[0].style.opacity = i === curSlide ? 1 : 0;
        }

        moveSlideCover();
    }, 5000);

    function moveSlideCover() {
        document.getElementsByClassName('slideCover')[0].style.left = '-108px';

        setTimeout(function () {
            document.getElementsByClassName('slideCover')[0].style.left = '800px';
        }, 4500);
    }

    moveSlideCover();
}());