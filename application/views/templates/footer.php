</div>
</div>
<a class="scroll-to-top" href="#page-top" aria-label="Kembali ke atas">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Keluar dari SIMANIS?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Sesi Anda akan diakhiri di perangkat ini. Data yang belum disimpan akan hilang.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-dark btn-sm" href="<?= base_url('auth/logout'); ?>">Keluar</a>
            </div>
        </div>
    </div>
</div>
<!-- Initiate baseUrl for ajax call -->
<script>
    let baseUrl = "<?= base_url(); ?>"
</script>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('assets/'); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>
<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Kerangka SIMANIS: laci menu mobile, mode rel desktop, pintasan "/" ke pencarian
    (function($) {
        var $side = $('#accordionSidebar');
        var isDesktop = function() { return window.innerWidth >= 768; };

        $('#sidebarBackdrop').on('click', function() {
            $('body').removeClass('sidebar-toggled');
            $side.removeClass('toggled');
        });

        $('#sidebarToggle').on('click', function() {
            if (!isDesktop()) return;
            try { localStorage.setItem('simanis.sidebar', $side.hasClass('toggled') ? 'rail' : 'full'); } catch (e) {}
        });

        $('#sidebarToggleTop').on('click', function() {
            $(this).attr('aria-expanded', $side.hasClass('toggled') ? 'true' : 'false');
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !isDesktop() && $side.hasClass('toggled')) {
                $('#sidebarBackdrop').trigger('click');
            }
            var tag = (e.target.tagName || '').toLowerCase();
            if (e.key === '/' && tag !== 'input' && tag !== 'textarea' && tag !== 'select' && !e.target.isContentEditable) {
                var box = document.getElementById('topbarSearch');
                if (box && box.offsetParent !== null) {
                    e.preventDefault();
                    box.focus();
                }
            }
        });
    })(jQuery);
</script>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    // Teks DataTables dalam Bahasa Indonesia untuk semua tabel
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            search: '',
            searchPlaceholder: 'Cari di tabel…',
            lengthMenu: 'Tampilkan _MENU_',
            info: '_START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(disaring dari _MAX_)',
            zeroRecords: 'Tidak ada data yang cocok',
            emptyTable: 'Belum ada data',
            processing: 'Memuat data…',
            loadingRecords: 'Memuat…',
            paginate: { first: 'Awal', last: 'Akhir', next: '›', previous: '‹' }
        }
    });
</script>
<script src="<?= base_url('assets/') ?>js/sweetalert2.all.min.js"></script>
<script src="<?= base_url('assets/') ?>js/ajax.js"></script>
<script src="<?= base_url('assets/') ?>js/myscript1.js?v=<?= time(); ?>"></script>
<script>
    $(document).ready(function() {
        $('#table-asset').DataTable();
    });
</script>
</body>

</html>
