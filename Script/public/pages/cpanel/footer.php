</div>
<footer class="footer footer-transparent">
    <div class="container">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ml-lg-auto">
                Concept and Developed By <a href="#" class="link-secondary">John Antonio</a>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                Copyright © 2021
                <a href="./" class="link-secondary">CloudMonster</a>.
                All rights reserved.
            </div>
        </div>

    </div>
</footer>



</div>
</div>




<script>
    document.body.style.display = "block"
</script>

<script>

    const ROOT = '<?php _e(siteurl()) ?>';


</script>



<!-- Libs JS -->
<script src="<?php buildResourceURI('assets/cpanel/libs/jquery/dist/jquery.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<script src="<?php buildResourceURI('assets/cpanel/libs/selectize/dist/js/standalone/selectize.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/apexcharts/dist/apexcharts.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/jqvmap/dist/jquery.vmap.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/jqvmap/dist/maps/jquery.vmap.world.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/daterangepicker/dist/daterangepicker.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/libs/dropzone/dist/js/dropzone.min.js'); ?>"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>


<script src="<?php buildResourceURI('assets/cpanel/js/theme.min.js'); ?>"></script>
<script src="<?php buildResourceURI('assets/cpanel/js/custom.js?v.1.1'); ?>"></script>

<?php echo $this->jsHtml; ?>

<script src="<?php buildResourceURI('assets/cpanel/js/app.min.js?v.1.1'); ?>"></script>


<script>
    $(document).ready(function () {
        $('#select-drives-list').selectize({
            render: {
                option: function (data, escape) {
                    return '<div class="option">' + data.avatar + '' + escape(data.text) + '</div>';
                },
                item: function (data, escape) {
                    return '<div class="d-flex align-items-center">' + data.avatar + '' + escape(data.text) + '</div>';
                }
            }
        });
    });

</script>


</body>
</html>