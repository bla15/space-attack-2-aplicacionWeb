$(document).ready(function(){
    $('.btn-process').click(function (e) {
        e.preventDefault();
        
        var row = $(this).parents('tr');
        
        var id = row.data('id');
        
        var form = $('#form-update');
        
        var url = form.attr('action').replace(':FORO_ID', id);
        
        var data = form.serialize();
        
        $('#button-' + id).addClass('disabled');
        
        $.post(url, data, function(result){
            $('#button-' + id).removeClass('disabled');
            if(result.processed == 1)
            {
                $('#message-warning').addClass("hidden");
                
                $('#message').removeClass("hidden");
                
                $('#glyphicon-' + id).removeClass('glyphicon-time text-danger').addClass('glyphicon-ok text-success');
                $('#glyphicon-' + id).prop('title', 'Finish');
                
                 $('#user-message').html(result.success);
            }
            else
            {
                $('#message').addClass("hidden");
                $('#message-warning').removeClass("hidden");
                $('#user-message-warning').html('The topic was already finished.');
            }
        }).fail(function () {
            $('#button-' + id).removeClass('disabled');
             $('#user-message-warning').html(result.warning);
        });
    });
});