<div class="row">
    <div class="col-xs-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <div class="pull-left">
                    <div class="box-title">
                        <h4><?php echo $judul ?></h4>
                    </div>
                </div>
                <div class="pull-right">
                    <div class="box-title">
                         <a href="<?php echo base_url('service/export_service') ?>" class="btn btn-success"><i class="fa fa-sign-in"></i> Export Excel</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" cellspacing="0" width="100%" id="table-service">
                        <thead>
                            <tr>
                                <th>Kode service</th>
                                <th>Tanggal Service</th>
                                <th>Tanggal Diambil</th>
                                <th>Karyawan</th>
                                <th>Status</th>
                                <th>Total Bayar</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>