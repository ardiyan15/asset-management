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

$('#room_bulk_transaction').change(function(){
    console.log($(this).val())
    let room_id = $(this).val()
$('#submit_bulk_takeout').click(function(){
    let asset = []
    $('input:checkbox[name="check"]:checked').each(function() {
        asset.push($(this).val())
        Swal.fire({
            title: 'Memindahkan Aset',
            text: "Anda ingin memindahkan aset yang dipilih?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(() => {
            $.ajax({
                method: 'POST',
                url: baseUrl+'transaction/bulk_transaction',
                // dataType: 'json',
                cache: false,
                data: {
                    'asset_id' : asset,
                    'room_id'  : room_id
                },
                success: function(results){
                    console.log(results)
                    if(results > 0){
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Berhasil memindahkan aset',
                            icon: 'success'
                        }).then(function(){
                            location.reload()
                        })
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal memindahkan aset',
                            icon: 'error'
                        })
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.log(XMLHttpRequest)
                    console.log('Error')
                }
            })
        }).catch(err => console.log(err))
    })
    })
})