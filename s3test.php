<?php
// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$localimage = '/home/ubuntu/snimbalk/switchonarex.png';
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => 'raw-smn',
    'Key' => basename($localimage),
    'SourceFile' => '/home/ubuntu/snimbalk/switchonarex.png']);
$url = $result['ObjectURL'];
echo $url;

//$result = $s3->listBuckets();

//foreach ($result['Buckets'] as $bucket) {
//    echo $bucket['Name'] . "\n";
//}

// Convert the result object to a PHP array
//$array = $result->toArray();
?>
<html>
<body>
<div align="center">
<img class="img-responsive" style='height: 100%; width: 100%; object-fit: contain' src="<?php echo $url; ?>" alt="Cover"
>
</div>
</body>
</html>
