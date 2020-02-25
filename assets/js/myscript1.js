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
            text: 'Operation Success',
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
    case 'take out':
        Swal.fire({
            title: 'Success',
            text: 'Operation Success',
            icon: 'success',
            showConfirmButton: true
        });
    case 'ti_success':
        Swal.fire({
            title: 'Success',
            text: 'Operation Success',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'reject':
        Swal.fire({
            title: 'Success',
            text: 'You Reject The Asset',
            icon: 'info',
            showConfirmButton: true
        });
        break;
    case 'success':
        Swal.fire({
            title: 'Success',
            text: 'Add Store Location Successfully',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'edtStr':
        Swal.fire({
            title: 'Success',
            text: 'Edit Store Location Successfully',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'deactStr':
        Swal.fire({
            title: 'Success',
            text: 'The Store has been deactivated',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'actvtStr':
        Swal.fire({
            title: 'Success',
            text: 'The Store has been activated',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'deleted':
        Swal.fire({
            title: 'Success',
            text: 'Delete asset successfully',
            icon: 'success',
            showConfirmButton: true
        });
        break;
    case 'failed':
        Swal.fire({
            title: 'Failed',
            text: 'Cannot input the same serial number',
            icon: 'error',
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
        text: "You will delete this asset",
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
        icon: 'question',
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

$('.takein-button').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Receive Asset',
        text: "Have you receive the asset?",
        icon: 'question',
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

$('.reject-button').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Reject Asset',
        text: "Have you receive the asset?",
        icon: 'question',
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

$('.deactStr-button').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Deactivate Store Location?',
        text: "This Store will not be able to receive or send the asset",
        icon: 'question',
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

$('.actvt-button').on('click', function (e) {
    e.preventDefault();

    const href = $(this).attr('href');

    Swal.fire({
        title: 'Activate Store Location?',
        text: "This Store will be able to receive or send the asset",
        icon: 'question',
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