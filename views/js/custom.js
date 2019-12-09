(function($){
    $(function () {

        $('form#wp-upseasytracking-form').on('submit', function (e) {

            e.preventDefault();

            $.ajax({
                type: 'post',
                url: ajax_object.wpups_ajaxurl,
                data:{
                    action: 'processWPUPSAjax', // this is the function in your functions.php that will be triggered
                    trackingnumber: $('#wp-upseasytracking-form-trackingnumber').val()
                  },
                beforeSend: function() { $('#wp-upseasytracking-response').html("Loading..."); },
                success: function (data) {
                    $('#wp-upseasytracking-response').html(data);
                }
            });

        });

    });
 })(jQuery.noConflict());

