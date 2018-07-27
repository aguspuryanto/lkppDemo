<?php
// ini_set('display_errors', true);
// ini_set('display_startup_errors', true);
// ini_set('log_errors', true);
// ini_set('html_errors', 1);
error_reporting(E_ALL | E_STRICT); // with E_STRICT for PHP 5.3 compatibility

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
    return $response->withStatus(302)->withHeader('Location', 'http://localhost/lkppDemo/demolkpp/1');
});

$app->get('/lkppmulti[/]', function ($request, $response, $args) {

	// $dirName = "cache";
    $fileName = "list_produk_77_203186.html";
    // $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77/?isSubmitted=1&kategoriProdukId=&keyword=&penyediaId=203186&manufakturId=all&orderBy=hargaAsc&list=100";
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77/?isSubmitted=1&kategoriProdukId=&keyword=&penyediaId=all&manufakturId=all&orderBy=hargaDesc&list=100";

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data);
	    // $data = file_get_contents($fileName);
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
			$fileName = "list_produk_77_203186_".$i.".html";
    		$url .= "&page=".$i;

		    if(!file_exists($fileName)){

			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				$data = curl_exec($ch);
				curl_close($ch);

			    @file_put_contents($fileName, $data);
			    $data = file_get_contents($fileName);
			}

			sleep(8);
		}
	}

});

$app->get('/demolkpp[/{id}]', function ($request, $response, $args) {

	$allGetVars = $request->getQueryParams();
	if($allGetVars){
		// var_dump($allGetVars);
	}

	$cacheDir = realpath(dirname(__FILE__)) . "/cache/";
	// echo $cacheDir;

	$fileName = $cacheDir . "list_produk_77";
	if($allGetVars['penyediaId']){
		$result = $this->db->query("select distinct(penyediaUrl) from ecatalog where penyedia='".$allGetVars['penyediaId']."'");
		$fields = ($result->fetch(PDO::FETCH_ASSOC));
		// echo var_dump($fields['penyediaUrl']);
		$penyediaId = explode("/", $fields['penyediaUrl']);
		$penyediaId = end($penyediaId);

		$fileName .= "_".$penyediaId;
	}
    // echo $fileName;
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77";

    if($allGetVars['isSubmitted']){
    	$url .= "/?isSubmitted=".$allGetVars['isSubmitted'];
    }
    if($allGetVars['kategoriProdukId']){
    	$url .= "&kategoriProdukId=".$allGetVars['kategoriProdukId'];
    }
    if($allGetVars['keyword']){
    	$url .= "&keyword=".$allGetVars['keyword'];
    }
    if($allGetVars['penyediaId']){
    	$url .= "&penyediaId=".$penyediaId;
    }
    if($allGetVars['manufakturId']){
    	$url .= "&manufakturId=".$allGetVars['manufakturId'];
    }
    if($allGetVars['orderBy']){
    	$url .= "&orderBy=".$allGetVars['orderBy'];
    }
    if($allGetVars['list']){
    	$url .= "&list=".$allGetVars['list'];
    }

    // echo "id:".$args['id'];
    if($args['id']>1){
    	$fileName .= "_".$args['id'];
    	$url .= "&page=".$args['id'];
	}
    // echo $url;
    $fileName .= ".html";
    // echo "<br>".$fileName;
    $currUri = $request->getUri();
    // echo "uri:".$uri;

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data);
	    // $data = file_get_contents($fileName);
	} else {

		$data = @file_get_contents($fileName);
	}

	$totpage = 0;

    //now parsing it into html
	$html = str_get_html($data);
	if($html){
		// echo $html->find("div.pageTitleWrap",0)->plaintext;
		$totHal = $html->find("div.contentNaviTop .control",0)->plaintext;
		preg_match_all('!\d+!', $totHal, $matches);
		$totpage = ($matches[0][0]);
		// echo "Total Halaman: " . trim($matches[0][0])."<br>";

	}

	$page = isset($args['id']) ? $args['id'] : 1;
	$limit = 10;
	$offset = ($page - 1) * $limit;

	if($allGetVars['penyediaId']){
		$filter = "where penyedia='".$allGetVars['penyediaId']."'";
	}

	$sth = $this->db->prepare("select * from ecatalog ".$filter." LIMIT ".$limit." OFFSET ".$offset);
    $sth->execute();
    $ecatalog = $sth->fetchAll();

    $sth2 = $this->db->prepare("select distinct(penyedia), penyediaUrl from ecatalog order by penyedia asc");
    $sth2->execute();
    $penyedia = $sth2->fetchAll();

    $currPage = explode("/", $currUri);
    $idPage = end($currPage);
	// echo "<br>currPage:".$idPage;
    $idPage = explode("?", $idPage);
    // echo "<br>currPage:".$idPage[0];

    $prevPage = "";
    if($idPage[0]>1){
    	$prevPage = str_replace("/".$idPage[0]."?", "/".($idPage[0]-1)."?", $currUri);
    }
    $nextPage = str_replace("/".$idPage[0]."?", "/".($idPage[0]+1)."?", $currUri);
    
    return $this->view->render($response, 'lkpp.php', [
    	'ecatalog' => $ecatalog,
    	'penyedia' => $penyedia,
    	'page' => $page,
    	'totpage' => $totpage,
    	'params' => $allGetVars,
    	'prevPage' => $prevPage,
    	'nextPage' => $nextPage,
    	// 'currUri' => $currUri
    ]);

});

$app->get('/test', function ($request, $response, $args) {
	/*https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/500670
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/418761
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/412079
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/417113
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/417330
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/416395
	https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/412880*/

	$arr = array(500670,418761,412079,417113,417330,416395,412880);
	// var_dump($arr);
	foreach ($arr as $key) {

		$fileName = "lihat_produk_".$key.".html";
	    $url = "https://e-katalog.lkpp.go.id/backend/katalog/lihat_produk/".$key;
	    if(!file_exists($fileName)){

		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			$data = curl_exec($ch);
			curl_close($ch);
		    @file_put_contents($fileName, $data);
		}
		sleep(5);
	}
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
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data);
	    // $data = file_get_contents($fileName);
	} else {

		$data = file_get_contents($fileName);
	}
    // return $data;

    //now parsing it into html
	$html = str_get_html($data);
	if($html){

		$rowData = array();
		foreach($html->find('table.tableInfo') as $ti) {
			// echo $html->outertext;

			/*$td = array();
		    foreach($ti->find('td') as $cell) {
		        // push the cell's text to the array
		        $item[] = $cell->plaintext;
		    }*/

		    $komoditas = strtolower(trim($ti->find("tr td",0)->plaintext));
			$item[$komoditas] = $ti->find("tr td",1)->plaintext;
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
				$item['spec'] .= $ti->find("tr td",22)->plaintext.":".$ti->find("tr td",23)->plaintext;
			}

			if(isset($ti->find("tr td",25)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",24)->plaintext.":".$ti->find("tr td",25)->plaintext;
			}

			if(isset($ti->find("tr td",27)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",26)->plaintext.":".$ti->find("tr td",27)->plaintext;
			}

			if(isset($ti->find("tr td",29)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",28)->plaintext.":".$ti->find("tr td",29)->plaintext;
			}

			if(isset($ti->find("tr td",31)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",30)->plaintext.":".$ti->find("tr td",31)->plaintext;
			}

			if(isset($ti->find("tr td",33)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",32)->plaintext.":".$ti->find("tr td",33)->plaintext;
			}

			if(isset($ti->find("tr td",35)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",34)->plaintext.":".$ti->find("tr td",35)->plaintext;
			}

			if(isset($ti->find("tr td",37)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",36)->plaintext.":".$ti->find("tr td",37)->plaintext;
			}

			if(isset($ti->find("tr td",39)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",38)->plaintext.":".$ti->find("tr td",39)->plaintext;
			}

			if(isset($ti->find("tr td",41)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",40)->plaintext.":".$ti->find("tr td",41)->plaintext;
			}

			if(isset($ti->find("tr td",43)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",42)->plaintext.":".$ti->find("tr td",43)->plaintext;
			}

			if(isset($ti->find("tr td",45)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",44)->plaintext.":".$ti->find("tr td",45)->plaintext;
			}

			if(isset($ti->find("tr td",47)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",46)->plaintext.":".$ti->find("tr td",47)->plaintext;
			}

			if(isset($ti->find("tr td",49)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",48)->plaintext.":".$ti->find("tr td",49)->plaintext;
			}

			if(isset($ti->find("tr td",51)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",50)->plaintext.":".$ti->find("tr td",51)->plaintext;
			}

			if(isset($ti->find("tr td",53)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",52)->plaintext.":".$ti->find("tr td",53)->plaintext;
			}

			if(isset($ti->find("tr td",55)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",54)->plaintext.":".$ti->find("tr td",55)->plaintext;
			}

			if(isset($ti->find("tr td",57)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",56)->plaintext.":".$ti->find("tr td",57)->plaintext;
			}

			if(isset($ti->find("tr td",59)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",58)->plaintext.":".$ti->find("tr td",59)->plaintext;
			}

			if(isset($ti->find("tr td",61)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",60)->plaintext.":".$ti->find("tr td",61)->plaintext;
			}

			if(isset($ti->find("tr td",63)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",62)->plaintext.":".$ti->find("tr td",63)->plaintext;
			}

			if(isset($ti->find("tr td",65)->plaintext)){
				$item['spec'] .= "<br>".$ti->find("tr td",64)->plaintext.":".$ti->find("tr td",65)->plaintext;
			}

			$rowData[] = $item;
			
			// 16
			try {
				$statement = $this->db->prepare("INSERT IGNORE INTO `detailcatalog` (komoditas, merek, noProduk, namaProduk, unit, penyedia, penyediaUrl, noProdukPenyedia, jenisProduk, berlakuSampai, urlProduk, hargaProduk, spec) VALUES(:komoditas, :merek, :noProduk, :namaProduk, :unit, :penyedia, :penyediaUrl, :noProdukPenyedia, :jenisProduk, :berlakuSampai, :urlProduk, :hargaProduk, :spec) ON DUPLICATE KEY UPDATE spec=:spec");

				// echo var_dump($statement)."<br>";
			   	$statement->execute(array(':komoditas'=>$item['komoditas'], ':merek'=>$item['merek'], ':noProduk'=>$item['noProduk'], ':namaProduk'=>$item['namaProduk'], ':unit'=>$item['unit'], ':penyedia'=>$item['penyedia'], ':penyediaUrl'=>$item['penyediaUrl'], ':noProdukPenyedia'=>$item['noProdukPenyedia'], ':jenisProduk'=>$item['jenisProduk'], ':berlakuSampai'=>$item['berlakuSampai'], ':urlProduk'=>$item['urlProduk'], ':hargaProduk'=>trim($item['hargaProduk']), ':spec'=>$item['spec']));

			} catch(PDOException $e) {
			    echo $e->getMessage();
			}
		}
	}

	/*$i = 0;
	foreach ($rowData[0] as $value) {
		if($i%2==0) $key = $value;
		if($i%2!=0) {
			$value = $value;
			$newitem[strtolower($key)] = $value;
			$newrowData = $newitem;
		}
		$i++;
	}*/

	echo json_encode($rowData);
    // return $this->response->withJson($newrowData);
});

$app->get('/lkpp[/{penyediaId}/{id}]', function ($request, $response, $args) {

    // header("Content-type: application/vnd-ms-excel");
 	// header("Content-Disposition: attachment; filename=Ayooklik.xls");
    // return $this->response->withJson($args); die();

    /*
     * https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77
     */
    $cacheDir = realpath(dirname(__FILE__)) . "/cache/";
    $fileName = $cacheDir . "list_produk_77_".$args['penyediaId'].".html";
    // echo $fileName;
    $url = "https://e-katalog.lkpp.go.id/backend/katalog/list_produk/77/?isSubmitted=1&kategoriProdukId=&keyword=&penyediaId=".$args['penyediaId']."&manufakturId=all&orderBy=hargaDesc&list=100";

    if(isset($args['id'])>1){
    	$fileName = "list_produk_77_".$args['penyediaId']."_".$args['id'].".html";
    	$url .= "&page=".$args['id'];
	}
    // echo $url; die();

    if(!file_exists($fileName)){

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		$data = curl_exec($ch);
		curl_close($ch);

	    @file_put_contents($fileName, $data);
	    // $data = file_get_contents($fileName);
	} else {

		$data = @file_get_contents($fileName);
	}

    // return $data;

    //now parsing it into html
	$html = str_get_html($data);
	if($html){
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
	}
	// echo json_encode($listproduct);	
    return $this->response->withJson($listproduct); die();

	$page = isset($args['id']) ? $args['id'] : 1;
	$limit = 10;
	$page = ($page - 1) * $limit;

	// $sth = $this->db->prepare("select * from ecatalog a left join detailcatalog b on a.noProduk=b.noProduk where a.penyedia='Ayooklik.com' and b.noProduk!='' LIMIT ".$limit." OFFSET ".$page);

	// $sth = $this->db->prepare("select * from ecatalog a where a.penyedia='Ayooklik.com' LIMIT ".$limit." OFFSET ".$page);

	$sth = $this->db->prepare("select * from ecatalog a left join detailcatalog b on a.noProduk=b.noProduk and b.noProduk!='' ORDER BY a.id DESC LIMIT ".$limit." OFFSET ".$page);
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

    		/*echo '<th>Unit</th>
    		<th>No SKU</th>
    		<th>Url Produk</th>
    		<th>Spec</th>';*/

    	echo '</tr>';

    foreach ($ecatalog as $key) {

        $produkId = explode("/",$key['lihatProduk']);
        $produkId = end($produkId);

        /*$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://localhost/lkppDemo/lkpp/product/".$produkId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		$data = curl_exec($ch);
		curl_close($ch);*/

        echo "<tr>
            <td>".$key['infoProduk']."</td>
            <td>".$key['infoProduk1']."</td>
            <td>".$key['noProduk']."</td>";
            echo "<td><a href='lkpp/product/".$produkId."'>".$key['lihatProduk']."</a></td>";
            // echo "<td>".$key['lihatProduk']."</td>";
            echo "<td>".$key['imageProduk']."</td>
            <td>".$key['namaManufaktur']."</td>
            <td>".$key['namaProduk']."</td>
            <td>".$key['hargaProduk']."</td>
            <td>".$key['updatedDate']."</td>
            <td>".$key['updatedDate1']."</td>
            <td>".$key['jumlahStok']."</td>
            <td>".$key['penyedia']."</td>
            <td>".$key['penyedia1']."</td>";

            /*echo "<td>".$key['unit']."</td>
            <td>".$key['noProdukPenyedia']."</td>
            <td>".$key['urlProduk']."</td>
            <td>".$key['spec']."</td>";*/

        echo "</tr>";
    }
    echo '</table>';
    // return $this->response->withJson($ecatalog);

});
 
$app->run();