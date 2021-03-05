$('#building').change(function() {
    let building_id = $(this).val()
    $.ajax({
        method: 'POST',
        url: baseUrl+'/room/list_rooms',
        dataType: 'json',
        cache: false,
        data: {"building_id": building_id},
        success: function(results){
            $.each(results, function(i, item) {
                $('#loc').append($('<options>', {
                    value: item.value,
                    text: item.text
                }))
            })
            console.log($('#loc'))
        },
        error: function(){
            console.log('Error')
        }
    })
})