<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Starter Template for Bootstrap</title>
    <!-- Bootstrap core CSS -->
    <!-- <link href="<?=$templatesUrl;?>/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="<?=$templatesUrl;?>/starter-template.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <main role="main" class="container">

      <!-- <div class="starter-template">
        <h4>Katalog Produk Online Shop - Perangkat Komputer</h4>
      </div> -->
      <?php
      $curPage = 1;
      if($page) $curPage = $page;
      // echo "page: ".$page."<br>";
      // echo var_dump($params);
      // echo $currUri;

      $penyediaId = "";
      foreach ($penyedia as $value) { 

        if($params['penyediaId']==$value['penyedia']){
          $penyediaId = explode("/", $value['penyediaUrl']);
          $penyediaId = end($penyediaId);
        }

      }
      // echo "penyediaId:".$penyediaId;
      ?>

      <div class="row">
        
        <div class="col-md-3">
          <form method="get">
            <input type="hidden" name="isSubmitted" value="1">
            <input type="hidden" name="kategoriProdukId">
            <input type="hidden" name="keyword">
            <div class="form-group">
              <label>Penyedia</label>
              <select name="penyediaId" class="form-control">
                  <option value="">All Penyedia</option>
                  <?php foreach ($penyedia as $value) {
                    $penyediaId = explode("/", $value['penyediaUrl']);
                    $penyediaId = end($penyediaId);

                    echo '<option value="'.$value['penyedia'].'"';
                    if($params['penyediaId']==$value['penyedia']) echo 'selected';
                    echo '>'.$value['penyedia'].'</option>';
                  } ?>
              </select>
            </div>
            <input type="hidden" name="manufakturId" value="all">
            <input type="hidden" name="orderBy" value="hargaDesc">
            <input type="hidden" name="list" value="100">
            <button type="submit" class="btn btn-warning btn-block">Submit</button>
          </form>
        </div>

        <div class="col-md-9">
          <h4>Katalog Produk Online Shop - Perangkat Komputer</h4>

          <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%">
              <span class="sr-only">5% Complete</span>
            </div>
          </div>

          <nav aria-label="Page navigation" class="text-center">
            <ul class="pagination">
              <li>
                <a href="<?=$prevPage;?>" aria-label="Previous">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
              <li class="active"><a href="#"><?=$curPage;?> / <?=$totpage;?></a></li>
              <li>
                <a href="<?=$nextPage;?>" aria-label="Next">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>

          <table class="table">
            <tbody>
              <?php
              foreach ($ecatalog as $key) {

                $produkId = explode("/",$key['lihatProduk']);
                $produkId = end($produkId);

                echo "<tr>
                <td><img src='".$key['imageProduk']."' class='img-thumbnail'></td>
                <td>
                  <ul class='list-unstyled'>
                    <li>".$key['infoProduk']."</li>
                    <li>".$key['infoProduk1']."</li>
                  </ul>
                  <p><a href='".$baseUrl."/lkpp/product/".$produkId."'>".$key['noProduk']."</a></p>
                  <b>".$key['namaProduk']."</b>
                </td>
                <td>
                  <span class='d-block'>Harga : IDR ".number_format($key['hargaProduk'],2)."</span>
                  <span class='d-block'>Tanggal Tayang :".$key['updatedDate']."</span>
                  <span class='d-block'>Update Harga :".$key['updatedDate1']."</span>
                  <span class='d-block'>Jumlah Stok :".$key['jumlahStok']."</span>
                  <span class='d-block'>Penyedia : <a href='".$key['penyediaUrl']."'>".$key['penyedia']."</a></span>
                  <span class='d-block'>Berlaku Sampai Dengan :".$key['penyedia1']."</span>
                </td>
              </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- JavaScript Libraries -->
    <script src="<?=$templatesUrl;?>/lib/jquery/jquery.min.js"></script>
    <script src="<?=$templatesUrl;?>/lib/jquery/jquery-migrate.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
      (function($){

        /*setInterval(function(){ 
            $.ajax({
              type:"get",
              url:"<?=$baseUrl;?>/test",
              async: false,
              complete:function(data){
                console.log("data:" + data);
              }
            });
        }, 10000); */

        var detilPage = "<?=$baseUrl;?>/lkpp/<?=$penyediaId;?>/<?=$curPage;?>";
        $.ajax({
          type:"get",
          url:detilPage,
          async: false,
          success:function(resp){
            // 
          },
          complete:function(data){
            // console.log("detilPage:" + detilPage);
            $('.progress .progress-bar').css('width', "100%").attr('aria-valuenow', '100');
            $('.progress').delay(100).fadeOut("slow");

            console.log("data:" + JSON.stringify(data));
          }
        });

      })(jQuery);
    </script>
  </body>
</html>