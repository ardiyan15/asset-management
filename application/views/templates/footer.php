                <!-- Footer -->
                <!-- End of Footer -->

                </div>
                <!-- End of Content Wrapper -->

                </div>
                <!-- End of Page Wrapper -->

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

                <!-- Logout Modal-->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Logout?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Pilih logout apabila anda ingin keluar dari aplikasi.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Batal</button>
                                <a class="btn btn-primary btn-sm" href="<?= base_url('auth/logout'); ?>">Logout</a>
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
                </script>
                <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
                <script src="http://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
                <script src="<?= base_url('assets/') ?>js/sweetalert2.all.min.js"></script>
                <script src="<?= base_url('assets/') ?>js/ajax.js"></script>
                <script>
                    $(document).ready( function () {
                        $('#table-asset').DataTable({
                            "columnDefs": [
                                {"searchable": false, "targets": 0}
                            ],
                            "oLanguage": {
                                "sLengthMenu": "_MENU_",
                                "sSearch": "Cari",
                                "sInfo": "Jumlah data: <b> _TOTAL_ </b>",
                                "sInfoEmpty": "Tidak ada data"
                            },
                            "language": {
                                "paginate": {
                                    "previous": "Sebelumnya",
                                    "next": "Selanjutnya"
                                }
                            }
                        });
                    });
                </script>
                <script src="<?= base_url('assets/') ?>js/myscript1.js"></script>
            </body>
        </html>