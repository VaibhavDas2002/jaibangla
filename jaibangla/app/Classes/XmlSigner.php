<?php

//namespace Selective\XmlDSig;
namespace App\Classes;

use DOMDocument;
use DOMXPath;
use App\Classes\Exception\XmlSignerException;
use UnexpectedValueException;

/**
 * Sign XML Documents with Digital Signatures (XMLDSIG).
 */
final class XmlSigner
{
    //
    // Signature Algorithm Identifiers, RSA (PKCS#1 v1.5)
    // https://www.w3.org/TR/xmldsig-core/#sec-PKCS1
    //
    private const SIGNATURE_SHA1_URL = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
    private const SIGNATURE_SHA224_URL = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha224';
    private const SIGNATURE_SHA256_URL = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
    private const SIGNATURE_SHA384_URL = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384';
    private const SIGNATURE_SHA512_URL = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512';

    //
    // Digest Algorithm Identifiers
    // https://www.w3.org/TR/xmldsig-core/#sec-AlgID
    //
    private const DIGEST_SHA1_URL = 'http://www.w3.org/2000/09/xmldsig#sha1';
    private const DIGEST_SHA224_URL = 'http://www.w3.org/2001/04/xmldsig-more#sha224';
    private const DIGEST_SHA256_URL = 'http://www.w3.org/2001/04/xmlenc#sha256';
    private const DIGEST_SHA384_URL = 'http://www.w3.org/2001/04/xmldsig-more#sha384';
    private const DIGEST_SHA512_URL = 'http://www.w3.org/2001/04/xmlenc#sha512';

//     private const CERTIFICATE_509 = 
//     "MIIGPzCCBSegAwIBAgIIFfqoZzBlXLEwDQYJKoZIhvcNAQELBQAwgbQxCzAJBgNV
// BAYTAlVTMRAwDgYDVQQIEwdBcml6b25hMRMwEQYDVQQHEwpTY290dHNkYWxlMRow
// GAYDVQQKExFHb0RhZGR5LmNvbSwgSW5jLjEtMCsGA1UECxMkaHR0cDovL2NlcnRz
// LmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvMTMwMQYDVQQDEypHbyBEYWRkeSBTZWN1
// cmUgQ2VydGlmaWNhdGUgQXV0aG9yaXR5IC0gRzIwHhcNMjAwNTA0MDgxMjE4WhcN
// MjEwNTA0MDgxMjE4WjA+MSEwHwYDVQQLExhEb21haW4gQ29udHJvbCBWYWxpZGF0
// ZWQxGTAXBgNVBAMMECoucHJhY2hlc3Rhd2IuaW4wggEiMA0GCSqGSIb3DQEBAQUA
// A4IBDwAwggEKAoIBAQClXBwb2FGbN0GnpsXB6pbvplttWpAMzg7LoKnkHuydV9QK
// dYvJ1NKxbgFxkGEx9C2xNoQm8SCa14p5eozzmogDOzWHQ4mALG/D+HcCsh0YSkDD
// TSk0I3DngUaEFKb9D0R1eUXoiHUF6s0886iUExwoaPbD8wAXt9l0YXRnePdIAKzS
// zZ20c1WljsMbG1H2x1ej8xseO49QlJ43jVEvxQf69t8Jg9YVTPbOlKAGsG7QagUt
// C3FerFdAovvIGOVVsijD8S9AWurzTqwAPFq9AdhWE6YqbiUARux0iJiC9cO0M1T5
// pE0byfh13imNwRsak+O3SwL6vgMyGw+BWsGBqFYLAgMBAAGjggLIMIICxDAMBgNV
// HRMBAf8EAjAAMB0GA1UdJQQWMBQGCCsGAQUFBwMBBggrBgEFBQcDAjAOBgNVHQ8B
// Af8EBAMCBaAwOAYDVR0fBDEwLzAtoCugKYYnaHR0cDovL2NybC5nb2RhZGR5LmNv
// bS9nZGlnMnMxLTE5MjkuY3JsMF0GA1UdIARWMFQwSAYLYIZIAYb9bQEHFwEwOTA3
// BggrBgEFBQcCARYraHR0cDovL2NlcnRpZmljYXRlcy5nb2RhZGR5LmNvbS9yZXBv
// c2l0b3J5LzAIBgZngQwBAgEwdgYIKwYBBQUHAQEEajBoMCQGCCsGAQUFBzABhhho
// dHRwOi8vb2NzcC5nb2RhZGR5LmNvbS8wQAYIKwYBBQUHMAKGNGh0dHA6Ly9jZXJ0
// aWZpY2F0ZXMuZ29kYWRkeS5jb20vcmVwb3NpdG9yeS9nZGlnMi5jcnQwHwYDVR0j
// BBgwFoAUQMK9J47MNIMwojPX+2yz8LQsgM4wKwYDVR0RBCQwIoIQKi5wcmFjaGVz
// dGF3Yi5pboIOcHJhY2hlc3Rhd2IuaW4wHQYDVR0OBBYEFOZ2Of4xE5d4tVkkvxRn
// wOhOaqcSMIIBBQYKKwYBBAHWeQIEAgSB9gSB8wDxAHcApLkJkLQYWBSHuxOizGdw
// Cjw1mAT5G9+443fNDsgN3BAAAAFx3r40SAAABAMASDBGAiEAxsKI05/nJb4FjBFu
// KkeHl3VtwFBNXqiy1qGf/EUTtggCIQDSUnFqRCCNif7i7MX0p3zqv6NsS2WfeUqX
// Oh1k/WKG0QB2AESUZS6w7s6vxEAH2Kj+KMDa5oK+2MsxtT/TM5a1toGoAAABcd6+
// NnsAAAQDAEcwRQIhAIfO9/VkgP9wCILvW/DWg+bPfrwwAyOpN3nISxecnAl3AiB4
// OsmLH2D0G8QKnnzhEwG6ouwkKdeP5UHZhH16ufEFdTANBgkqhkiG9w0BAQsFAAOC
// AQEAd0qAb7cxcIcKeoIDF2FftBeASKVqRbbJ0rv5quXNRQT2wCLVRlhnfYQCVlTh
// aA1t8K57FY4fQ91saDInfYOsTSMVPuvVZWSQitSHM4sTqHyE+SzIMP1ZsVV7fv1z
// i57F5TodIyu+nLSX3YyA3AoBfBMgrP86k/byLoUffG5mX2NEOKIaSvlSELFgzQrs
// vC3TWc5Uq72NelqvULIRZS2E9tuhpvBSVCu1yGTpswfJLKnzQZGctx/R+dF12OeQ
// cSsPLYLrhG5XfrwkakkEJYzRPRchAdX/gFyuJdQg8Ghj0mXqACmIuwt/AbcxcLAv
// yMkqdjUXDXgjPF0tzXzkqkaDgw==";

private const CERTIFICATE_509 = "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";
    private $x509_certificate;

    /**
     * @var int
     */
    private $sslAlgorithm;

    /**
     * @var string
     */
    private $algorithmName;

    /**
     * @var string
     */
    private $signatureAlgorithmUrl;

    /**
     * @var string
     */
    private $digestAlgorithmUrl;

    /**
     * @var resource|false
     */
    private $privateKeyId;

    /**
     * @var resource|false
     */
    private $publicKeyId;

    /**
     * @var string
     */
    private $referenceUri = '';

    /**
     * @var string
     */
    private $modulus;

    /**
     * @var string
     */
    private $publicExponent;

    /**
     * @var XmlReader
     */
    private $xmlReader;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->xmlReader = new XmlReader();
    }

    /**
     * Read and load the pfx file.
     *
     * @param string $filename PFX filename
     * @param string $password PFX password
     *
     * @throws XmlSignerException
     *
     * @return bool Success
     */
    public function loadPfxFile(string $filename, string $password): bool
    {
        if (!file_exists($filename)) {
            throw new XmlSignerException(sprintf('File not found: %s', $filename));
        }

        $certStore = file_get_contents($filename);

        if (!$certStore) {
            throw new XmlSignerException(sprintf('File could not be read: %s', $filename));
        }

        $status = openssl_pkcs12_read($certStore, $certInfo, $password);

        if (!$status) {
            throw new XmlSignerException('Invalid PFX password');
        }

        // Read the private key
        $this->privateKeyId = openssl_pkey_get_private((string)$certInfo['pkey']);

        //Read the public key -- Gaurav
        //$this->publicKeyId = openssl_get_publickey($certInfo['cert']);

        //print_r(openssl_pkey_get_details($this->publicKeyId));
        //die();

        if (!$this->privateKeyId) {
            throw new XmlSignerException('Invalid private key');
        }

        $this->loadPrivateKeyDetails();

        return true;
    }

    /**
     * Read and load a private key file.
     *
     * @param string $filename The PEM filename
     * @param string $password The PEM password
     *
     * @throws XmlSignerException
     *
     * @return bool Success
     */
    public function loadPrivateKeyFile(string $filename, string $password): bool
    {
        if (!file_exists($filename)) {
            throw new XmlSignerException(sprintf('File not found: %s', $filename));
        }

        $certStore = file_get_contents($filename);

        if (!$certStore) {
            throw new XmlSignerException(sprintf('File could not be read: %s', $filename));
        }

        // Read the private key
        $this->privateKeyId = openssl_pkey_get_private($certStore, $password);

        if (!$this->privateKeyId) {
            throw new XmlSignerException('Invalid password or private key');
        }

        $this->loadPrivateKeyDetails();

        return true;
    }

    /**
     * Load private key details.
     *
     * @throws UnexpectedValueException
     *
     * @return void
     */
    private function loadPrivateKeyDetails(): void
    {
        if (!$this->privateKeyId) {
            throw new UnexpectedValueException('Private key is not defined');
        }

        $details = openssl_pkey_get_details($this->privateKeyId);

        if ($details === false) {
            throw new UnexpectedValueException('Invalid private key');
        }

        $key = $this->getPrivateKeyDetailKey($details['type']);
        $this->modulus = base64_encode($details[$key]['n']);
        $this->publicExponent = base64_encode($details[$key]['e']);
    }

    /**
     * Get private key details key type.
     *
     * @param int $type The type
     *
     * @return string The array key
     */
    private function getPrivateKeyDetailKey(int $type): string
    {
        $key = '';
        $key = $type === OPENSSL_KEYTYPE_RSA ? 'rsa' : $key;
        $key = $type === OPENSSL_KEYTYPE_DSA ? 'dsa' : $key;
        $key = $type === OPENSSL_KEYTYPE_DH ? 'dh' : $key;
        $key = $type === OPENSSL_KEYTYPE_EC ? 'ec' : $key;

        return $key;
    }

    /**
     * Sign an XML file and save the signature in a new file.
     * This method does not save the public key within the XML file.
     *
     * @param string $filename Input file
     * @param string $outputFilename Output file
     * @param string $algorithm For example: sha1, sha224, sha256, sha384, sha512
     *
     * @throws XmlSignerException
     *
     * @return bool Success
     */
    public function signXmlFile(string $filename, string $outputFilename, string $algorithm): bool
    {
        if (!file_exists($filename)) {
            throw new XmlSignerException(sprintf('File not found: %s', $filename));
        }

        if (!$this->privateKeyId) {
            throw new XmlSignerException('No private key provided');
        }

        $this->setAlgorithm($algorithm);

        // Read the xml file content
        $xml = new DOMDocument();

        // Whitespaces must be preserved
        $xml->preserveWhiteSpace = true;

        $xml->formatOutput = false;

        $xml->load($filename);

        // Canonicalize the content, exclusive and without comments
        if (!$xml->documentElement) {
            throw new UnexpectedValueException('Undefined document element');
        }

        $canonicalData = $xml->documentElement->C14N(true, false);

        // Calculate and encode digest value
        $digestValue = openssl_digest($canonicalData, $this->algorithmName, true);
        if ($digestValue === false) {
            throw new UnexpectedValueException('Invalid digest value');
        }

        $digestValue = base64_encode($digestValue);
        $this->appendSignature($xml, $digestValue);

        file_put_contents($outputFilename, $xml->saveXML());

        return true;
    }

    /**
     * Set reference URI.
     *
     * @param string $referenceUri The reference URI
     *
     * @return void
     */
    public function setReferenceUri(string $referenceUri)
    {
        $this->referenceUri = $referenceUri;
    }

    /**
     * Create the XML representation of the signature.
     *
     * @param DOMDocument $xml The xml document
     * @param string $digestValue The digest value
     *
     * @throws UnexpectedValueException
     *
     * @return void The DOM document
     */
    private function appendSignature(DOMDocument $xml, string $digestValue)
    {
        $signatureElement = $xml->createElement('Signature');
        $signatureElement->setAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');

        // Append the element to the XML document.
        // We insert the new element as root (child of the document)

        if (!$xml->documentElement) {
            throw new UnexpectedValueException('Undefined document element');
        }

        $xml->documentElement->appendChild($signatureElement);

        $signedInfoElement = $xml->createElement('SignedInfo');
        $signatureElement->appendChild($signedInfoElement);

        $canonicalizationMethodElement = $xml->createElement('CanonicalizationMethod');
        $canonicalizationMethodElement->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfoElement->appendChild($canonicalizationMethodElement);

        $signatureMethodElement = $xml->createElement('SignatureMethod');
        $signatureMethodElement->setAttribute('Algorithm', $this->signatureAlgorithmUrl);
        $signedInfoElement->appendChild($signatureMethodElement);

        $referenceElement = $xml->createElement('Reference');
        $referenceElement->setAttribute('URI', $this->referenceUri);
        $signedInfoElement->appendChild($referenceElement);

        $transformsElement = $xml->createElement('Transforms');
        $referenceElement->appendChild($transformsElement);

        // Enveloped: the <Signature> node is inside the XML we want to sign
        $transformElement = $xml->createElement('Transform');
        $transformElement->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transformsElement->appendChild($transformElement);

        $digestMethodElement = $xml->createElement('DigestMethod');
        $digestMethodElement->setAttribute('Algorithm', $this->digestAlgorithmUrl);
        $referenceElement->appendChild($digestMethodElement);

        $digestValueElement = $xml->createElement('DigestValue', $digestValue);
        $referenceElement->appendChild($digestValueElement);

        $signatureValueElement = $xml->createElement('SignatureValue', '');
        $signatureElement->appendChild($signatureValueElement);

        $keyInfoElement = $xml->createElement('KeyInfo');
        $signatureElement->appendChild($keyInfoElement);

        //$keyValueElement = $xml->createElement('KeyValue');
        //$keyInfoElement->appendChild($keyValueElement);

        $x509DataElement = $xml->createElement('X509Data');
        $keyInfoElement->appendChild($x509DataElement);

        $x509CertificateElement = $xml->createElement('X509Certificate',$this->x509_certificate); 
        $x509DataElement->appendChild($x509CertificateElement);

        //$rsaKeyValueElement = $xml->createElement('RSAKeyValue');
        //$keyValueElement->appendChild($rsaKeyValueElement);

        //$modulusElement = $xml->createElement('Modulus', $this->modulus);
        //$rsaKeyValueElement->appendChild($modulusElement);

        //$exponentElement = $xml->createElement('Exponent', $this->publicExponent);
        //$rsaKeyValueElement->appendChild($exponentElement);

        // http://www.soapclient.com/XMLCanon.html
        $c14nSignedInfo = $signedInfoElement->C14N(true, false);

        // Calculate and encode digest value
        if (!$this->privateKeyId) {
            throw new UnexpectedValueException('Undefined private key');
        }

        $status = openssl_sign($c14nSignedInfo, $signatureValue, $this->privateKeyId, $this->sslAlgorithm);

        if (!$status) {
            throw new XmlSignerException('Computing of the signature failed');
        }

        $xpath = new DOMXpath($xml);
        $signatureValueElement = $this->xmlReader->queryDomNode($xpath, '//SignatureValue', $signatureElement);
        $signatureValueElement->nodeValue = base64_encode($signatureValue);
    }

    /**
     * Set signature and digest algorithm.
     *
     * @param string $algorithm For example: sha1, sha224, sha256, sha384, sha512
     */
    private function setAlgorithm(string $algorithm): void
    {
        switch ($algorithm) {
            case 'sha1':
                $this->signatureAlgorithmUrl = self::SIGNATURE_SHA1_URL;
                $this->digestAlgorithmUrl = self::DIGEST_SHA1_URL;
                $this->sslAlgorithm = OPENSSL_ALGO_SHA1;
                $this->x509_certificate = self::CERTIFICATE_509;
                break;
            case 'sha224':
                $this->signatureAlgorithmUrl = self::SIGNATURE_SHA224_URL;
                $this->digestAlgorithmUrl = self::DIGEST_SHA224_URL;
                $this->sslAlgorithm = OPENSSL_ALGO_SHA224;
                break;
            case 'sha256':
                $this->signatureAlgorithmUrl = self::SIGNATURE_SHA256_URL;
                $this->digestAlgorithmUrl = self::DIGEST_SHA256_URL;
                $this->sslAlgorithm = OPENSSL_ALGO_SHA256;
                $this->x509_certificate = self::CERTIFICATE_509;
                break;
            case 'sha384':
                $this->signatureAlgorithmUrl = self::SIGNATURE_SHA384_URL;
                $this->digestAlgorithmUrl = self::DIGEST_SHA384_URL;
                $this->sslAlgorithm = OPENSSL_ALGO_SHA384;
                break;
            case 'sha512':
                $this->signatureAlgorithmUrl = self::SIGNATURE_SHA512_URL;
                $this->digestAlgorithmUrl = self::DIGEST_SHA512_URL;
                $this->sslAlgorithm = OPENSSL_ALGO_SHA512;
                break;
            default:
                throw new XmlSignerException("Cannot validate digest: Unsupported algorithm <$algorithm>");
        }

        $this->algorithmName = $algorithm;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Free the key from memory
        if ($this->privateKeyId) {
            openssl_free_key($this->privateKeyId);
        }
    }
}
