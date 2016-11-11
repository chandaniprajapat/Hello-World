Khoob<?php

//amazon api

define('AWS_ACCESS_KEY_ID', '');
define('AWS_SECRET_ACCESS_KEY', '');
define('AMAZON_ASSOC_TAG', '');

function amazon_get_signed_url($searchTerm) {
$base_url = "http://ecs.amazonaws.com/onca/xml";
$params = array(
'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
'AssociateTag' => AMAZON_ASSOC_TAG,
'Version' => "2016-11-01",
'Operation' => "ItemSearch",
'Service' => "AWSECommerceService",
'ResponseGroup' => "ItemAttributes,Images",
'Availability' => "Available",
'Condition' => "All",
'Operation' => "ItemSearch",
'SearchIndex' => 'All', //Change search index if required, you can also accept it as a parameter for the current method like $searchTerm
'Keywords' => $searchTerm);

//'ItemPage'=>"1",
//'ResponseGroup'=>"Images,ItemAttributes,EditorialReview",

if(empty($params['AssociateTag'])) {
unset($params['AssociateTag']);
}

// Add the Timestamp
$params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

// Sort the URL parameters
$url_parts = array();
foreach(array_keys($params) as $key)
$url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
sort($url_parts);

// Construct the string to sign
$url_string = implode("&", $url_parts);
$string_to_sign = "GET\necs.amazonaws.com\n/onca/xml\n" . $url_string;

// Sign the request
$signature = hash_hmac("sha256", $string_to_sign, AWS_SECRET_ACCESS_KEY, TRUE);

// Base64 encode the signature and make it URL safe
$signature = urlencode(base64_encode($signature));

$url = $base_url . '?' . $url_string . "&Signature=" . $signature;

return ($url);
}


?>

<?php

 

$getthis = 'jeans';
$show = amazon_get_signed_url('$getthis');
//print_r($show);

$ch = curl_init($show);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$c = curl_exec($ch);

$xml = simplexml_load_string($c);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
//print_r($array);
$checkamazon = $array['Items']['Item'][0]['DetailPageURL'];

if($checkamazon != ""){
echo "<br><br><font size='3' color='#36355' style='padding: 3px;'><b>Products</b></font><br>";
}

for($i=0;$i<10;$i++){
if(!empty($array['Items']['Item'][$i]['ItemAttributes']['ListPrice']['FormattedPrice']))
{
$aprice = $array['Items']['Item'][$i]['ItemAttributes']['ListPrice']['FormattedPrice'];
}
else
{
    $aprice =0;
}
$aDescription = $array['Items']['Item'][$i]['ItemAttributes']['Title'];
$aUrl = $array['Items']['Item'][$i]['DetailPageURL'];
$aImage = $array['Items']['Item'][$i]['SmallImage']['URL'];
if(!empty($array['Items']['Item'][$i]['ItemAttributes']['Feature'][0]))
    {
$afeature1 = $array['Items']['Item'][$i]['ItemAttributes']['Feature'][0];
    }
    else
    {
        $afeature1 = '';
    }
    if(!empty($array['Items']['Item'][$i]['ItemAttributes']['Feature'][1]))
    {
$afeature2 = $array['Items']['Item'][$i]['ItemAttributes']['Feature'][1];
    }
    else
    {
        $afeature2 = '';
    }
    if(!empty($array['Items']['Item'][$i]['ItemAttributes']['Feature'][2]))
    {
$afeature3 = $array['Items']['Item'][$i]['ItemAttributes']['Feature'][2];
    }
    else
    {
        $afeature3 = '';
    }
//$afeature2 = $array['Items']['Item'][$i]['ItemAttributes']['Feature'][1];
//$afeature3 = $array['Items']['Item'][$i]['ItemAttributes']['Feature'][2];

 $web_rank = '';

if($aUrl != ""){

echo "<div id=\".$web_rank.\" style='text-align: left; display: block; float: left; width: 100%; padding: 10px; border-bottom: SOLID 1px #C4C4C4;'>";
echo "<div id='leftcolumn' style='width: 0px; float: left; display:inline; padding: 5px;'>";

echo "</div>";
echo "<div id='rightcolumn' style='width: 600px; float: left; padding: 0px;'>";
echo "<a href = \"$aUrl\" target='_blank' STYLE=\"TEXT-DECORATION: NONE;\">";
echo "<img src='$aImage' style='height: 50px;' >";
echo "<font size='3' color='#00000' style='padding: 3px;'><b>$aDescription</b></font><font size='3' color='#B3E001' style='padding: 3px;'><b> $aprice</b></font></a><br>";
echo "<font size='2' color='#000' style='padding: 3px;'>";
if($afeature1 != ""){
echo " $afeature1";
}
if($afeature2 != ""){
echo ", $afeature2";
}
if($afeature3 != ""){
echo ", $afeature3";
}
echo "</font>";
echo "</div>";
echo "</div>";
}
}

?>
<!--<iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=US&source=ac&ref=tf_til&ad_type=product_link&tracking_id=ignis0f-20&marketplace=amazon&region=US&placement=0984782850&asins=0984782850&linkId=b138b98610bdacb7821ee6984ca8ea33&show_border=false&link_opens_in_new_window=false&price_color=333333&title_color=0066C0&bg_color=FFFFFF">
    </iframe>-->
