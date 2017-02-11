<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 12:55 AM
 */

namespace Sm\Response;


use Sm\Resolvable\Resolvable;
use Sm\Type\DateTime_;
use Sm\Util;

class Response extends Resolvable {
    const TYPE_TEXT_HTML = 'text/html';
    const TYPE_JSON      = 'application/json';
    #
    #
    #
    /** @var DateTime_ $creation_dt The Date_ that this was created */
    public $creation_dt;
    /** @var DateTime_ $access_dt The Date_ that this was last accessed */
    public $access_dt;
    /** @var string $content_type The Content Type of the Response that we are going to send back */
    public $content_type = Response::TYPE_TEXT_HTML;
    
    /**
     * Response constructor.
     *
     * @param null $subject
     */
    public function __construct($subject) {
        # Update the creation/access dates
        $this->creation_dt = DateTime_::init();
        $this->access_dt   = DateTime_::init();
        parent::__construct($subject);
    }
    /**
     * @return string
     */
    public function resolve() {
        switch ($this->content_type) {
            default:
            case Response::TYPE_TEXT_HTML:
                return Util::canBeString($this->subject) ? "$this->subject" : json_encode($this->subject);
                break;
            case Response::TYPE_JSON:
                return json_encode($this);
                break;
        }
    }
    public function __toString() {
        $result = $this->resolve();
        return Util::canBeString($result) ? $result : json_encode($result);
    }
    /**
     * Get the time that this Response was initially created
     *
     * @return DateTime_
     */
    public function getCreationDt(): DateTime_ {
        return $this->creation_dt;
    }
    /**
     * Get the type of content that this Response represents
     *
     * @return string
     */
    public function getContentType(): string {
        return $this->content_type;
    }
    /**
     * Set the Content Type that the Response is supposed to resolve to;
     *
     * @param string $content_type
     *
     * @return Response
     */
    public function setContentType(string $content_type): Response {
        $this->content_type = $content_type;
        return $this;
    }
}