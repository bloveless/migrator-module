$(function() {

    if($('#table-listing').length > 0) {

        $('#table-listing').change(function() {

            $.get('/admin/migrator/migrate_column/' + $(this).val(), function(data) {
                var data = $.parseJSON(data);

                $('#column-container').removeClass('hidden');
                $('#column-listing').empty().append($('<option></option>').attr('value', '').text('Select a column'));

                for(var i = 0; i < data.length; i++) {
                    var field = data[i];
                    $('#column-listing').append($('<option></option>').attr('value', field).text(field));
                }

                $('#column-listing').unbind('change').bind('change', function() {
                    var $this = $(this);

                    if($this.val()) {
                        console.log($this.val());
                        $('#submit-container').removeClass('hidden');
                        $('#submit-container input[type=submit]').attr('disabled', false);
                    } else {
                        $('#submit-container').addClass('hidden');
                        $('#submit-container input[type=submit]').attr('disabled', true);
                    }
                });
            })

        });

    }

});