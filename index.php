<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
 * Readme
 * https://arjunphp.com/creating-restful-api-slim-framework/
 * https://gist.github.com/odan/d2b889c350aa2ea0ff8e5ca93ce588a2
 */

require 'vendor/autoload.php';
require ('settings.php');
require ('simple_html_dom.php');

// $app = new \Slim\App($config);
// $app = new Slim\App();
// var_dump($config);

$app = new \Slim\App($config);

include_once ('dependencies.php');
 
$app->get('/', function ($request, $response, $args) use($app) {
    // $response->write("Hello World");
    // return $response;
    // return $this->view->render($response, 'index.php');
    // return $response->withRedirect('http://localhost/demolkpp/lkpp'); 
    return $response->withStatus(302)->withHeader('Location', 'http://localhost/lkppDemo/demolkpp');
});

$app->get('/lkppmulti[/]', function ($request, $response, $args) {

	// $dirName = "cache";
    $fileName = "list_produk_77_269615.html";
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77/?isSubmitted=1&kategoriProdukId=&keyword=&penyediaId=269615&manufakturId=all&orderBy=hargaAsc&list=100";

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data, FILE_APPEND);

	    $data = file_get_contents($fileName);
	} else {

		$data = file_get_contents($fileName);
	}
    //now parsing it into html
	$html = str_get_html($data);

	echo $html->find("div.pageTitleWrap",0)->plaintext;
	$totHal = $html->find("div.contentNaviTop .control",0)->plaintext;
	preg_match_all('!\d+!', $totHal, $matches);
	echo "Total Halaman: " . trim($matches[0][0])."<br>";

	for ($i=0; $i < (trim($matches[0][0])/20); $i++) { 
		// echo "Page: ".$i.";";
		if($i>=1){

			echo "Page: ".$i.";";
			$fileName = "list_produk_77_269615_".$i.".html";
    		$url .= "&page=".$i;

		    if(!file_exists($fileName)){

			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
				$data = curl_exec($ch);
				curl_close($ch);

			    @file_put_contents($fileName, $data, FILE_APPEND);

			    $data = file_get_contents($fileName);
			}

			sleep(8);
		}
	}

});

$app->get('/demolkpp[/{id}]', function ($request, $response, $args) {

	$page = isset($args['id']) ? $args['id'] : 1;
	$limit = 10;
	$page = ($page - 1) * $limit;

	$sth = $this->db->prepare("select * from ecatalog where penyedia='Bhinneka.com' LIMIT ".$limit." OFFSET ".$page);
    $sth->execute();
    $ecatalog = $sth->fetchAll();
    
    return $this->view->render($response, 'lkpp.php', ['ecatalog' => $ecatalog]);

});

$app->get('/lkpp/product[/{id}]', function ($request, $response, $args) {
	// echo "https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/".$args['id'];
	// $dirName = dirname(__FILE__) . "/cache";

	$fileName = "lihat_produk_".$args['id'].".html";
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/".$args['id'];

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data, FILE_APPEND);

	    $data = file_get_contents($fileName);
	} else {

		$data = file_get_contents($fileName);
	}

    // return $data;

    //now parsing it into html
	$html = str_get_html($data);
	foreach($html->find('table.tableInfo') as $ti) {
		// echo $html->outertext;
		$item['komoditas'] = $ti->find("tr td",1)->plaintext;
		$item['merek'] = $ti->find("tr td",3)->plaintext;
		$item['noProduk'] = $ti->find("tr td",5)->plaintext;
		$item['namaProduk'] = $ti->find("tr td",7)->plaintext;
		$item['unit'] = $ti->find("tr td",9)->plaintext;
		$item['penyedia'] = $ti->find("tr td",11)->plaintext;
		$item['penyediaUrl'] = $ti->find("tr td a",0)->href;
		$item['noProdukPenyedia'] = $ti->find("tr td",13)->plaintext;
		$item['jenisProduk'] = $ti->find("tr td",15)->plaintext;
		$item['berlakuSampai'] = $ti->find("tr td",17)->plaintext;
		$item['urlProduk'] = $ti->find("tr td",19)->plaintext;
		$item['hargaProduk'] = $ti->find("tr td",21)->plaintext;
		$item['spec'] = "";

		if(isset($ti->find("tr td",23)->plaintext)){
			$item['spec'] = $ti->find("tr td",23)->plaintext;
		}
		
		// 16
		try {
			$statement = $this->db->prepare("INSERT IGNORE INTO `detailcatalog` (komoditas, merek, noProduk, namaProduk, unit, penyedia, penyediaUrl, noProdukPenyedia, jenisProduk, berlakuSampai, urlProduk, hargaProduk, spec) VALUES(:komoditas, :merek, :noProduk, :namaProduk, :unit, :penyedia, :penyediaUrl, :noProdukPenyedia, :jenisProduk, :berlakuSampai, :urlProduk, :hargaProduk, :spec)");

			// echo var_dump($statement)."<br>";
		   	$statement->execute(array(':komoditas'=>$item['komoditas'], ':merek'=>$item['merek'], ':noProduk'=>$item['noProduk'], ':namaProduk'=>$item['namaProduk'], ':unit'=>$item['unit'], ':penyedia'=>$item['penyedia'], ':penyediaUrl'=>$item['penyediaUrl'], ':noProdukPenyedia'=>$item['noProdukPenyedia'], ':jenisProduk'=>$item['jenisProduk'], ':berlakuSampai'=>$item['berlakuSampai'], ':urlProduk'=>$item['urlProduk'], ':hargaProduk'=>trim($item['hargaProduk']), ':spec'=>$item['spec']));

		} catch(PDOException $e) {
		    echo $e->getMessage();
		}

		$listproduct[] = $item;
	}

	echo json_encode($listproduct);
});

$app->get('/lkpp[/{id}]', function ($request, $response, $args) {

    header("Content-type: application/vnd-ms-excel");
 	header("Content-Disposition: attachment; filename=Ayooklik.xls");
    
    /*
     * https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77
     */
    // $dirName = "cache";
    $fileName = "list_produk_77_269615.html";
    // echo $fileName;
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77/?isSubmitted=1&kategoriProdukId=&keyword=&penyediaId=269615&manufakturId=all&orderBy=hargaAsc&list=100";

    if(isset($args['id'])>1){
    	$fileName = "list_produk_77_269615_".$args['id'].".html";
    	$url .= "&page=".$args['id'];
	}
    // echo $url;

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data, FILE_APPEND);
	    // $data = file_get_contents($fileName);
	} else {

		$data = @file_get_contents($fileName);
	}

    // return $data;

    //now parsing it into html
	$html = str_get_html($data);
	if($html){
		/*echo $html->find("div.pageTitleWrap",0)->plaintext;
		$totHal = $html->find("div.contentNaviTop .control",0)->plaintext;
		preg_match_all('!\d+!', $totHal, $matches);
		echo "Total Halaman: " . trim($matches[0][0])."<br>";

		foreach($html->find('div.contentCore table tr') as $tr) {
			// echo var_dump($tr->find("td"));
			//get all <td> where class is NOT "debug"
			foreach($tr->find('td div.itemWrapper') as $t) {
				//get the inner HTML
				$item['jenisProduk'] = "Import";
				$item['imageProduk'] = $t->find("div.imageProduk img",0)->src;
				$item['infoProduk'] = $t->find("div.infoProduk ol li",0)->plaintext;
				$item['infoProduk1'] = $t->find("div.infoProduk ol li",1)->plaintext;
				$item['lihatProduk'] = $t->find("div.noProduk a",0)->href;
				$item['noProduk'] = $t->find("div.noProduk a",0)->plaintext;
				$item['namaManufaktur'] = $t->find("div.infoProduk div.namaProdukWrapper span.namaManufaktur",0)->plaintext;
				$item['namaProduk'] = $t->find("div.infoProduk div.namaProdukWrapper span.namaProduk",0)->plaintext;
				$item['hargaProduk'] = $t->find("div.hargaUtama",0)->plaintext;
				$item['updatedDate'] = $t->find("div.updatedDate",0)->plaintext;
				$item['updatedDate1'] = $t->find("div.updatedDate",1)->plaintext;
				$item['jumlahStok'] = trim($t->find("div.jumlahStok",0)->plaintext);
				$item['penyediaUrl'] = $t->find("div.penyedia a",0)->href;
				$item['penyedia'] = $t->find("div.penyedia",0)->plaintext;
				$item['penyedia1'] = trim($t->find("div.penyedia",1)->plaintext);

				$hargaProduk = trim($item['hargaProduk']);
				preg_match_all('!\d+!', $hargaProduk, $newhargaProduk);
				$hargaProduk = $newhargaProduk[0][0];
				
				$tglTayang = str_replace("Tanggal Tayang                                                : ","",trim($item['updatedDate']));

				$tglUpdate = str_replace("Update Harga                                                : ","",trim($item['updatedDate1']));

				$jumlahStok = str_replace("Jumlah Stok                                    : ","",trim($item['jumlahStok']));
				$penyedia = str_replace("Penyedia: ","",$item['penyedia']);

				$validTrue = str_replace("Berlaku Sampai Dengan                                        : ","",trim($item['penyedia1']));

				// 16
				try {
					$statement = $this->db->prepare("INSERT IGNORE INTO `ecatalog` (pid, jenisProduk, imageProduk, infoProduk, infoProduk1, noProduk, lihatProduk, namaManufaktur, namaProduk, hargaProduk, updatedDate, updatedDate1, jumlahStok, penyediaUrl, penyedia, penyedia1) VALUES(:pid, :jenisProduk, :imageProduk, :infoProduk, :infoProduk1, :noProduk, :lihatProduk, :namaManufaktur, :namaProduk, :hargaProduk, :updatedDate, :updatedDate1, :jumlahStok, :penyediaUrl, :penyedia, :penyedia1) ON DUPLICATE KEY UPDATE lihatProduk=:lihatProduk");

					// echo var_dump($statement)."<br>";
			    	$statement->execute(array(':pid'=>'77', ':jenisProduk'=>$item['jenisProduk'], ':imageProduk'=>$item['imageProduk'], ':infoProduk'=>$item['infoProduk'], ':infoProduk1'=>$item['infoProduk1'], ':noProduk'=>$item['noProduk'], ':lihatProduk'=>$item['lihatProduk'], ':namaManufaktur'=>$item['namaManufaktur'], ':namaProduk'=>$item['namaProduk'], ':hargaProduk'=>$hargaProduk, ':updatedDate'=>$tglTayang, ':updatedDate1'=>$tglUpdate, ':jumlahStok'=>$jumlahStok, ':penyediaUrl'=>$item['penyediaUrl'], ':penyedia'=>trim($penyedia), ':penyedia1'=>$validTrue));

			    } catch(PDOException $e) {
			        echo $e->getMessage();
			    }

				$listproduct[] = $item;
			}

		}*/
	}
	// echo json_encode($listproduct);

	$page = isset($args['id']) ? $args['id'] : 1;
	$limit = 10;
	$page = ($page - 1) * $limit;

	$sth = $this->db->prepare("select * from ecatalog a left join detailcatalog b on a.noProduk=b.noProduk where a.penyedia='Ayooklik.com' and b.noProduk!='' LIMIT ".$limit." OFFSET ".$page);

	// $sth = $this->db->prepare("select * from ecatalog a where a.penyedia='Ayooklik.com' LIMIT ".$limit." OFFSET ".$page);
    $sth->execute();
    $ecatalog = $sth->fetchAll();

    echo '<table class="table">
    	<thead>
    	<tr>
    		<th>infoProduk</th>
    		<th>infoProduk1</th>
    		<th>noProduk</th>
    		<th>lihatProduk</th>
    		<th>imageProduk</th>
    		<th>namaManufaktur</th>
    		<th>namaProduk</th>
    		<th>hargaProduk</th>
    		<th>tglTayang</th>
    		<th>tglupdatedHarga</th>
    		<th>jumlahStok</th>
    		<th>penyedia</th>
    		<th>akhirTayang</th>
    		<th>Komoditas</th>';
    		echo '<th>Unit</th>
    		<th>No SKU</th>
    		<th>Url Produk</th>
    		<th>Spec</th>';    		
    	echo '</tr>';

    foreach ($ecatalog as $key) {

        $produkId = explode("/",$key['lihatProduk']);
        $produkId = end($produkId);

        echo "<tr>
            <td>".$key['infoProduk']."</td>
            <td>".$key['infoProduk1']."</td>
            <td>".$key['noProduk']."</td>";
            // echo "<td><a href='lkpp/product/".$produkId."'>".$key['lihatProduk']."</a></td>";
            echo "<td>".$key['lihatProduk']."</td>";
            echo "<td>".$key['imageProduk']."</td>
            <td>".$key['namaManufaktur']."</td>
            <td>".$key['namaProduk']."</td>
            <td>".$key['hargaProduk']."</td>
            <td>".$key['updatedDate']."</td>
            <td>".$key['updatedDate1']."</td>
            <td>".$key['jumlahStok']."</td>
            <td>".$key['penyedia']."</td>
            <td>".$key['penyedia1']."</td>";

            echo "<td>".$key['unit']."</td>
            <td>".$key['noProdukPenyedia']."</td>
            <td>".$key['urlProduk']."</td>
            <td>".$key['spec']."</td>";

        echo "</tr>";
    }
    echo '</table>';
    // return $this->response->withJson($ecatalog);

});
 
$app->run();