<?php
namespace Visy\RestApi\Model;

use Visy\RestApi\Api\VisyIntegrationInterface;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class VisyIntegration implements VisyIntegrationInterface {
    private $_scopeConfig;
    private $_directoryList;

    private $s3Bucket;
    private $s3Region;
    private $s3Key;
    private $s3Secret;


    /**
	 * Constructor
	 * @return null
	 */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_directoryList = $directoryList;

        $this->s3Bucket = $this->_scopeConfig->getValue('visy_integration/aws/s3_bucket', \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
        $this->s3Region = $this->_scopeConfig->getValue('visy_integration/aws/s3_region', \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
        $this->s3Key = $this->_scopeConfig->getValue('visy_integration/aws/s3_key', \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
        $this->s3Secret = $this->_scopeConfig->getValue('visy_integration/aws/s3_secret', \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
    }

    /**
	 * Upload .csv customer product/pricing.
	 * @param file $file
	 * @return null
	 */
    public function postProductPricing() {
        if(empty($this->s3Bucket) || empty($this->s3Region) || empty($this->s3Key) || empty($this->s3Secret)) {
            $message = "[FAILED] Empty AWS S3 configuration.";
            $this->logMessage("[FAILED] {$message}");

            return json_decode(json_encode([
                "messages" => [
                    'message' => $message,
                    'error' => ["code" => 500]
                ]
            ]), true);
        }

        if(!isset($_FILES['file'])) {
            $message = "Required 'file' field is missing.";
            $this->logMessage("[FAILED] {$message}");

            return json_decode(json_encode([
                "messages" => [
                    'message' => $message,
                    'error' => ["code" => 500]
                ]
            ]), true);
        }
        if($_FILES['file']['type'] != 'text/csv') {
            $message = "Invalid 'text/csv' file type.";
            $this->logMessage("[FAILED] {$message}");
            return json_decode(json_encode([
                "messages" => [
                    'message' => $message,
                    'error' => ["code" => 500],
                ]
            ]), true);
        }

        $targetFilename = "pricing_".date("YmdHis").".csv";
        $filePath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];

        if(!move_uploaded_file($_FILES["file"]["tmp_name"], $this->_directoryList->getPath('tmp') . "/$targetFilename")) {
            $message = "Unable to upload '{$filename}' source file.";
            $this->logMessage("[FAILED] {$message}");
            return json_decode(json_encode([
                "messages" => [
                    'message' => $message,
                    'error' => ["code" => 500],
                ]
            ]), true);
        }

        if($this->uploadToS3($targetFilename)) {
            $message = "Successfully upload '{$targetFilename}' file to S3 Bucket (Source file: '{$fileName}').";
            $this->logMessage("[SENT] {$message}");
            return json_decode(json_encode([
                'messages' => [
                    'message' => $message,
                ]
            ]), true);
        }

        $response = [
            'hello' => 'world'
        ];
        return json_encode($response);
    }

    private function uploadToS3($filename) {
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->s3Region,
            'credentials' => [
                'key' => $this->s3Key,
                'secret' => $this->s3Secret
            ]
        ]);

        try {
            // Upload data.
            $result = $s3->putObject([
                'Bucket' => $this->s3Bucket,
                'Key' => "pricing/{$filename}",
                'SourceFile' => $this->_directoryList->getPath('tmp') . "/$filename",
            ]);
            if($result) {
                return true;
            }

        } catch (S3Exception $e) {
            $this->logMessage("[FAILED] {$e->getMessage()}");
            return false;
        }
    }

    private function logMessage($message) {
        $datetime = date("Y-m-d H:i:sA T");
        file_put_contents($this->_directoryList->getPath('log') . '/visy_integration_product_pricing.log', "[$datetime] $message".PHP_EOL, FILE_APPEND);
    }
}
