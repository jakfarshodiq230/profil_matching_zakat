      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?= $page?>
        </h1>
        <ol class="breadcrumb">
          <li>PM</li>
          <li><a href="<?= base_url('admin/hasil')?>"><?= $page ;?></a></li>
        </ol>
        <?php if(validation_errors()) : ?>
          <!-- Row Note -->
          <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
            <?= validation_errors(); ?>
          </div>
        <?php endif ;?>
        <?php if($this->session->flashdata('message') == TRUE) : ?>
          <!-- Row Note -->
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-info"></i> Note:</h4>
            <?= $this->session->flashdata('message'); ?>
          </div>
        <?php endif ;?>
        <?php if($this->session->flashdata('success') == TRUE) : ?>
          <!-- Row Note -->
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fa fa-check"></i> Note:</h5>
            <?= $this->session->flashdata('success'); ?>
          </div>
        <?php endif ;?> 
      </section>
      <!-- Main content -->
      <section class="content">

        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">List Calon Penerima Zakat Yang Sudah DiTerima</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body table-responsive">
          <a class="btn btn-xs btn-info" style="margin-bottom:20px;" href="<?= base_url('admin/exportPDF')?>" title="Detail"><i class="fa fa-print"></i> LAPORAN</a>
            <table id="example2" class="table table-bordered table-striped display nowrap " style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>NIK</th>
                  <th>Nama Penerima Zakat</th>
                  <th>Jenis Kelamin</th>
                  <th>Nilai Akhir</th>
                  <th>Rangking</th>
                  <th>Keterangan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=0; foreach ($allmasuk as $kh) :  $i++;?>
                <tr>
                  <th scope="row"><?= $i ;?></th>
                  <td><?= $kh->nik; ?></td>
                  <td><?= $kh->nama_penerima; ?></td>
                  <td><?= $kh->jenis_kelamin; ?></td>
                  <td><?= $kh->nilai_akhir; ?></td>
                  <td><?= $kh->my_rank; ?></td>
                  <td>
                    <?php if($kh->kandidat_terima == '1'){
                      echo 'Telah Diterima';
                    }?>
                  </td>
                  <td>
                    <a class="btn btn-xs btn-info" href="<?= base_url('admin/zakatTerimaDetail/'.$kh->id_penerima)?>" title="Detail"><i class="fa fa-info-circle"></i></a>
                    <a class="btn btn-xs btn-danger" href="#" data-toggle="modal" data-target="#deleteModalPekerja<?= $kh->id_penerima?>"  title="Delete"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->



      <!-- Barang Hapus Modal-->
      <?php $i=0; foreach($allmasuk as $row) :  $i++;?>
      <!-- Logout Modal-->
      <div class="modal fade" id="deleteModalPekerja<?= $row->id_penerima?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-red">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Apakah Anda Ingin Menghapus "<?= $row->nama_penerima?>"</h4>
              </div>
              <div class="modal-body">
                <p>Pilih "Hapus" dibawah untuk menghapus Data <b><?= $row->nama_penerima;?></b>.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left " data-dismiss="modal">Close</button>
                <a class="btn btn-danger " href="<?php echo base_url('admin/pekerjadelete/'.$this->encrypt->encode($row->id_penerima))?>">Delete</a>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
      <?php endforeach; ?>

    </section>
    <!-- /.content -->