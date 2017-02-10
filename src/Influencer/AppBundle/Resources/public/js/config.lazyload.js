/* ============================================================
 * File: config.lazyload.js
 * Configure modules for ocLazyLoader. These are grouped by 
 * vendor libraries. 
 * ============================================================ */

angular.module('app')
    .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
    	var root = (Routing.generate('inf_root')).replace('app_dev.php/', '');
        $ocLazyLoadProvider.config({
            debug: false,
            events: true,
            modules: [{
                    name: 'isotope',
                    files: [
                        root + 'assets/plugins/imagesloaded/imagesloaded.pkgd.min.js',
                        root + 'assets/plugins/jquery-isotope/isotope.pkgd.min.js'
                    ]
                }, {
                    name: 'codropsDialogFx',
                    files: [
                        root + 'assets/plugins/codrops-dialogFx/dialogFx.js',
                        root + 'assets/plugins/codrops-dialogFx/dialog.css',
                        root + 'assets/plugins/codrops-dialogFx/dialog-sandra.css'
                    ]
                }, {
                    name: 'metrojs',
                    files: [
                        root + 'assets/plugins/jquery-metrojs/MetroJs.min.js',
                        root + 'assets/plugins/jquery-metrojs/MetroJs.css'
                    ]
                }, {
                    name: 'owlCarousel',
                    files: [
                        root + 'assets/plugins/owl-carousel/owl.carousel.min.js',
                        root + 'assets/plugins/owl-carousel/assets/owl.carousel.css'
                    ]
                }, {
                    name: 'noUiSlider',
                    files: [
                        root + 'assets/plugins/jquery-nouislider/jquery.nouislider.min.js',
                        root + 'assets/plugins/jquery-nouislider/jquery.liblink.js',
                        root + 'assets/plugins/jquery-nouislider/jquery.nouislider.css'
                    ]
                }, {
                    name: 'nvd3',
                    files: [
                        root + 'assets/plugins/nvd3/lib/d3.v3.js',
                        root + 'assets/plugins/nvd3/nv.d3.min.js',
                        root + 'assets/plugins/nvd3/src/utils.js',
                        root + 'assets/plugins/nvd3/src/tooltip.js',
                        root + 'assets/plugins/nvd3/src/interactiveLayer.js',
                        root + 'assets/plugins/nvd3/src/models/axis.js',
                        root + 'assets/plugins/nvd3/src/models/line.js',
                        root + 'assets/plugins/nvd3/src/models/lineWithFocusChart.js',
                        root + 'assets/plugins/angular-nvd3/angular-nvd3.js',
                        root + 'assets/plugins/nvd3/nv.d3.min.css'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'rickshaw',
                    files: [
                        root + 'assets/plugins/nvd3/lib/d3.v3.js',
                        root + 'assets/plugins/rickshaw/rickshaw.min.js',
                        root + 'assets/plugins/angular-rickshaw/rickshaw.js',
                        root + 'assets/plugins/rickshaw/rickshaw.min.css',
                    ],
                    serie: true
                }, {
                    name: 'sparkline',
                    files: [
                    root + 'assets/plugins/jquery-sparkline/jquery.sparkline.min.js',
                    root + 'assets/plugins/angular-sparkline/angular-sparkline.js'
                    ]
                }, {
                    name: 'mapplic',
                    files: [
                        root + 'assets/plugins/mapplic/js/hammer.js',
                        root + 'assets/plugins/mapplic/js/jquery.mousewheel.js',
                        root + 'assets/plugins/mapplic/js/mapplic.js',
                        root + 'assets/plugins/mapplic/css/mapplic.css'
                    ]
                }, {
                    name: 'skycons',
                    files: [root + 'assets/plugins/skycons/skycons.js']
                }, {
                    name: 'switchery',
                    files: [
                        root + 'assets/plugins/switchery/js/switchery.min.js',
                        root + 'assets/plugins/ng-switchery/ng-switchery.js',
                        root + 'assets/plugins/switchery/css/switchery.min.css',
                    ]
                }, {
                    name: 'menuclipper',
                    files: [
                        root + 'assets/plugins/jquery-menuclipper/jquery.menuclipper.css',
                        root + 'assets/plugins/jquery-menuclipper/jquery.menuclipper.js'
                    ]
                }, {
                    name: 'wysihtml5',
                    files: [
                        root + 'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.min.css',
                        root + 'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js'
                    ]
                }, {
                    name: 'stepsForm',
                    files: [
                        root + 'assets/plugins/codrops-stepsform/css/component.css',
                        root + 'assets/plugins/codrops-stepsform/js/stepsForm.js'
                    ]
                }, {
                    name: 'jquery-ui',
                    files: [root + 'assets/plugins/jquery-ui-touch/jquery.ui.touch-punch.min.js']
                }, {
                    name: 'moment',
                    files: [root + 'assets/plugins/moment/moment.min.js',
                        root + 'assets/plugins/moment/moment-with-locales.min.js'
                    ]
                }, {
                    name: 'moment-locales',
                    files: [root + 'assets/plugins/moment/moment-with-locales.min.js'
                    ]
                }, {
                    name: 'hammer',
                    files: [root + 'assets/plugins/hammer.min.js']
                }, {
                    name: 'sieve',
                    files: [root + 'assets/plugins/jquery.sieve.min.js']
                }, {
                    name: 'line-icons',
                    files: [root + 'assets/plugins/simple-line-icons/simple-line-icons.css']
                }, {
                    name: 'ionRangeSlider',
                    files: [
                        root + 'assets/plugins/ion-slider/css/ion.rangeSlider.css',
                        root + 'assets/plugins/ion-slider/css/ion.rangeSlider.skinFlat.css',
                        root + 'assets/plugins/ion-slider/js/ion.rangeSlider.min.js'
                    ]
                }, {
                    name: 'navTree',
                    files: [
                        root + 'assets/plugins/angular-bootstrap-nav-tree/abn_tree_directive.js',
                        root + 'assets/plugins/angular-bootstrap-nav-tree/abn_tree.css'
                    ]
                }, {
                    name: 'nestable',
                    files: [
                        root + 'assets/plugins/jquery-nestable/jquery.nestable.css',
                        root + 'assets/plugins/jquery-nestable/jquery.nestable.js',
                        root + 'assets/plugins/angular-nestable/angular-nestable.js'
                    ]
                }, {
                    //https://github.com/angular-ui/ui-select
                    name: 'select',
                    files: [
                        root + 'assets/plugins/bootstrap-select2/select2.css',
                        root + 'assets/plugins/angular-ui-select/select.min.css',
                        root + 'assets/plugins/angular-ui-select/select.min.js'
                    ]
                }, {
                    name: 'datepicker',
                    files: [
                        root + 'assets/plugins/bootstrap-datepicker/css/datepicker3.css',
                        root + 'assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    ]
                }, {
                    name: 'daterangepicker',
                    files: [
                        root + 'assets/plugins/moment/moment.min.js',
                        root + 'assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
                        root + 'assets/plugins/bootstrap-daterangepicker/daterangepicker.js',
                        root + 'assets/plugins/angular-daterangepicker/angular-daterangepicker.min.js'
                    ]
                }, {
                    name: 'timepicker',
                    files: [
                        root + 'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css',
                        root + 'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js'
                    ]
                }, {
                    name: 'inputMask',
                    files: [
                        root + 'assets/plugins/jquery-inputmask/jquery.inputmask.min.js'
                    ]
                }, {
                    name: 'autonumeric',
                    files: [
                        root + 'assets/plugins/jquery-autonumeric/autoNumeric.js'
                    ]
                }, {
                    name: 'summernote',
                    files: [
                        root + 'assets/plugins/summernote/css/summernote.css',
                        root + 'assets/plugins/summernote/js/summernote.min.js',
                        root + 'assets/plugins/angular-summernote/angular-summernote.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'tagsInput',
                    files: [
                        root + 'assets/plugins/bootstrap-tag/bootstrap-tagsinput.css',
                        root + 'assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js'
                    ]
                }, {
                    name: 'dropzone',
                    files: [
                        root + 'assets/plugins/dropzone/css/dropzone.css',
                        root + 'assets/plugins/dropzone/dropzone.min.js',
                        root + 'assets/plugins/angular-dropzone/angular-dropzone.js'
                    ]
                }, {
                    name: 'wizard',
                    files: [
                        root + 'assets/plugins/lodash/lodash.min.js',
                        root + 'assets/plugins/angular-wizard/angular-wizard.min.css',
                        root + 'assets/plugins/angular-wizard/angular-wizard.min.js'
                    ]
                }, {
                    name: 'dataTables',
                    files: [
                        root + 'assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css',
                        root + 'assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css',
                        root + 'assets/plugins/datatables-responsive/css/datatables.responsive.css',
                        root + 'assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js',
                        root + 'assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js',
                        root + 'assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js',
                        root + 'assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js',
                        root + 'assets/plugins/datatables-responsive/js/datatables.responsive.js',
                        root + 'assets/plugins/datatables-responsive/js/lodash.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'google-map',
                    files: [
                        root + 'assets/plugins/angular-google-map-loader/google-map-loader.js',
                        root + 'assets/plugins/angular-google-map-loader/google-maps.js'
                    ]
                },  {
                    name: 'interact',
                    files: [
                        root + 'assets/plugins/interactjs/interact.min.js'
                    ]
                }, {
                    name: 'tabcollapse',
                    files: [
                        root + 'assets/plugins/bootstrap-collapse/bootstrap-tabcollapse.js'
                    ]
                }, {
                	name: 'ngImgCrop',
                	files: [
                		root + 'assets/plugins/ngImgCrop/compile/minified/ng-img-crop.css',
                		root + 'assets/plugins/ngImgCrop/compile/minified/ng-img-crop.js'
                	]
                }
            ]
        });
    }]);