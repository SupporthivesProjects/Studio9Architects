$(document).ready(function () {
    const images = [
        "url('./assets/img/newAssets/PROJECTS/VILE PARLE/1 (2).png')",
        "url('./assets/img/newAssets/PROJECTS/VILE PARLE/2 (2).png')",
        "url('./assets/img/newAssets/PROJECTS/VILE PARLE/3 (2).png')"
    ];

    let index = 0;
    const $slide = $('#hero_slide');
    const $controls = $('#slide_controls div');
    let interval;

    // Set initial image and active control
    $slide.css('background-image', images[index]);
    $controls.removeClass('active').eq(index).addClass('active');

    function startSlider() {
        interval = setInterval(() => {
            index = (index + 1) % images.length;

            $slide.fadeOut(500, function () {
                $slide.css('background-image', images[index]).fadeIn(500);
                $controls.removeClass('active').eq(index).addClass('active');
            });
        }, 5000);
    }

    function showImage(i) {
        clearInterval(interval); // Stop slider when manually selected
        index = i;
        $slide.fadeOut(500, function () {
            $slide.css('background-image', images[index]).fadeIn(500);
            $controls.removeClass('active').eq(index).addClass('active');
        });
        startSlider(); // Restart slider
    }

    // Start the auto-slider
    startSlider();

    // Handle span click
    $controls.on('click', function () {
        const clickedIndex = $(this).data('index');
        showImage(clickedIndex);
    });
});

/* Skill Bar */
	if ($('.skills-progress-bar').length) {
		$('.skills-progress-bar').waypoint(function() {
			$('.skillbar').each(function() {
				$(this).find('.count-bar').animate({
				width:$(this).attr('data-percent')
				},2000);
			});
		},{
			offset: '50%'
		});
	}
    