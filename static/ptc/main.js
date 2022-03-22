(function ($) {
    $.fn.progressTimer = function (options) {
		var settings = $.extend({}, $.fn.progressTimer.defaults, options);

        this.each(function () {
            $(this).empty();
            var barContainer = $("<div>").addClass("progress active progress-striped");
            var bar = $("<div>").addClass("progress-bar progress-bar-striped progress-bar-animated").addClass(settings.baseStyle)
                .attr("role", "progressbar")
                .attr("aria-valuenow", "0")
                .attr("aria-valuemin", "0")
                .attr("aria-valuemax", settings.timeLimit);

            bar.appendTo(barContainer);
            barContainer.appendTo($(this));
            
            var start = new Date();
            var limit = settings.timeLimit * 1000;
            var interval = window.setInterval(function () {
				if(window_focus) {
					var elapsed = new Date() - start;
					bar.width(((elapsed / limit) * 100) + "%");

					if (limit - elapsed <= 3000)
						bar.removeClass(settings.baseStyle)
						   .removeClass(settings.completeStyle)
						   .addClass(settings.warningStyle);

					if (elapsed >= limit) {
						window.clearInterval(interval);

						bar.removeClass(settings.baseStyle)
						   .removeClass(settings.warningStyle)
						   .addClass(settings.completeStyle);

						settings.onFinish.call(this);
					}
				} else {
					start.setMilliseconds(start.getMilliseconds()+300);
				}
            }, 300);
        });

        return this;
    };

    $.fn.progressTimer.defaults = {
        timeLimit: 60,
        warningThreshold: 3,
        onFinish: function () {},
		baseStyle: 'bg-primary',
        warningStyle: 'bg-danger',
        completeStyle: 'bg-success'
    };
}(jQuery));

$(document).ready(function() {
    $(window).focus(function() {
        window_focus = true;
    });
    $(window).blur(function() {
        window_focus = false;
        setTimeout(function() {
            $(window).focus();
        }, 50);
    });
});

function showadbar(){
	$("#status").fadeOut(1000, function(){
		$("#progress").progressTimer({
			timeLimit: secs,
			onFinish: function () {endprogress()}
		});
	});
}

function endprogress(){
	 $("#progress").fadeOut(1000, function(){
		$("#status").html('<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-circle fa-fw"></i> '+captchaMsg+'</div>').fadeIn('fast');
		showCaptcha();
	});
}

function validateVisit(){
	var captchaID = $('input[name="captcha-idhf"]').val();
    var captchaIcon = $('input[name="captcha-hf"]').val();

	$("#status").html('<div class="alert alert-info" role="alert"><i class="fa fa-cog fa-spin fa-fw"></i> '+waitMsg+'</div>').fadeIn('fast');

	$.ajax({
		type: "POST",
		url: "system/ajax.php",
		data: "a=proccessPTC&data=" + sid + "&token=" + token + "&captcha-idhf=" + captchaID + "&captcha-hf=" + captchaIcon,
		dataType: "json",
		success: function(data) {
			if(data.status == '600') {
				showCaptcha();
				$("#status").html(data.message).fadeIn('slow');
			} else {
				$("#status").html(data.message).fadeIn('slow');
				window.setTimeout(function(){
					$('#validateVisit').modal('hide');
					if(data.redirect != 'false')
					{
						window.location.replace(data.redirect);
					}
				}, 1000);
			}
		}
	});
}

function showCaptcha() {
	$('#validateVisit').modal({
	  backdrop: 'static',
	  keyboard: false
	});
	
	$('.captcha-holder').CBCaptcha({
		clickDelay: 500,
		invalidResetDelay: 2500,
		requestIconsDelay: 1500,
		loadingAnimationDelay: 1500,
		hoverDetection: true,
		enableLoadingAnimation: true,
		validationPath: 'system/libs/captcha/request.php'
	}).bind('success.CBCaptcha', function(e, id) {
		validateVisit();
	});
}