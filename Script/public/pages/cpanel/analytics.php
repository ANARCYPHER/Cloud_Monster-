<?php

use CloudMonster\Helpers\Help;

?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">
                <?=$this->pageTitle?>
            </h2>
        </div>
        <!-- /.col -->
        <div class="col-auto ml-auto d-print-none">

            <?php if(!empty($fileInfo)): ?>

                <a href="javascript:void(0)" class="btn btn-warning ml-3 disabled">
                    ONE FILE
                </a>

            <?php else: ?>

                <a href="javascript:void(0)" class="btn btn-primary ml-3 disabled">
                    ALL FILES
                </a>

            <?php endif; ?>

        </div>
        <!-- /.col -->
    </div>
    <!-- /.row-->
</div>
<!--/.page-header-->

<div id="alert-wrap">
    <?php  $this->displayAlerts(); ?>
</div>
<!--/.alert-wrap-->

<?php if(!$fileNotFound): ?>

    <div class="box" >

        <?php if(!empty($fileInfo)): ?>

            <div class="card">
                <div class="card-body py-2">
                    <div class=" d-flex justify-content-between" id="selected-file" data-id="<?php _e('id',$fileInfo); ?>">

                        <div class="selected-drive-info d-flex lh-sm py-1 align-items-center">

                            <?php if(!empty($fileInfo['id'])): ?>

                                <span class="avatar mr-2" style="background-image: url( <?php imgUri('cdrives/'.$fileInfo['type'].'.png'); ?>  )"></span>
                                <div class="flex-fill">
                                    <div class="strong name"> <?php _e('name', $fileInfo); ?> </div>
                                    <div class="text-muted text-h5">
                                        <a href="javascript:void(0)" class="text-reset"> <?php _e('type', $fileInfo); ?> </a>
                                    </div>
                                </div>

                                <span class="ml-3 mb-3">

                                    <?php echo Help::formatDriveStatus($fileInfo['fstatus']); ?>

                                </span>

                            <?php else: ?>

                                <span>Not selected</span>

                            <?php endif; ?>

                        </div>
                        <!-- /.selected-drive-info -->

                        <div class="row" >
                            <label class="form-label col-3 col-form-label" style="width: 150px;">Results For : </label>
                            <div class="col">
                                <a href="<?php buildURIPath('cpanel/buckets/view/'.$fileInfo['bucketId']); ?>" class="text-primary font-weight-bold d-block cut-text">
                                    <?php _e('fileName', $fileInfo); ?>
                                </a>
                                <a href="<?php _e(\CloudMonster\Helpers\Help::getFileLink($fileInfo['slug'])); ?>"  target="_blank" class="text-gray">
                                    <small> <?php _e('code', $fileInfo); ?> </small>
                                </a>
                            </div>
                        </div>
                        <!-- /.row -->

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

        <?php endif; ?>


        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Visits summary</h3>
                    <div class="ml-auto">
                        <div class="input-icon">
                              <span class="input-icon-addon">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                 </svg>
                              </span>
                              <input type="text" class="form-control form-control-sm" id="visitsMonthlyDatePicker" name="" value="" />
                        </div>
                        <!-- /.input-icon -->
                    </div>
                    <!-- /.ml-auto -->
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div id="chart-visits-my-monthly"  class="chart-lg" ></div>
                </div>
                <!-- /.card-body -->

            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-12 -->

        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Visits Countries</h3>
                    <div class="ml-auto">
                        <div class="input-icon">
                              <span class="input-icon-addon">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                 </svg>
                              </span>
                              <input type="text" class="form-control form-control-sm" id="visitsByCountry" name="" value="" />
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="embed-responsive embed-responsive-16by9">
                        <div class="embed-responsive-item">
                            <div id="visitors-map" class="w-100 h-100"></div>
                        </div>
                    </div>
                    <div class="card-preloader">
                        <div class="card-preloader-inner">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-12 -->

        <?php if(!empty($top10Visits)): ?>

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Top Visits Countries : <small><i>all time</i></small></h3>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Country Name</th>
                                    <th class="text-center">Total Visits</th>
                                    <th class="text-center">Unique Visits</th>
                                    <th class="w-1"></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach ($top10Visits as $k => $val): ?>

                                    <tr>
                                        <td>
                                            #<?php echo $k + 1; ?>
                                        </td>
                                        <td class="">
                                            <?php _e('countryName', $val); ?>
                                        </td>
                                        <td class="text-muted text-center">
                                            <?php _e('totalVisits', $val); ?>
                                        </td>
                                        <td class="text-muted text-center">
                                            <?php _e('uniqVisits', $val); ?>
                                        </td>
                                        <td></td>
                                    </tr>

                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-body -->

                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-12 -->

        <?php endif; ?>

    </div>
<!-- /.box -->

<?php endif; ?>