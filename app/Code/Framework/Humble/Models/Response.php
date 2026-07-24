<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
/**
 *
 * PSR-7 Response
 *
 * An implementation of the Response object, as defined by PSR-7
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Response extends Model implements \Psr\Http\Message\ResponseInterface
{

    use \Code\Framework\Humble\Traits\Base;
    
    private $headers     = [];
    private $body        = null;
    private $status_code = 0;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->body = Humble::model('humble/stream');
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function className() {
        return __CLASS__;
    }

    public function getProtocolVersion()  : string {
        return '1.2';
    }
    public function withProtocolVersion($version): MessageInterface {
        
    }
    public function getHeaders() : array {
        return $this->headers;
    }
    public function hasHeader($name=null) : bool {
        return isset($this->headers[$name]);
    }
    public function getHeader($name=null) : array {
        $result = [];
        if (isset($this->headers[$name])) {
            $result[] = $this->headers[$name];
        }
        return $result;
    }
    public function getHeaderLine($name=null): string {
        return '';
    }
    public function withHeader($name=null, $value=null): MessageInterface {
        
    }
    public function withAddedHeader($name=null, $value=null): MessageInterface {
        
    }
    public function withoutHeader($name=null): MessageInterface {
        
    }
    public function getBody(): StreamInterface {
        return $this->body;
    }
    public function withBody(StreamInterface $body): MessageInterface {
        
    }
    public function getReasonPhrase() : string {
        return 'wtf?';
    }
    public function withStatus(int $code, string $reasonPhrase = ''): \Psr\Http\Message\ResponseInterface {
        
    }
    public function getStatusCode() : int {
        return $this->status_code;
    }
}