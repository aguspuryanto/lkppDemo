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

	$dirName = "cache";
    $fileName = $dirName . "/list_produk_77.html";
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77";

    $data = file_get_contents($fileName);
    //now parsing it into html
	$html = str_get_html($data);

	echo $html->find("div.pageTitleWrap",0)->plaintext;
	$totHal = $html->find("div.contentNaviTop .control",0)->plaintext;
	preg_match_all('!\d+!', $totHal, $matches);
	echo "Total Halaman: " . trim($matches[0][0])."<br>";

	for ($i=10; $i < (trim($matches[0][0])/20); $i++) { 
		// echo "Page: ".$i.";";
		if($i>10){

			echo "Page: ".$i.";";
			$fileName = $dirName . "/llist_produk_77_".$i.".html";
    		$url .= "/?isSubmitted=1&page=".$i;

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
	$limit = 20;
	$page = ($page - 1) * $limit;

	$sth = $this->db->prepare("select * from ecatalog LIMIT ".$limit." OFFSET ".$page);
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
	foreach($html->find('table.tableInfo') as $html) {
		echo $html->outertext;
	}

	// echo $result;
});

$app->get('/lkpp[/{id}]', function ($request, $response, $args) {

	// echo var_dump($args['id']); die();
    /*
     * https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77
     */
    $dirName = "cache";
    $fileName = $dirName . "/list_produk_77.html";
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77";

    if($args['id']>1){
    	$fileName = $dirName . "/list_produk_77_".$args['id'].".html";
    	$url .= "/?isSubmitted=1&page=".$args['id'];
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

	    $data = file_get_contents($fileName);
	} else {

		$data = file_get_contents($fileName);
	}

    // return $data;

    //now parsing it into html
	$html = str_get_html($data);

	echo $html->find("div.pageTitleWrap",0)->plaintext;
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

	}
	// echo json_encode($listproduct);

	$sth = $this->db->prepare("select * from ecatalog");
    $sth->execute();
    $ecatalog = $sth->fetchAll();
    return $this->response->withJson($ecatalog);

});
 
$app->run();