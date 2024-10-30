jQuery(document).ready(function(){
    multiscrollInitial();
    function multiscrollInitial() {
        $('#CSHMSMultiScroll').multiscroll({
            navigation: true,
            navigationPosition: 'right',
            afterRender: function(){

            }
        });
    }

    var main = $('#CSHMSMultiScroll'),
        leftSideInside = main.find('.ms-left .wpb_wrapper .cshms-row'),
        rightSideInside = main.find('.ms-right .cshms-row'),
        leftSide = main.find('.ms-left .wpb_wrapper'),
        rightSide = main.find('.ms-right'),
        changed = false;


    var firstLeftSide = [];
    leftSideInside.each(function () {
        firstLeftSide.push($(this).clone())
    });

    var firstRightSide  = [];
    rightSideInside.each(function () {
        $(this).addClass('spot-right-side');
        firstRightSide.push($(this).clone())
    });

    var storageStyleObject = [], storageStyle = [];

    var initHandleSpliter = {
        init: function () {
            this.savingStyleAttributes();
            this.resizeEvent(this);
        },
        handleOrderBigScreen: function ( ) {
            if(changed === true) {
                /*for(key in firstLeftSide) {
                    leftSide.append(firstLeftSide[key]);
                }*/

                $(document).find('.ms-left .spot-right-side').remove();
                rightSideInside.each(function () {
                    $(this).css('height', '100%');
                });
                rightSide.show();

                $('html, body').css({'height':'100%','overflow':'hidden'});
                $('#multiscroll-nav').css('display', 'block');
                $.fn.multiscroll.build();
                $.fn.multiscroll.moveTo(1);
                this.turnBackStyleAttributes();
            }
        },
        handleOrderSmallScreen: function () {
            if(changed === true) {
                var reverse = firstRightSide;
                reverse = reverse.reverse();


                $('.ms-left, .ms-right, .spot-right-side, .ms-section, .ms-tableCell, html, body').each( function() {
                    $(this).attr('style','');
                    $('#multiscroll-nav').css('display','none');
                });
            }
            var count = 0;
            leftSideInside.each(function () {
                $(this).after(reverse[count]);
                count+=1;
            });

            $.fn.multiscroll.destroy();
            rightSide.hide();
            $.fn.multiscroll.moveTo(1);

        },
        savingStyleAttributes: function() {
            leftSide.find('[style]').each(function () {
                if($(this).attr('class') != undefined) {
                    var cLass = $(this).attr('class'),
                        reClassString = '';
                    cLass = cLass.split(' ');


                    for(var key in cLass) {
                        reClassString += '.' + cLass[key] + ' ';
                    }

                    storageStyleObject.push(reClassString);
                    storageStyle.push($(this).attr('style'));
                }
            });
            console.log(storageStyleObject);
            console.log(storageStyle);
        },
        turnBackStyleAttributes : function () {
            for(var key in storageStyleObject) {
                $(storageStyleObject[key]).attr('style', storageStyle[key] );
            }
        },
        resizeEvent: function ( ) {
            var $this = this;
            $this.resizeHandler();
            $(window).resize(function () {
                $this.resizeHandler();
            });
        },
        resizeHandler: function ( ) {
            if ($(window).width() < 992) {
                changed = true;
                this.handleOrderSmallScreen();
            } else {
                this.handleOrderBigScreen() ;
            }
        }
    };
    initHandleSpliter.init();

});




