const flashData = $('.flash-data').data('flashdata');

switch (flashData) {
    case 'added ':
        Swal.fire({
            title: 'Success',
            text: 'Add asset successfully',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'edited ':
        Swal.fire({
            title: 'Success',
            text: 'User has been activated',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'failed ':
        Swal.fire({
            title: 'Failed',
            text: 'Operation failed',
            icon: 'error',
            showConfirmButton: true
        });
        break;
    case 'activate':
        Swal.fire({
            title: 'Success',
            text: 'User has been activated',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'deactivate':
        Swal.fire({
            title: 'Success',
            text: 'User has been deactivated',
            icon: 'success',
            showConfirmButton: true
        });
        break;
}

// Pop Up Delete
$('.delete-button').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            document.location.href = href;
        }
    })
});

$('.activate-user').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Activate this user?',
        text: "This user will be able to login",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.value) {
            document.location.href = href;
        }
    })
});

$('.deactivate-user').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Deactivate this user?',
        text: "This user will not be able to login",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.value) {
            document.location.href = href;
        }
    })
});