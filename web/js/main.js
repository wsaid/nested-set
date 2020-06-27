$(document).ready(function(){ 

    $(document).on('click', '.addnode', function(){
        var name = $(this).attr('data-name');
        var dataString = 'Category[name]='+ name ;
        var parent = $(this);
            $.ajax({
                type: "POST",
                url: "save",
                data:  dataString,
                success  : function(response) {
                    var url = 'index';
                    $.pjax.reload({container: '#nested-set', async: true});
                }
            });
        });
});