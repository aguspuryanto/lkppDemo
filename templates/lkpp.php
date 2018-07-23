<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Starter Template for Bootstrap</title>
    <!-- Bootstrap core CSS -->
    <link href="<?=$templatesUrl;?>/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=$templatesUrl;?>/starter-template.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

    <main role="main" class="container">

      <!-- <div class="starter-template">
        <h4>Katalog Produk Online Shop - Perangkat Komputer</h4>
      </div> -->

      <div class="row">
        <h4>Katalog Produk Online Shop - Perangkat Komputer</h4>
        <table class="table">
          <!-- <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">First</th>
              <th scope="col">Last</th>
              <th scope="col">Handle</th>
            </tr>
          </thead> -->
          <tbody>
            <?php
            foreach ($ecatalog as $key) {

              $produkId = explode("/",$key['lihatProduk']);
              $produkId = end($produkId);

              echo "<tr>
              <th scope='row'>".$key['jenisProduk']."</th>
              <td><img src='".$key['imageProduk']."' class='img-thumbnail'></td>
              <td>
                <ul class='list-unstyled'>
                  <li>".$key['infoProduk']."</li>
                  <li>".$key['infoProduk1']."</li>
                </ul>
                <p><a href='".$baseUrl."/lkpp/product/".$produkId."'>".$key['noProduk']."</a></p>
                <h4>".$key['namaProduk']."</h4>
              </td>
              <td>
                <p>Harga : IDR ".number_format($key['hargaProduk'],2)."</p>
                <p>Tanggal Tayang :".$key['updatedDate']."</p>
                <p>Update Harga :".$key['updatedDate1']."</p>
                <p>Jumlah Stok :".$key['jumlahStok']."</p>
                <p>Penyedia : <a href='".$key['penyediaUrl']."'>".$key['penyedia']."</a></p>
                <p>Berlaku Sampai Dengan :".$key['penyedia1']."</p>
              </td>
            </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- JavaScript Libraries -->
    <script src="<?=$templatesUrl;?>/lib/jquery/jquery.min.js"></script>
    <script src="<?=$templatesUrl;?>/lib/jquery/jquery-migrate.min.js"></script>
    <script src="<?=$templatesUrl;?>/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
  </body>
</html>